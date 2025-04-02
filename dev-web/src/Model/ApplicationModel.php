<?php

namespace App\Model;

use PDO;
use PDOException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ApplicationModel
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

    public function createApplication(
        int $offer_id,
        int $user_id,
        string $cv_path,
        string $motivation_letter_path
    ): bool {
        $sql = "INSERT INTO Applications (id_offer, id_candidate, resume, cover_letter, application_date, status) 
        VALUES (:offer_id, :user_id, :cv_path, :motivation_letter_path, CURDATE(), 'pending')";
        
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':offer_id' => $offer_id,
            ':user_id' => $user_id,
            ':cv_path' => $cv_path,
            ':motivation_letter_path' => $motivation_letter_path
        ]);
    }

    public function hasAlreadyApplied(int $offer_id, int $user_id): bool
    {
        $sql = "SELECT COUNT(*) FROM Applications 
                WHERE id_offer = :offer_id AND id_candidate = :user_id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':offer_id' => $offer_id,
            ':user_id' => $user_id
        ]);
        
        return $stmt->fetchColumn() > 0;
    }

    public function getOfferDetails(int $offer_id): ?array
    {
        $sql = "SELECT Offers.*, Companies.name as company_name, Companies.id as company_id
                FROM Offers
                JOIN Companies ON Offers.id_company = Companies.id
                WHERE Offers.id = :offer_id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':offer_id' => $offer_id]);
        $offer = $stmt->fetch();

        if (!$offer) {
            return null;
        }

        // Récupérer les compétences requises
        $sqlSkills = "SELECT Skills.name 
                      FROM OfferSkill
                      JOIN Skills ON OfferSkill.id_skill = Skills.id
                      WHERE OfferSkill.id_offer = :offer_id";
        
        $stmtSkills = $this->pdo->prepare($sqlSkills);
        $stmtSkills->execute([':offer_id' => $offer_id]);
        $skills = $stmtSkills->fetchAll(PDO::FETCH_COLUMN, 0);

        $offer['skills'] = $skills;

        return $offer;
    }

    public function uploadFile(UploadedFile $file, string $targetDir): string
{
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $fileName = uniqid() . '_' . $file->getClientOriginalName();
    $targetFile = $targetDir . $fileName;

    // Vérification du type de fichier
    $fileType = strtolower($file->getClientOriginalExtension());
    $allowedTypes = ['pdf', 'doc', 'docx', 'odt', 'rtf', 'jpeg', 'jpg', 'png'];
    
    if (!in_array($fileType, $allowedTypes)) {
        throw new \Exception('Type de fichier non autorisé');
    }

    // Vérification de la taille (2Mo max)
    if ($file->getSize() > 2000000) {
        throw new \Exception('Le fichier est trop volumineux (max 2Mo)');
    }

    if (!$file->move($targetDir, $fileName)) {
        throw new \Exception('Erreur lors du téléchargement du fichier');
    }

    return $fileName;
}
}