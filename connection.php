<?php
try {
    // Chaîne de connexion PDO
    $dsn = 'mysql:host=localhost;dbname=WK;charset=utf8mb4';
    $username = 'titouan';
    $password = 'Posutiag3';

    // Options pour PDO
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];

    // Créer une nouvelle instance de PDO
    $pdo = new PDO($dsn, $username, $password, $options);
    $sql = "SELECT COUNT(*) FROM utilisateurs WHERE pseudo = 'Gandalf'";
    $result = $pdo->query($sql);

    if ($result === false) {
        die("Error execution request");
    }
    $result = $result->fetchColumn();
    echo $result;

    $sql = "SELECT * from utilisateurs where pseudo = 'Gandalf'";
    $result = $pdo->query($sql);
    $user = $result->fetch(PDO::FETCH_NUM);
    echo "Statut Admin (indexé) : " . $user[3] . "<br>";
} catch (PDOException $e) {
    // Gestion des erreurs de connexion
    echo "Erreur de connexion : " . $e->getMessage();
}
