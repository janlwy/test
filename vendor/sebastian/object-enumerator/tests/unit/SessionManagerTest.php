<?php
use PHPUnit\Framework\TestCase;
use App\Session\SessionManager;

class SessionManagerTest extends TestCase
{
    private $sessionManager;
    
    protected function setUp(): void
    {
        $this->sessionManager = SessionManager::getInstance();
        $_SESSION = [];
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
    }

    public function testGetInstance()
    {
        $instance1 = SessionManager::getInstance();
        $instance2 = SessionManager::getInstance();
        $this->assertSame($instance1, $instance2);
    }

    public function testSetAndGet()
    {
        $this->sessionManager->set('test_key', 'test_value');
        $this->assertEquals('test_value', $this->sessionManager->get('test_key'));
        $this->assertEquals('default', $this->sessionManager->get('non_existent', 'default'));
    }

    public function testRemove()
    {
        $this->sessionManager->set('test_key', 'test_value');
        $this->sessionManager->remove('test_key');
        $this->assertNull($this->sessionManager->get('test_key'));
    }

    public function testHas()
    {
        $this->sessionManager->set('test_key', 'test_value');
        $this->assertTrue($this->sessionManager->has('test_key'));
        $this->assertFalse($this->sessionManager->has('non_existent'));
    }

    public function testRegenerateAndValidateToken()
    {
        $token = $this->sessionManager->regenerateToken();
        $this->assertTrue($this->sessionManager->validateToken($token));
        $this->assertFalse($this->sessionManager->validateToken('invalid_token'));
    }

    public function testIsAuthenticated()
    {
        $this->assertFalse($this->sessionManager->isAuthenticated());
        
        $this->sessionManager->setAuthenticated('test_user', 1);
        $this->assertTrue($this->sessionManager->isAuthenticated());
    }

    public function testValidateFileUpload()
    {
        $file = [
            'tmp_name' => 'test.txt',
            'error' => UPLOAD_ERR_OK,
            'size' => 1024
        ];

        $allowedTypes = ['text/plain'];
        $maxSize = 2048;

        $errors = $this->sessionManager->validateFileUpload($file, $allowedTypes, $maxSize);
        $this->assertIsArray($errors);
    }
}
