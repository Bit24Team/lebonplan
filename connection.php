<?php
try {
    // Connexion à la base de données
    $dsn = 'mysql:host=localhost;dbname=WK;charset=utf8mb4';
    $username = 'titouan';
    $password = 'Posutiag3';

    // Options PDO
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];

    // Création de la connexion PDO
    $pdo = new PDO($dsn, $username, $password, $options);

    // Vérification de la soumission du formulaire
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
    die("<p style='color: red;'>Erreur de connexion : " . $e->getMessage() . "</p>");
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