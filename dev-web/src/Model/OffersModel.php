<?php

namespace App\Model;

use PDO;
use PDOException;

class OffersModel
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
    
    public function get_id_offer($title,$company):int{
        $sql = 'SELECT id from Offers where title =:title AND id_company=:company';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':title'=> $title,
            ':company'=> $company
        ]);
        return $stmt->fetch();
    }
    public function get_id_skill($name):int{
        $sql = 'SELECT id from Skills where name=:name';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':name'=> $name,
        ]);
        return $stmt->fetch();
    }
    public function reacherch_offer(string $title,string $description,int $salary,$start_date,int $duration,int $skill){
        $sql = "SELECT id FROM Offers INNER JOIN OfferSkills ON Offers.id = OfferSkill.id_offer JOIN Skills ON Skills.id = id_skill WHERE 1=1";
        $params = [];

        if ($title !== null) {
            $sql .= " AND Offers.title = :title";
            $params[':title'] = $title;
        }
        if ($description !== null) {
            $sql .= " AND Offers.description = :description";
            $params[':description'] = $description;
        }
        if ($salary !== null) {
            $sql .= " AND Offers.salary = :salary";
            $params[':salary'] = $salary;
        }
        if ($start_date !== null) {
            $sql .= " AND Offers.start_date = :start_date";
            $params[':start_date'] = $start_date;
        }
        if ($duration !== null) {
            $sql .= " AND Offers.duration = :duration";
            $params[':duration'] = $duration;
        }
        if ($skill !== null) {
            $sql .= " AND id.skills = :skill";
            $params[':skill'] = $skill;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    
    }

    public function create_offer(string $skills, string $title, string $description, int $company, float $salary, string $start_date, string $duration)
    {
        $sql = "INSERT INTO Offers (id_company,title,description,salary,start_date,duration) 
                VALUES (:id_company,:title,:description,:salary,:start_date,:duration)";
        
        $stmt = $this->pdo->prepare($sql);
        

        $stmt->execute([
            ':id_company' => $company,
            ':title' => $title,
            ':description' => $description,
            ':salary' => $salary,
            ':start_date' => $start_date,
            ':duration' => $duration
        ]);
        
        $sql="SELECT COUNT * FROM Skills where name=:name";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':name'=> $skills ]);
        if($stmt->fetch()=0){
            $this->newskill($skills);
            $sql="INSERT into OfferSkill(id_offer,id_skill) values (:offer,:skill) ";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':skill' => $this->get_id_skill($skills),
                ':offer' => $this->get_id_offer($title,$company),

            ]);
        }   
    }
    public function newskill(string $skills):void{ 
    $sql= 'INSERT INTO Skills(name) VALUES (:skill)';
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([':skill'=> $skills ]);
    }



        public function update_offer(int $offer_id, ?string $skills, ?string $title, ?string $description, ?string $company, ?float $salary, ?string $start_date, ?string $end_date): void
        {
            // Initialiser la base de la requête SQL
            $sql = "UPDATE Offers SET ";
            $params = [];
        

            if ($title !== null) {
                $sql .= "title = :title, ";
                $params[':title'] = $title;
            }
            if ($description !== null) {
                $sql .= "description = :description, ";
                $params[':description'] = $description;
            }
            if ($company !== null) {
                $sql .= "company = :company, ";
                $params[':company'] = $company;
            }
            if ($skills !== null) {
                $sql .= "skills = :skills, ";
                $params[':skills'] = $skills;
            }
            if ($salary !== null) {
                $sql .= "salary = :salary, ";
                $params[':salary'] = $salary;
            }
            if ($start_date !== null) {
                $sql .= "start_date = :start_date, ";
                $params[':start_date'] = $start_date;
            }
            if ($end_date !== null) {
                $sql .= "end_date = :end_date, ";
                $params[':end_date'] = $end_date;
            }
        

            $sql = rtrim($sql, ', ');

            $sql .= " WHERE id = :offer_id";
            $params[':offer_id'] = $offer_id;
        
            // Préparer et exécuter la requête
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
        }
        


    public function delete_offer(int $offer_id)
    {

        $sql = "DELETE FROM Offers WHERE id = :offer_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':offer_id' => $offer_id
         ]);
    
    }
    
    
    
}



?>
