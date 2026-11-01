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

    public function testGetFullPaths()
    {
        $this->assertEquals('Ressources/audio/test.mp3', $this->audio->getFullPath());
        // Si test.jpg n'existe pas sur le disque, getFullImagePath() renvoie le fallback startPic.png
        $this->assertEquals('Ressources/images/startPic.png', $this->audio->getFullImagePath());
    }
}
