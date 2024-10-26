<?php

class AudioRepository {
    private $manager;
    
    public function __construct() {
        $this->manager = new Manager();
    }
    
    public function findById(int $id): ?Audio {
        $data = $this->manager->readTableOne('audio', $id);
        return $data ? new Audio($data) : null;
    }
    
    public function findAllByUser(int $userId): array {
        $results = $this->manager->readTableAll('audio', $userId);
        $audios = [];
        foreach ($results as $data) {
            $audios[] = new Audio($data);
        }
        return $audios;
    }
    
    public function save(Audio $audio): void {
        $data = [
            'title' => $audio->getTitle(),
            'artist' => $audio->getArtist(),
            'image' => $audio->getImage(),
            'path' => $audio->getPath()
        ];
        
        if ($audio->getId()) {
            $this->manager->updateTable('audio', $data, $audio->getId());
        } else {
            $data['created_at'] = date('Y-m-d H:i:s');
            $result = $this->manager->insertTable('audio', $data);
            $audio->hydrate(['id' => $result['id']]);
        }
    }
    
    public function delete(int $id): void {
        $this->manager->deleteTable('audio', $id);
    }
}
