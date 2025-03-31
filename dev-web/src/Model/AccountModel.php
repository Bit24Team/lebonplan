<?php

namespace App\Model;

use PDO;
use PDOException;

class AccountModel
{
    private PDO $pdo;

    public function __construct()
    {
        try {
            $dsn = $_ENV['DB_DSN'];
            $username = $_ENV['DB_USR'];
            $password = $_ENV['DB_PWD'];
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ];

            $this->pdo = new PDO($dsn, $username, $password, $options);
        } catch (PDOException $e) {
            die('Connection failed: ' . $e->getMessage());
        }
    }

    public function createUser(string $first_name, string $last_name, string $password, string $email, int $permission, string $phone, string $group): bool
    {
        $sql = "INSERT INTO Users (first_name,last_name,password,email,permission,phone,groupe)
                VALUES (:first_name,:last_name,:password,:email,:permission,:phone,:groupe)";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':first_name' => $first_name,
            ':last_name' => $last_name,
            ':password' => password_hash($password, PASSWORD_BCRYPT), // Hashage du mot de passe
            ':email' => $email,
            ':permission' => $permission,
            ':phone' => $phone,
            ':groupe' => $group
        ]);
    }

    public function login(string $email, string $password): int|null
    {
        $sql = "SELECT * FROM Users WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            return $user['id'];
        } else {
            return null;
        }
    }

}
?>
