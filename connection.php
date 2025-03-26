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

    //1 Créer une nouvelle instance de PDO
    $pdo = new PDO($dsn, $username, $password, $options);
    //4
    $sql = "SELECT COUNT(*) FROM utilisateurs WHERE pseudo = 'Gandalf'";
    $result = $pdo->query($sql);

    if ($result === false) {
        die("Error execution request");
    }
    $result = $result->fetchColumn();
    echo $result . "<br>";
    //5.1
    $sql = "SELECT * from utilisateurs where pseudo = 'Gandalf'";
    $result = $pdo->query($sql);
    $user = $result->fetch(PDO::FETCH_NUM);
    echo "Statut Admin (indexé) : " . $user[3] . "<br>";

    // 5.2 Récupérer en tableau associatif
    $stmt = $pdo->query($sql); // Re-exécuter la requête
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Statut Admin (associatif) : " . $user['statutAdmin'] . "<br>";
    //5.3 Récupérer en objet anonyme
    $stmt = $pdo->query($sql);
    $user = $stmt->fetch(PDO::FETCH_OBJ);
    echo "Statut Admin (objet) : " . $user->statutAdmin . "<br>";

    // 6 Recuperer les pseudos 
    $sql = "SELECT pseudo from utilisateurs";
    $result = $pdo->query($sql);
    if ($result === false) {
        die("Erreur lors de la récupération des pseudos.");
    }
    foreach ($result as $row) {
        echo $row['pseudo'] . "</br>";
    }
    // 7 Gerer la co d'un  utilisateurs
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        // Récupérer les valeurs du formulaire
        $pseudo = $_POST['pseudo'] ?? '';
        $mdp = $_POST['mdp'] ?? '';

        if (!empty($pseudo) && !empty($mdp)) {
            // Requête préparée pour éviter les injections SQL
            $sql = "SELECT * FROM utilisateurs WHERE pseudo = :pseudo";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['pseudo' => $pseudo]);
            $user = $stmt->fetch();

            if ($user) {
                // Comparaison des mots de passe
                if ($mdp === $user['motDePasse']) { // Remplace par password_verify() si les mots de passe sont hachés
                    echo "<p style='color: green;'>Connexion réussie ! Bienvenue, " . htmlspecialchars($user['pseudo']) . ".</p>";
                } else {
                    echo "<p style='color: red;'>Pseudo ou mot de passe incorrect.</p>";
                }
            } else {
                echo "<p style='color: red;'>Pseudo ou mot de passe incorrect.</p>";
            }
        } else {
            echo "<p style='color: red;'>Veuillez remplir tous les champs.</p>";
        }
    }
} catch (PDOException $e) {
    // Gestion des erreurs de connexion
    echo "Erreur de connexion : " . $e->getMessage();
}
?>
<!-- Formulaire HTML pour la connexion -->
<form method="POST">
    <label>Pseudo :</label>
    <input type="text" name="pseudo" required>

    <label>Mot de passe :</label>
    <input type="password" name="mdp" required>

    <button type="submit">Se connecter</button>
</form>