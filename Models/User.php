<?php

class User
{
    private $id;
    private $name;
    private $email;
    private $password;
    private $created_at;

    public function __construct($data)
    {
        $this->id = $data['id'] ?? null;
        $this->name = $data['name'] ?? '';
        $this->email = $data['email'] ?? '';
        $this->password = $data['password'] ?? '';
        $this->created_at = $data['created_at'] ?? null;
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

    public function getPassword()
    {
        return $this->password;
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }
        $manager = new Manager();
        $connexion = $manager->getConnexion();
        if ($this->id) {
            // Update existing user
            $sql = "UPDATE users SET name = :name, email = :email, password = :password WHERE id = :id";
            $stmt = $connexion->prepare($sql);
            $stmt->execute(['name' => $this->name, 'email' => $this->email, 'password' => $this->password, 'id' => $this->id]);
        } else {
            // Insert new user
            $sql = "INSERT INTO users (name, email, password, created_at) VALUES (:name, :email, :password, NOW())";
            $stmt = $connexion->prepare($sql);
            $stmt->execute(['name' => $this->name, 'email' => $this->email, 'password' => $this->password]);
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
