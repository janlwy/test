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
        $connexion = Bdd::getConnexion();
        if ($this->id) {
            // Update existing user
            $sql = "UPDATE users SET pseudo = :pseudo, mdp = :mdp WHERE id = :id";
            $stmt = $connexion->prepare($sql);
            $stmt->execute(['pseudo' => $this->pseudo, 'mdp' => $this->mdp, 'id' => $this->id]);
        } else {
            // Insert new user
            $sql = "INSERT INTO users (pseudo, mdp, created_at) VALUES (:pseudo, :mdp, NOW())";
            $stmt = $connexion->prepare($sql);
            $stmt->execute(['pseudo' => $this->pseudo, 'mdp' => $this->mdp]);
            $this->id = $connexion->lastInsertId();
        }
    }

    public static function find($id)
    {
        $manager = new Manager();
        $connexion = $manager->getConnexion();
        $sql = "SELECT * FROM users WHERE id = :id";
        $stmt = $connexion->prepare($sql);
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? new self($data) : null;
    }
}

?>
