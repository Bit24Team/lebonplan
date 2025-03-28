<?php

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
    echo 'Connection failed: ' . $e->getMessage();
}

function create_users($pdo, $first_name, $last_name, $password, $email, $permission, $phone, $group) {
    //Création de la requète
    $sql = "INSERT INTO Users (first_name, last_name, password, email, permission, phone, `group`)
            VALUES (:first_name, :last_name, :password, :email, :permission, :phone, :group)";

    $stmt = $pdo->prepare($sql);

    // Liaison des paramètres
    $stmt->bindParam(':first_name', $first_name);
    $stmt->bindParam(':last_name', $last_name);
    $stmt->bindParam(':password', $password);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':permission', $permission);
    $stmt->bindParam(':phone', $phone);
    $stmt->bindParam(':group', $group);

    // Exécuter la requête
    $result = $stmt->execute();


}


function login($pdo, $email, $password) {
    if (!empty($email) && !empty($password)) {
        // Requête préparée pour éviter les injections SQL
        $sql = "SELECT * FROM Users WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user) {
            // Comparaison des mots de passe
            if (password_verify($password, $user['password'])) { // Utilisation correcte de password_verify()
                echo "<p style='color: green;'>Connexion réussie ! Bienvenue, " . htmlspecialchars($user['first_name']) . ".</p>";
            } else {
                echo "<p style='color: red;'>Email ou mot de passe incorrect.</p>";
            }
        } else {
            echo "<p style='color: red;'>Email ou mot de passe incorrect.</p>";
        }
    } else {
        echo "<p style='color: red;'>Veuillez remplir tous les champs.</p>";
    }
}

function research_compagny($pdo,$company_name,$company_desc,$company_email,$company_phone,$company_intern,$company_rating){
     // Initialisation de la requête SQL avec une condition toujours vraie
     $sql = "SELECT * FROM Companies INNER JOIN Evaluations ON Companies.id = Evaluations.to_company WHERE 1=1";

     // Tableau pour stocker les paramètres de la requête préparée
     $params = [];
 
     // Ajouter les conditions en fonction des critères non nuls
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
 
     // Préparation et exécution de la requête
     $stmt = $pdo->prepare($sql);
     $stmt->execute($params);
 
     // Récupération des résultats
     $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
 
     // Retourner les résultats
     return $results;
 }

function number_interns($pdo,$name){
    $sql = "SELECT COUNT * FROM Applications inner join Offers on id_offer.Applications = id.offer inner join Companies on id_company.Offers = id.Companies where name.Compagnies=:name";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['name.Companies' => $name]);
    $user = $stmt->fetch();
    return $user;
}

?>
