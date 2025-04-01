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

    public function login(string $email, string $password): array|null
    {
        $sql = "SELECT * FROM Users WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            return ['id'=>$user['id'],'permission'=>$user['permission']];
        } else {
            return null;
        }
    }
    public function get_perm(string $id): int|null
    {
        $sql = "SELECT permission FROM Users WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $user = $stmt->fetch();

        if ($user) {
            return (int)$user['permission'];
        } else {
            return null;
        }
    }
    public function get_user_id(string $first_name,string $last_name,string $email):INT{
        $sql = "SELECT id from Users Where first_name=:first_name AND last_name=:last_name AND email=:email ";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':first_name'=>$first_name,
            ':last_name'=>$last_name,
            ':email'=>$email
        ]);
        
        
    }
    public function modify_user(?string $first_name,?string $last_name,?string $password,?string $email,?string $phone,?string $group,?int $skills,int $user_id): void {
        $sql = "UPDATE Users SET ";
        $params = [];
    
        if ($first_name !== null) {
            $sql .= " first_name = :first_name, ";
            $params[':first_name'] = $first_name;
        }
        if ($last_name !== null) {
            $sql .= " last_name = :last_name, ";
            $params[':last_name'] = $last_name;
        }
        if ($password !== null) {
            $sql .= " password = :password, ";
            $params[':password'] = $password;
        }
        if ($email !== null) {
            $sql .= " email = :email, ";
            $params[':email'] = $email;
        }
        if ($phone !== null) {
            $sql .= " phone = :phone, ";
            $params[':phone'] = $phone;
        }
        if ($group !== null) {
            $sql .= " group = :group, ";
            $params[':group'] = $group;
        }

        
        // Remove the trailing comma and space
        $sql = rtrim($sql, ', ');
    
        $sql .= " WHERE id = :user_id";
        $params[':user_id'] = $user_id;
    
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        if ($skills !== null) {
            $sql = "UPDATE OfferSkill = :skills SET id_offer=:id_offer id_skill=:skill , ";
            
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id_offer'=>$this->get_user_id($first_name,$last_name,$email),
            ':skill'=>$skills
    ]);
    }



    public function delete_user(int $user_id):void{
        $sql = "DELETE from Users Where id=:user_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':user_id'=>$user_id]);
        
    }
}

?>
