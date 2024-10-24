<?php

class User
{
    private $id;
    private $name;
    private $email;

    public function __construct($data)
    {
        $this->id = $data['id'] ?? null;
        $this->name = $data['name'] ?? '';
        $this->email = $data['email'] ?? '';
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function save()
    {
        $connexion = Bdd::getConnexion();
        if ($this->id) {
            // Update existing user
            $sql = "UPDATE users SET name = :name, email = :email WHERE id = :id";
            $stmt = $connexion->prepare($sql);
            $stmt->execute(['name' => $this->name, 'email' => $this->email, 'id' => $this->id]);
        } else {
            // Insert new user
            $sql = "INSERT INTO users (name, email) VALUES (:name, :email)";
            $stmt = $connexion->prepare($sql);
            $stmt->execute(['name' => $this->name, 'email' => $this->email]);
            $this->id = $connexion->lastInsertId();
        }
    }

    public static function find($id)
    {
        $connexion = Bdd::getConnexion();
        $sql = "SELECT * FROM users WHERE id = :id";
        $stmt = $connexion->prepare($sql);
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? new self($data) : null;
    }
}

?>
