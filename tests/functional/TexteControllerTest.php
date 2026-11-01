<?php

use PHPUnit\Framework\TestCase;
use App\Controllers\TexteController;
use App\Session\SessionManager;
use App\Models\Texte;
use App\Models\TexteRepository;

class TexteControllerTest extends TestCase
{
    private $controller;
    private $session;

    protected function setUp(): void
    {
        $this->session = $this->createMock(SessionManager::class);
        $this->controller = new TexteController();
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

    public function testIndex()
    {
        $this->expectOutputRegex('/Location: \?url=texte\/list/');
        $this->controller->index();
    }

    public function testList()
    {
        $textData = [
            new Texte([
                'id' => 1,
                'title' => 'Test Note 1',
                'content' => 'Content 1',
                'user_id' => 1
            ]),
            new Texte([
                'id' => 2,
                'title' => 'Test Note 2',
                'content' => 'Content 2',
                'user_id' => 1
            ])
        ];

        $mockRepository = $this->createMock(TexteRepository::class);
        $mockRepository->expects($this->once())
            ->method('findAllByUser')
            ->with(1)
            ->willReturn($textData);

        $this->controller->repository = $mockRepository;

        ob_start();
        $this->controller->list();
        $output = ob_get_clean();

        $this->assertStringContainsString('Test Note 1', $output);
        $this->assertStringContainsString('Content 1', $output);
        $this->assertStringContainsString('Test Note 2', $output);
        $this->assertStringContainsString('Content 2', $output);
    }

    public function testCreate()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $_POST = [
            'title' => 'New Test Note',
            'content' => 'New Content',
            'csrf_token' => 'valid_token'
        ];

        $mockRepository = $this->createMock(TexteRepository::class);
        $mockRepository->expects($this->once())
            ->method('save');
        $this->controller->repository = $mockRepository;

        $this->expectOutputRegex('/Location: \?url=texte\/list/');

        $this->controller->create();
    }

    public function testUpdate()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $_POST = [
            'title' => 'Updated Note',
            'content' => 'Updated Content',
            'csrf_token' => 'valid_token'
        ];

        $existingNote = new Texte([
            'id' => 1,
            'title' => 'Old Note',
            'content' => 'Old Content',
            'user_id' => 1
        ]);

        $mockRepository = $this->createMock(TexteRepository::class);
        $mockRepository->expects($this->once())
            ->method('findById')
            ->with(1)
            ->willReturn($existingNote);
        $mockRepository->expects($this->once())
            ->method('save');

        $this->controller->repository = $mockRepository;

        $this->expectOutputRegex('/Location: \?url=texte\/list/');

        $this->controller->update(1);

        $this->assertEquals('Updated Note', $existingNote->getTitle());
        $this->assertEquals('Updated Content', $existingNote->getContent());
    }

    public function testDelete()
    {
        $existingNote = new Texte([
            'id' => 1,
            'title' => 'Note to Delete',
            'content' => 'Content',
            'user_id' => 1
        ]);

        $mockRepository = $this->createMock(TexteRepository::class);
        $mockRepository->expects($this->once())
            ->method('findById')
            ->with(1)
            ->willReturn($existingNote);

        $mockRepository->expects($this->once())
            ->method('delete')
            ->with(1);

        $this->controller->repository = $mockRepository;

        $this->expectOutputRegex('/Location: \?url=texte\/list/');

        $this->controller->delete(1);
    }
}
