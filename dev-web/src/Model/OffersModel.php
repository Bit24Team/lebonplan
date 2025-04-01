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

    public function create_offer(string $skills, string $title, string $description, string $company, float $salary, string $start_date, string $end_date)
    {
        $sql = "INSERT INTO Offers (title, description, company, skills, salary, start_date, end_date) 
                VALUES (:title, :description, :company, :skills, :salary, :start_date, :end_date)";
        
        $stmt = $this->pdo->prepare($sql);
        

        $stmt->execute([
            ':title' => $title,
            ':description' => $description,
            ':company' => $company,
            ':skills' => $skills,
            ':salary' => $salary,
            ':start_date' => $start_date,
            ':end_date' => $end_date
        ]);
}
    public function update_offer(int $offer_id, string $skills, string $title, string $description, string $company, float $salary, string $start_date, string $end_date)
    {
        $sql = "UPDATE Offers 
                SET title = :title, 
                    description = :description, 
                    company = :company, 
                    skills = :skills, 
                    salary = :salary, 
                    start_date = :start_date, 
                    end_date = :end_date 
                WHERE id = :offer_id";

        $stmt = $this->pdo->prepare($sql);


        $stmt->execute([
            ':title' => $title,
            ':description' => $description,
            ':company' => $company,
            ':skills' => $skills,
            ':salary' => $salary,
            ':start_date' => $start_date,
            ':end_date' => $end_date,
            ':offer_id' => $offer_id 
        ]);

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
