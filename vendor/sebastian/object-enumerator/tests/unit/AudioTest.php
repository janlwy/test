<?php
use PHPUnit\Framework\TestCase;
use App\Models\Audio;

class AudioTest extends TestCase
{
    private $audio;
    
    protected function setUp(): void
    {
        $this->audio = new Audio([
            'id' => 1,
            'title' => 'Test Song',
            'artist' => 'Test Artist',
            'image' => 'test.jpg',
            'path' => 'test.mp3',
            'user_id' => 1
        ]);
    }

    public function testGettersAndSetters()
    {
        $this->assertEquals(1, $this->audio->getId());
        $this->assertEquals('Test Song', $this->audio->getTitle());
        $this->assertEquals('Test Artist', $this->audio->getArtist());
        $this->assertEquals('test.jpg', $this->audio->getImage());
        $this->assertEquals('test.mp3', $this->audio->getPath());
        $this->assertEquals(1, $this->audio->getUserId());
    }

    public function testValidateAudioFile()
    {
        $validFile = [
            'tmp_name' => __DIR__ . '/fixtures/valid.mp3',
            'type' => 'audio/mpeg',
            'size' => 1024 * 1024 // 1MB
        ];

        $errors = $this->audio->validateAudioFile($validFile);
        $this->assertEmpty($errors);

        $invalidFile = [
            'tmp_name' => __DIR__ . '/fixtures/invalid.txt',
            'type' => 'text/plain',
            'size' => 51 * 1024 * 1024 // 51MB
        ];

        $errors = $this->audio->validateAudioFile($invalidFile);
        $this->assertNotEmpty($errors);
    }

    public function testGetFullPaths()
    {
        $this->assertEquals('Ressources/audio/test.mp3', $this->audio->getFullPath());
        $this->assertEquals('Ressources/images/pochettes/test.jpg', $this->audio->getFullImagePath());
    }

    public function testSave()
    {
        $mockManager = $this->createMock(Manager::class);
        $mockManager->expects($this->once())
                   ->method('insertTable')
                   ->willReturn(['id' => 2]);

        $result = $this->audio->save();
        $this->assertTrue($result);
        $this->assertEquals(2, $this->audio->getId());
    }

    public function testDelete()
    {
        // Créer un fichier temporaire pour le test
        $audioPath = sys_get_temp_dir() . '/test.mp3';
        $imagePath = sys_get_temp_dir() . '/test.jpg';
        file_put_contents($audioPath, 'test');
        file_put_contents($imagePath, 'test');

        $audio = new Audio([
            'id' => 1,
            'path' => basename($audioPath),
            'image' => basename($imagePath)
        ]);

        $mockManager = $this->createMock(Manager::class);
        $mockManager->expects($this->once())
                   ->method('deleteTable')
                   ->willReturn(true);

        $result = $audio->delete();
        $this->assertTrue($result);
        $this->assertFileDoesNotExist($audioPath);
        $this->assertFileDoesNotExist($imagePath);
    }
}
