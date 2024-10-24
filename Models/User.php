<?php

class User
{
    private $id;
    private $pseudo;
    //private $email;
    private $mdp;
    private $created_at;

    public function __construct($data)
    {
        $this->id = $data['id'] ?? null;
        $this->pseudo = $data['pseudo'] ?? '';
        //$this->email = $data['email'] ?? '';
        $this->mdp = $data['mdp'] ?? '';
        $this->created_at = $data['created_at'] ?? null;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getPseudo()
    {
        return $this->pseudo;
    }

    /*public function getEmail()
    {
        return $this->email;
    }*/

    public function getMdp()
    {
        return $this->mdp;
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }

    public function save()
    {
        $manager = new Manager();
        if ($this->id) {
            $manager->updateTable('users', ['pseudo' => $this->pseudo, 'mdp' => $this->mdp], $this->id);
        } else {
            $result = $manager->insertTable('users', ['pseudo' => $this->pseudo, 'mdp' => $this->mdp, 'created_at' => date('Y-m-d H:i:s')]);
            $this->id = $result['id'];
        }
    }

    public static function find($id)
    {
        $manager = new Manager();
        $data = $manager->readTableOne('users', $id);
        return $data ? new self($data) : null;
    }
}

?>
