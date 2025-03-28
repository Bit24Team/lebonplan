<?php

namespace App\Model\Model;

use PDO;
use PDOException;

try {
    // Chaîne de connexion PDO
    $dsn = 'mysql:host=localhost;dbname=Lebonplan;charset=utf8mb4';
    $username = 'titouan';
    $password = 'Posutiag3';

    // Options pour PDO
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];

    // Création d'une nouvelle instance de PDO
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    die('Connection failed: ' . $e->getMessage());
}

/**
 * Création d'un utilisateur
 */
function create_users($pdo, $first_name, $last_name, $password, $email, $permission, $phone, $group) {
    try {
        $sql = "INSERT INTO Users (first_name, last_name, password, email, permission, phone, `group`)
                VALUES (:first_name, :last_name, :password, :email, :permission, :phone, :group)";

        $stmt = $pdo->prepare($sql);

        // Hashage du mot de passe avant insertion
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Liaison des paramètres
        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':last_name', $last_name);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':permission', $permission);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':group', $group);

        return $stmt->execute();
    } catch (PDOException $e) {
        die('Erreur lors de la création de l\'utilisateur : ' . $e->getMessage());
    }
}

/**
 * Connexion d'un utilisateur
 */
function login($pdo, $email, $password) {
    if (!empty($email) && !empty($password)) {
        $sql = "SELECT * FROM Users WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            echo "<p style='color: green;'>Connexion réussie ! Bienvenue, " . htmlspecialchars($user['first_name']) . ".</p>";
        } else {
            echo "<p style='color: red;'>Email ou mot de passe incorrect.</p>";
        }
    } else {
        echo "<p style='color: red;'>Veuillez remplir tous les champs.</p>";
    }
}

/**
 * Recherche d'une entreprise avec différents critères
 */
function research_company($pdo, $company_name = null, $company_desc = null, $company_email = null, $company_phone = null, $company_rating = null) {
    $sql = "SELECT * FROM Companies 
            INNER JOIN Evaluations ON Companies.id = Evaluations.to_company 
            WHERE 1=1";
    $params = [];

    if (!empty($company_name)) {
        $sql .= " AND Companies.name = :company_name";
        $params[':company_name'] = $company_name;
    }
    if (!empty($company_desc)) {
        $sql .= " AND Companies.description LIKE :company_desc";
        $params[':company_desc'] = "%$company_desc%";
    }
    if (!empty($company_email)) {
        $sql .= " AND Companies.contact_mail = :company_email";
        $params[':company_email'] = $company_email;
    }
    if (!empty($company_phone)) {
        $sql .= " AND Companies.contact_phone = :company_phone";
        $params[':company_phone'] = $company_phone;
    }
    if (!empty($company_rating)) {
        $sql .= " AND Evaluations.amount = :company_rating";
        $params[':company_rating'] = $company_rating;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * Retourne le nombre de stagiaires dans une entreprise donnée
 */
function number_interns($pdo, $company_name) {
    $sql = "SELECT COUNT(*) AS total 
            FROM Applications 
            INNER JOIN Offers ON Applications.id_offer = Offers.id
            INNER JOIN Companies ON Offers.id_company = Companies.id
            WHERE Companies.name = :company_name";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['company_name' => $company_name]);
    return $stmt->fetchColumn();
}

?>
