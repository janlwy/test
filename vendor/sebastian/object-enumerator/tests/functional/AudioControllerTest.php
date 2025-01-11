<?php
use PHPUnit\Framework\TestCase;
use App\Session\SessionManager;
use App\Controllers\AudioController;

class AudioControllerTest extends TestCase
{
    private $controller;
    private $session;
    
    protected function setUp(): void
    {
        $this->session = $this->createMock(SessionManager::class);
        $this->controller = new AudioController();
        
        // Simuler un utilisateur connecté
        $this->session->method('isAuthenticated')
                     ->willReturn(true);
        $this->session->method('get')
                     ->willReturnMap([
                         ['user_id', 1],
                         ['pseudo', 'testuser']
                     ]);
    }

    public function testListAudio()
    {
        // Simuler des données audio
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

        // Capturer la sortie HTML
        ob_start();
        $this->controller->listeAudio();
        $output = ob_get_clean();

        // Vérifier que la sortie contient les éléments attendus
        $this->assertStringContainsString('Test Song 1', $output);
        $this->assertStringContainsString('Artist 1', $output);
        $this->assertStringContainsString('Test Song 2', $output);
        $this->assertStringContainsString('Artist 2', $output);
    }

    public function testCreateAudio()
    {
        $_FILES['image'] = [
            'name' => 'test.jpg',
            'type' => 'image/jpeg',
            'tmp_name' => __DIR__ . '/fixtures/test.jpg',
            'error' => 0,
            'size' => 1024
        ];

        $_FILES['path'] = [
            'name' => 'test.mp3',
            'type' => 'audio/mpeg',
            'tmp_name' => __DIR__ . '/fixtures/test.mp3',
            'error' => 0,
            'size' => 1024 * 1024
        ];

        $_POST = [
            'title' => 'New Test Song',
            'artiste' => 'New Artist',
            'csrf_token' => 'valid_token'
        ];

        $this->session->expects($this->once())
                     ->method('validateToken')
                     ->with('valid_token')
                     ->willReturn(true);

        // Simuler la redirection
        $this->expectOutputRegex('/Location: \?url=audio\/list/');
        
        $this->controller->create();

        // Vérifier que les fichiers ont été uploadés
        $this->assertFileExists('Ressources/audio/test.mp3');
        $this->assertFileExists('Ressources/images/pochettes/test.jpg');
    }

    public function testUpdateAudio()
    {
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

        $this->controller->audioRepository = $mockRepository;

        // Simuler la redirection
        $this->expectOutputRegex('/Location: \?url=audio\/list/');
        
        $this->controller->update(1);

        // Vérifier que l'audio a été mis à jour
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

        // Simuler la redirection
        $this->expectOutputRegex('/Location: \?url=audio\/list/');
        
        $this->controller->delete(1);
    }
}
