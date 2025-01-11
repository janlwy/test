<?php
use PHPUnit\Framework\TestCase;

class TexteTest extends TestCase
{
    private $texte;
    
    protected function setUp(): void
    {
        $this->texte = new Texte([
            'id' => 1,
            'title' => 'Test Title',
            'content' => 'Test Content',
            'user_id' => 1
        ]);
    }

    public function testGettersAndSetters()
    {
        $this->assertEquals(1, $this->texte->getId());
        $this->assertEquals('Test Title', $this->texte->getTitle());
        $this->assertEquals('Test Content', $this->texte->getContent());
        $this->assertEquals(1, $this->texte->getUserId());
    }

    public function testValidation()
    {
        // Test avec des données valides
        $this->texte->setTitle('Valid Title');
        $this->texte->setContent('Valid Content');
        $errors = $this->texte->validate();
        $this->assertEmpty($errors);

        // Test avec titre vide
        $this->texte->setTitle('');
        $errors = $this->texte->validate();
        $this->assertContains('Le titre est requis', $errors);

        // Test avec contenu vide
        $this->texte->setTitle('Title');
        $this->texte->setContent('');
        $errors = $this->texte->validate();
        $this->assertContains('Le contenu est requis', $errors);
    }

    public function testHydration()
    {
        $data = [
            'title' => 'New Title',
            'content' => 'New Content',
            'user_id' => 2
        ];

        $texte = new Texte($data);
        $this->assertEquals('New Title', $texte->getTitle());
        $this->assertEquals('New Content', $texte->getContent());
        $this->assertEquals(2, $texte->getUserId());
    }
}
