<?php

require_once 'AbstractModel.php';
require_once 'Manager.php';

class Audio extends AbstractModel
{
    private $title;
    private $artist;
    private $image;
    private $path;

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function setArtist($artist)
    {
        $this->artist = $artist;
    }

    public function setImage($image)
    {
        $this->image = $image;
    }

    public function setPath($path)
    {
        $this->path = $path;
    }

    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getArtist()
    {
        return $this->artist;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getCreatedAt(): ?string
    {
        return $this->created_at;
    }

    public function save()
    {
        $manager = new Manager();
        if ($this->id) {
            $manager->updateTable('audio', [
                'title' => $this->title,
                'artist' => $this->artist,
                'image' => $this->image,
                'path' => $this->path
            ], $this->id);
        } else {
            $result = $manager->insertTable('audio', [
                'title' => $this->title,
                'artist' => $this->artist,
                'image' => $this->image,
                'path' => $this->path,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            $this->id = $result['id'];
        }
    }

    public static function find($id)
    {
        $manager = new Manager();
        $data = $manager->readTableOne('audio', $id);
        return $data ? new self($data) : null;
    }
}

?>
