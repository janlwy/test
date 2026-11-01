<?php

use PHPUnit\Framework\TestCase;
use App\Session\SessionManager;
use App\Controllers\AudioController;
use App\Models\Audio;
use App\Models\AudioRepository;

class AudioControllerTest extends TestCase
{
    private $controller;
    private $session;

    protected function setUp(): void
    {
        $this->session = $this->createMock(SessionManager::class);
        $this->controller = new AudioController();
        $this->controller->session = $this->session;

        // Simuler un utilisateur connecté
        $this->session->method('isAuthenticated')
            ->willReturn(true);
        $this->session->method('get')
            ->willReturnCallback(function ($key, $default = null) {
                $map = [
                    'user_id' => 1,
                    'pseudo' => 'testuser',
                    'csrf_token' => 'valid_token'
                ];
                return $map[$key] ?? $default;
            });
        $this->session->method('validateToken')
            ->willReturn(true);
    }

    public function testListAudio()
    {
        $audioData = [
            new Audio([
                'id' => 1,
                'title' => 'Test Song 1',
                'artist' => 'Artist 1',
                'image' => 'image1.jpg',
                'path' => 'song1.mp3',
                'user_id' => 1
            ]),
            new Audio([
                'id' => 2,
                'title' => 'Test Song 2',
                'artist' => 'Artist 2',
                'image' => 'image2.jpg',
                'path' => 'song2.mp3',
                'user_id' => 1
            ])
        ];

        $mockRepository = $this->createMock(AudioRepository::class);
        $mockRepository->expects($this->once())
            ->method('findAllByUser')
            ->with(1)
            ->willReturn($audioData);

        $this->controller->audioRepository = $mockRepository;

        ob_start();
        $this->controller->listeAudio();
        $output = ob_get_clean();

        $this->assertStringContainsString('Test Song 1', $output);
        $this->assertStringContainsString('Artist 1', $output);
        $this->assertStringContainsString('Test Song 2', $output);
        $this->assertStringContainsString('Artist 2', $output);
    }

    public function testCreateAudio()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $_POST = [
            'title' => 'New Test Song',
            'artiste' => 'New Artist',
            'csrf_token' => 'valid_token'
        ];

        $mockRepository = $this->createMock(AudioRepository::class);
        $this->controller->audioRepository = $mockRepository;

        $this->expectOutputRegex('/Location: \?url=audio\/list/');

        $this->controller->create();
    }

    public function testUpdateAudio()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_FILES = [];

        $_POST = [
            'title' => 'Updated Song',
            'artiste' => 'Updated Artist',
            'csrf_token' => 'valid_token'
        ];

        $existingAudio = new Audio([
            'id' => 1,
            'title' => 'Old Song',
            'artist' => 'Old Artist',
            'image' => 'old.jpg',
            'path' => 'old.mp3',
            'user_id' => 1
        ]);

        $mockRepository = $this->createMock(AudioRepository::class);
        $mockRepository->expects($this->once())
            ->method('findById')
            ->with(1)
            ->willReturn($existingAudio);
        $mockRepository->expects($this->once())
            ->method('save');

        $this->controller->audioRepository = $mockRepository;

        $this->expectOutputRegex('/Location: \?url=audio\/list/');

        $this->controller->update(1);

        $this->assertEquals('Updated Song', $existingAudio->getTitle());
        $this->assertEquals('Updated Artist', $existingAudio->getArtist());
    }

    public function testDeleteAudio()
    {
        $existingAudio = new Audio([
            'id' => 1,
            'title' => 'Song to Delete',
            'artist' => 'Artist',
            'image' => 'delete.jpg',
            'path' => 'delete.mp3',
            'user_id' => 1
        ]);

        $mockRepository = $this->createMock(AudioRepository::class);
        $mockRepository->expects($this->once())
            ->method('findById')
            ->with(1)
            ->willReturn($existingAudio);

        $mockRepository->expects($this->once())
            ->method('delete')
            ->with(1);

        $this->controller->audioRepository = $mockRepository;

        $this->expectOutputRegex('/Location: \?url=audio\/list/');

        $this->controller->delete(1);
    }
}
