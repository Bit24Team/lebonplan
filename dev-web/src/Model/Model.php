<?php

namespace App\Model;

use PDO;
use PDOException;

class Model
{
    private PDO $pdo;

    public function __construct()
    {
        try {
            $dsn = 'mysql:host=localhost;dbname=Lebonplan;charset=utf8mb4';
            $username = 'titouan';
            $password = 'Posutiag3';
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ];

            $this->pdo = new PDO($dsn, $username, $password, $options);
        } catch (PDOException $e) {
            die('Connection failed: ' . $e->getMessage());
        }
    }

    public function createUsers(string $first_name, string $last_name, string $password, string $email, int $permission, string $phone, string $group): bool
    {
        $sql = "INSERT INTO Users (first_name, last_name, password, email, permission, phone, `group`)
                VALUES (:first_name, :last_name, :password, :email, :permission, :phone, :group)";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':first_name' => $first_name,
            ':last_name' => $last_name,
            ':password' => password_hash($password, PASSWORD_BCRYPT), // Hashage du mot de passe
            ':email' => $email,
            ':permission' => $permission,
            ':phone' => $phone,
            ':group' => $group
        ]);
    }

    public function login(string $email, string $password): bool
    {
        $sql = "SELECT * FROM Users WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            echo "<p style='color: green;'>Connexion réussie ! Bienvenue, " . htmlspecialchars($user['first_name']) . ".</p>";
            return true;
        } else {
            echo "<p style='color: red;'>Email ou mot de passe incorrect.</p>";
            return false;
        }
    }

    public function researchcompagny(?string $company_name, ?string $company_desc, ?string $company_email, ?string $company_phone, ?int $company_rating): array
    {
        $sql = "SELECT * FROM Companies INNER JOIN Evaluations ON Companies.id = Evaluations.to_company WHERE 1=1";
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
            $sql .= " AND Evaluations.amount = :company_rating";
            $params[':company_rating'] = $company_rating;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    function newcompagny($pdo, $id, $idmanager, $email, $name, $description, $contact_mail, $contact_phone) {

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            // Récupérer les valeurs du formulaire
            $id = $_POST['id'] ?? '';
            $idmanager = $_POST['idmanager'] ?? '';
            $email = $_POST['email'] ?? '';
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';
            $contact_mail = $_POST['contact_mail'] ?? '';
            $contact_phone = $_POST['contact_phone'] ?? '';
        
            // Call the function to insert the new company
            newcompagny($pdo, $id, $idmanager, $email, $name, $description, $contact_mail, $contact_phone);
        
            // Pass a success message to the template
            $successMessage = "Company added successfully!";
            echo $twig->render('Entreprise.html.twig', ['successMessage' => $successMessage]);
            exit();
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
}
?>
