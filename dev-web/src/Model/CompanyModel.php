<?php

namespace App\Model;

use PDO;
use PDOException;

class CompanyModel
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
 
    public function researchcompany(?string $company_name, ?string $company_desc, ?string $company_email, ?string $company_phone, ?int $company_rating): array
    {
        $sql = "SELECT id FROM Companies INNER JOIN Evaluations ON Companies.id = Evaluations.to_company WHERE 1=1";
        $params = [];

        if ($company_name !== null) {
            $sql .= " AND Companies.name = :company_name";
            $params[':company_name'] = $company_name;
        }
        if ($company_desc !== null) {
            $sql .= " AND Companies.description = :company_desc";
            $params[':company_desc'] = $company_desc;
        }
        if ($company_email !== null) {
            $sql .= " AND Companies.contact_mail = :company_email";
            $params[':company_email'] = $company_email;
        }
        if ($company_phone !== null) {
            $sql .= " AND Companies.contact_phone = :company_phone";
            $params[':company_phone'] = $company_phone;
        }
        if ($company_rating !== null) {
            $sql .= " AND Evaluations.amount >= :company_rating";
            $params[':company_rating'] = $company_rating;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    function newcompany($idmanager,$name, $description, $contact_mail, $contact_phone) {
        
                $sql = "INSERT INTO Companies (id_manager,name,description,contact_mail,contact_phone) VALUES (:idmanager,:name,:description,:contact_mail,:contact_phone)";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([
                    'idmanager' => $idmanager,
                    'name' => $name,
                    'description' => $description,
                    'contact_mail' => $contact_mail,
                    'contact_phone' => $contact_phone
                ]);
            
        
            }   

    public function numberInterns(string $company_name): int
    {
        $sql = "SELECT COUNT(*) AS count FROM Applications
                INNER JOIN Offers ON Applications.id_offer = Offers.id
                INNER JOIN Companies ON Offers.id_company = Companies.id
                WHERE Companies.name = :company_name";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':company_name' => $company_name]);
        return $stmt->fetch()['count'] ?? 0;
    }

    public function modify_company(?string $name,?string $description,?string $email,?string $phone,INT $company_id):void
    {
        $sql = "UPDATE Companies SET ";
        $params = [];

        if ($name !== null) {
            $sql .= " Companies.name = :name";
            $params[':name'] = $name;
        }
        if ($description !== null) {
            $sql .= " Companies.description = :description";
            $params[':description'] = $description;
        }
        if ($email !== null) {
            $sql .= " Companies.contact_mail = :email";
            $params[':email'] = $email;
        }
        if ($phone !== null) {
            $sql .= " Companies.contact_phone = :company_phone";
            $params[':company_phone'] = $phone;
        }
        $sql .= " WHERE Companies=:company_id";
        $params[':company_id'] = $company_id;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
    }   
    public function rate_company(INT $user_id, INT $company_id,INT $rating):void
    {
        $sql = "SELECT Count(*) from Evaluations where from_user=:user_id AND to_company=:company_id ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':user_id' => $user_id,
            ':company_id' => $company_id
        ]);
        $stmt->fetch();

        if ($stmt>=1){
        $sql = "UPDATE Evaluations SET amount=:rating WHERE from_user=:user_id AND to_company=:company_id ";
        }
        else {
        $sql = "INSERT INTO Evaluations(from_user,to_compagny,amount) VALUES (:user_id,:company_id,:rating)";
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':user_id' => $user_id,
            ':company_id' => $company_id,
            ':rating' => $rating,
        ]);
    }
    public function get_rate(INT $company_id):INT {
        $sql = "SELECT amount FROM Evaluations where to_company=:company_id" ;
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':company_id' => $company_id
        ]);
        return $stmt->fetch();
    }  

    //sfx6
    public function delete_company(INT $company_id):void{
        $sql = "DELETE FROM Companies WHERE to_company=:company_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':company_id' => $company_id
        ]);
    }

    public function researchCompaniesPaginated(
        ?string $company_name, 
        ?string $company_desc, 
        ?string $company_email, 
        ?string $company_phone, 
        ?int $company_rating,
        int $page = 1,
        int $perPage = 10
    ): array {
        $sql = "SELECT Companies.* FROM Companies 
                LEFT JOIN Evaluations ON Companies.id = Evaluations.to_company 
                WHERE 1=1";
        $countSql = "SELECT COUNT(*) as total FROM Companies 
                     LEFT JOIN Evaluations ON Companies.id = Evaluations.to_company 
                     WHERE 1=1";
        $params = [];
    
        if ($company_name !== null) {
            $sql .= " AND Companies.name LIKE :company_name";
            $countSql .= " AND Companies.name LIKE :company_name";
            $params[':company_name'] = '%' . $company_name . '%';
        }
        if ($company_desc !== null) {
            $sql .= " AND Companies.description LIKE :company_desc";
            $countSql .= " AND Companies.description LIKE :company_desc";
            $params[':company_desc'] = '%' . $company_desc . '%';
        }
        if ($company_email !== null) {
            $sql .= " AND Companies.contact_mail LIKE :company_email";
            $countSql .= " AND Companies.contact_mail LIKE :company_email";
            $params[':company_email'] = '%' . $company_email . '%';
        }
        if ($company_phone !== null) {
            $sql .= " AND Companies.contact_phone LIKE :company_phone";
            $countSql .= " AND Companies.contact_phone LIKE :company_phone";
            $params[':company_phone'] = '%' . $company_phone . '%';
        }
        if ($company_rating !== null) {
            $sql .= " AND Evaluations.amount = :company_rating";
            $countSql .= " AND Evaluations.amount = :company_rating";
            $params[':company_rating'] = $company_rating;
        }
    
        // Ajout de la pagination
        $offset = ($page - 1) * $perPage;
        $sql .= " LIMIT :offset, :perPage";
        $params[':offset'] = $offset;
        $params[':perPage'] = $perPage;
    
        // Exécution de la requête principale
        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->execute();
        $companies = $stmt->fetchAll();
    
        // Exécution de la requête de comptage
        $stmtCount = $this->pdo->prepare($countSql);
        foreach ($params as $key => $value) {
            if ($key !== ':offset' && $key !== ':perPage') {
                $stmtCount->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
            }
        }
        $stmtCount->execute();
        $total = $stmtCount->fetch()['total'];
    
        return [
            'companies' => $companies,
            'total' => $total,
            'current_page' => $page,
            'per_page' => $perPage,
            'total_pages' => ceil($total / $perPage)
        ];
    }

        


}

?>
