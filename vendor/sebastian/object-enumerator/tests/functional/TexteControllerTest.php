<?php
use PHPUnit\Framework\TestCase;
use App\Controllers\TexteController;
use App\Session\SessionManager;

class TexteControllerTest extends TestCase
{
    private $controller;
    private $session;
    
    protected function setUp(): void
    {
        $this->session = $this->createMock(SessionManager::class);
        $this->controller = new TexteController();
        
        // Simuler un utilisateur connecté
        $this->session->method('isAuthenticated')
                     ->willReturn(true);
        $this->session->method('get')
                     ->willReturnMap([
                         ['user_id', 1],
                         ['pseudo', 'testuser']
                     ]);
    }

    public function testIndex()
    {
        // Simuler la vérification CSRF
        $this->session->expects($this->once())
                     ->method('validateToken')
                     ->willReturn(true);

        // Capturer la sortie HTML
        ob_start();
        $this->controller->index();
        $output = ob_get_clean();

        // Vérifier que la sortie contient les éléments attendus
        $this->assertStringContainsString('searchBox', $output);
        $this->assertStringContainsString('searchInput', $output);
        $this->assertStringContainsString('material-icons', $output);
    }

    public function testList()
    {
        // Simuler des données texte
        $textData = [
            [
                'id' => 1,
                'title' => 'Test Note 1',
                'content' => 'Content 1',
                'user_id' => 1
            ],
            [
                'id' => 2, 
                'title' => 'Test Note 2',
                'content' => 'Content 2',
                'user_id' => 1
            ]
        ];

        $mockRepository = $this->createMock(TexteRepository::class);
        $mockRepository->expects($this->once())
                      ->method('findAllByUser')
                      ->with(1)
                      ->willReturn($textData);

        $this->controller->repository = $mockRepository;

        // Capturer la sortie HTML
        ob_start();
        $this->controller->list();
        $output = ob_get_clean();

        // Vérifier que la sortie contient les éléments attendus
        $this->assertStringContainsString('Test Note 1', $output);
        $this->assertStringContainsString('Content 1', $output);
        $this->assertStringContainsString('Test Note 2', $output);
        $this->assertStringContainsString('Content 2', $output);
    }

    public function testCreate()
    {
        $_POST = [
            'title' => 'New Test Note',
            'content' => 'New Content',
            'csrf_token' => 'valid_token'
        ];

        $this->session->expects($this->once())
                     ->method('validateToken')
                     ->with('valid_token')
                     ->willReturn(true);

        // Simuler la redirection
        $this->expectOutputRegex('/Location: \?url=texte\/list/');
        
        $this->controller->create();

        // Vérifier que la note a été créée
        $mockRepository = $this->createMock(TexteRepository::class);
        $mockRepository->expects($this->once())
                      ->method('findById')
                      ->with(1)
                      ->willReturn([
                          'id' => 1,
                          'title' => 'New Test Note',
                          'content' => 'New Content',
                          'user_id' => 1
                      ]);
    }

    public function testUpdate()
    {
        $_POST = [
            'title' => 'Updated Note',
            'content' => 'Updated Content',
            'csrf_token' => 'valid_token'
        ];

        $existingNote = [
            'id' => 1,
            'title' => 'Old Note',
            'content' => 'Old Content',
            'user_id' => 1
        ];

        $mockRepository = $this->createMock(TexteRepository::class);
        $mockRepository->expects($this->once())
                      ->method('findById')
                      ->with(1)
                      ->willReturn($existingNote);

        $this->controller->repository = $mockRepository;

        // Simuler la redirection
        $this->expectOutputRegex('/Location: \?url=texte\/list/');
        
        $this->controller->update(1);

        // Vérifier que la note a été mise à jour
        $this->assertEquals('Updated Note', $existingNote['title']);
        $this->assertEquals('Updated Content', $existingNote['content']);
    }

    public function testDelete()
    {
        $existingNote = [
            'id' => 1,
            'title' => 'Note to Delete',
            'content' => 'Content',
            'user_id' => 1
        ];

        $mockRepository = $this->createMock(TexteRepository::class);
        $mockRepository->expects($this->once())
                      ->method('findById')
                      ->with(1)
                      ->willReturn($existingNote);

        $mockRepository->expects($this->once())
                      ->method('delete')
                      ->with(1);

        $this->controller->repository = $mockRepository;

        // Simuler la redirection
        $this->expectOutputRegex('/Location: \?url=texte\/list/');
        
        $this->controller->delete(1);
    }
}
