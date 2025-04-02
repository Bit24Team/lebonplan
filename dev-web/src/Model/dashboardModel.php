<?php

namespace App\Model;

use PDO;
use PDOException;

class DashboardModel
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

    // Statistiques pour le tableau de bord admin
    public function getAdminStats(): array
    {
        $stats = [];
        
        // Nombre d'utilisateurs
        $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM Users");
        $stats['users_count'] = $stmt->fetch()['count'];
        
        // Nombre d'entreprises
        $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM Companies");
        $stats['companies_count'] = $stmt->fetch()['count'];
        
        // Offres actives (on considère toutes les offres comme actives dans cette version)
        $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM Offers");
        $stats['active_offers_count'] = $stmt->fetch()['count'];
        
        // Candidatures des 7 derniers jours
        $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM Applications WHERE application_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)");
        $stats['weekly_applications'] = $stmt->fetch()['count'];
        
        return $stats;
    }

    // Données pour les graphiques admin
    public function getAdminChartsData(): array
    {
        $data = [];
        
        // Graphique des candidatures (30 derniers jours)
        $stmt = $this->pdo->query("
            SELECT DATE(application_date) as date, COUNT(*) as count 
            FROM Applications 
            WHERE application_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            GROUP BY DATE(application_date)
            ORDER BY date
        ");
        $applicationsData = $stmt->fetchAll();
        
        $labels = [];
        $values = [];
        foreach ($applicationsData as $row) {
            $labels[] = $row['date'];
            $values[] = $row['count'];
        }
        
        $data['applications_chart'] = [
            'labels' => $labels,
            'data' => $values
        ];
        
        // Répartition des utilisateurs (0 = candidat, 1 = manager, 2 = admin)
        $usersChartData = [0, 0, 0];
        
        // Candidats (permission = 0)
        $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM Users WHERE permission = 0");
        $usersChartData[0] = $stmt->fetch()['count'];
        
        // Managers (permission = 1)
        $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM Users WHERE permission = 1");
        $usersChartData[1] = $stmt->fetch()['count'];
        
        // Admins (permission = 2)
        $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM Users WHERE permission = 2");
        $usersChartData[2] = $stmt->fetch()['count'];
        
        $data['users_chart'] = [
            'data' => $usersChartData
        ];
        
        return $data;
    }

    // Activité récente simulée pour admin
    public function getRecentActivity(int $limit = 10): array
    {
        // Simulation d'activité récente basée sur les candidatures
        $stmt = $this->pdo->prepare("
            SELECT 
                a.application_date as date,
                CONCAT(u.first_name, ' ', u.last_name) as user,
                'Candidature' as action,
                CONCAT('Postulé à l\'offre: ', o.title) as details
            FROM Applications a
            JOIN Users u ON a.id_candidate = u.id
            JOIN Offers o ON a.id_offer = o.id
            ORDER BY a.application_date DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    // Gestion des utilisateurs pour admin
    public function getAllUsers(): array
    {
        $stmt = $this->pdo->query("
            SELECT 
                u.*,
                c.name as company_name,
                (SELECT COUNT(*) FROM Applications WHERE id_candidate = u.id) as applications_count,
                CASE 
                    WHEN u.permission = 2 THEN 'Admin'
                    WHEN u.permission = 1 THEN 'Manager'
                    ELSE 'Candidat'
                END as role_name,
                IF(u.permission = 1, 1, 0) as is_active
            FROM Users u
            LEFT JOIN Companies c ON u.id = c.id_manager
            ORDER BY u.id DESC
        ");
        return $stmt->fetchAll();
    }

    // Gestion des entreprises pour admin
    public function getAllCompanies(): array
    {
        $stmt = $this->pdo->query("
            SELECT 
                c.*,
                CONCAT(u.first_name, ' ', u.last_name) as manager_name,
                u.email as manager_email,
                (SELECT COUNT(*) FROM Offers WHERE id_company = c.id) as offers_count,
                (SELECT AVG(amount) FROM Evaluations WHERE to_company = c.id) as average_rating
            FROM Companies c
            LEFT JOIN Users u ON c.id_manager = u.id
            ORDER BY c.id DESC
        ");
        return $stmt->fetchAll();
    }

    // Gestion des offres pour admin
    public function getAllOffers(string $filter = 'all'): array
    {
        // Dans cette version, on ignore le filtre car pas de champ status dans la table
        $stmt = $this->pdo->query("
            SELECT 
                o.*,
                c.name as company_name,
                (SELECT COUNT(*) FROM Applications WHERE id_offer = o.id) as applications_count,
                'active' as status
            FROM Offers o
            JOIN Companies c ON o.id_company = c.id
            ORDER BY o.start_date DESC
        ");
        return $stmt->fetchAll();
    }

    // Gestion des candidatures pour admin
    public function getAllApplications(string $filter = 'all'): array
    {
        $sql = "
            SELECT 
                a.*,
                o.title as offer_title,
                c.name as company_name,
                CONCAT(u.first_name, ' ', u.last_name) as candidate_name,
                u.email as candidate_email
            FROM Applications a
            JOIN Offers o ON a.id_offer = o.id
            JOIN Companies c ON o.id_company = c.id
            JOIN Users u ON a.id_candidate = u.id
        ";
        
        // On utilise le champ status existant dans Applications
        if ($filter === 'pending') {
            $sql .= " WHERE a.status = 'en attente'";
        } elseif ($filter === 'accepted') {
            $sql .= " WHERE a.status = 'accepté'";
        } elseif ($filter === 'rejected') {
            $sql .= " WHERE a.status = 'refusé'";
        } elseif ($filter === 'wishlist') {
            $sql .= " WHERE a.status = 'wishlist'";
        }
        
        $sql .= " ORDER BY a.application_date DESC";
        
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    // Paramètres du site simulés
    public function getSiteSettings(): array
    {
        // Retourne des valeurs par défaut sans nécessiter de table Settings
        return [
            'site_name' => 'Lebonplan',
            'site_email' => 'contact@lebonplan.com',
            'maintenance_mode' => 0,
            'registration_allowed' => 1
        ];
    }

    // Sauvegarder les paramètres du site (ne fait rien dans cette version)
    public function saveSiteSettings(array $settings): bool
    {
        // Ne pas sauvegarder réellement sans la table Settings
        return true;
    }

    // Données pour le tableau de bord manager
    public function getManagerData(int $managerId): array
    {
        $data = [];
        
        // Récupérer l'entreprise du manager
        $stmt = $this->pdo->prepare("
            SELECT * FROM Companies WHERE id_manager = :manager_id
        ");
        $stmt->execute([':manager_id' => $managerId]);
        $data['company'] = $stmt->fetch();
        
        if ($data['company']) {
            $companyId = $data['company']['id'];
            
            // Nombre d'offres
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) as count FROM Offers WHERE id_company = :company_id
            ");
            $stmt->execute([':company_id' => $companyId]);
            $data['offers_count'] = $stmt->fetch()['count'];
            
            // Nombre total de candidatures
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) as count 
                FROM Applications a
                JOIN Offers o ON a.id_offer = o.id
                WHERE o.id_company = :company_id
            ");
            $stmt->execute([':company_id' => $companyId]);
            $data['total_applications'] = $stmt->fetch()['count'];
            
            // Nouvelles candidatures (7 derniers jours)
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) as count 
                FROM Applications a
                JOIN Offers o ON a.id_offer = o.id
                WHERE o.id_company = :company_id 
                AND a.application_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
            ");
            $stmt->execute([':company_id' => $companyId]);
            $data['new_applications'] = $stmt->fetch()['count'];
            
            // Offres avec candidatures
            $stmt = $this->pdo->prepare("
                SELECT 
                    o.id, 
                    o.title,
                    o.start_date,
                    o.salary,
                    COUNT(a.id) as applications_count
                FROM Offers o
                LEFT JOIN Applications a ON o.id = a.id_offer
                WHERE o.id_company = :company_id
                GROUP BY o.id
                ORDER BY o.start_date DESC
            ");
            $stmt->execute([':company_id' => $companyId]);
            $data['offers'] = $stmt->fetchAll();
            
            // Toutes les candidatures groupées par offre
            $stmt = $this->pdo->prepare("
                SELECT 
                    o.id,
                    o.title,
                    a.id as application_id,
                    a.application_date,
                    a.status,
                    a.resume,
                    a.cover_letter,
                    u.first_name,
                    u.last_name,
                    u.email
                FROM Offers o
                JOIN Applications a ON o.id = a.id_offer
                JOIN Users u ON a.id_candidate = u.id
                WHERE o.id_company = :company_id
                ORDER BY o.id, a.application_date DESC
            ");
            $stmt->execute([':company_id' => $companyId]);
            
            $groupedApplications = [];
            foreach ($stmt->fetchAll() as $row) {
                $offerId = $row['id'];
                if (!isset($groupedApplications[$offerId])) {
                    $groupedApplications[$offerId] = [
                        'id' => $offerId,
                        'title' => $row['title'],
                        'applications' => []
                    ];
                }
                $groupedApplications[$offerId]['applications'][] = $row;
            }
            
            $data['all_applications'] = array_values($groupedApplications);
        }
        
        return $data;
    }

    // Données pour le tableau de bord utilisateur
    public function getUserData(int $userId): array
    {
        $data = [];

        // Récupérer les compétences
        $stmt = $this->pdo->prepare("
            SELECT s.id, s.name 
            FROM UserSkill us
            JOIN Skills s ON us.id_skill = s.id
            WHERE us.id_user = :user_id
        ");
        $stmt->execute([':user_id' => $userId]);
        $data['user_skills'] = $stmt->fetchAll();

        // Candidatures
        $stmt = $this->pdo->prepare("
            SELECT 
                a.*,
                o.title as offer_title,
                c.name as company_name,
                o.salary,
                o.start_date
            FROM Applications a
            JOIN Offers o ON a.id_offer = o.id
            JOIN Companies c ON o.id_company = c.id
            WHERE a.id_candidate = :user_id
            AND a.status != 'wishlist'
            ORDER BY a.application_date DESC
        ");
        $stmt->execute([':user_id' => $userId]);
        $data['applications'] = $stmt->fetchAll();

        // Wishlist
        $stmt = $this->pdo->prepare("
            SELECT 
                a.*,
                o.title as offer_title,
                c.name as company_name,
                o.salary,
                o.start_date,
                o.description
            FROM Applications a
            JOIN Offers o ON a.id_offer = o.id
            JOIN Companies c ON o.id_company = c.id
            WHERE a.id_candidate = :user_id
            AND a.status = 'wishlist'
            ORDER BY a.application_date DESC
        ");
        $stmt->execute([':user_id' => $userId]);
        $data['wishlist'] = $stmt->fetchAll();

        return $data;
    }
    public function getUserById(int $userId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT id, first_name, last_name, email, phone, groupe, permission 
            FROM Users 
            WHERE id = :id
        ");
        $stmt->execute([':id' => $userId]);
        return $stmt->fetch() ?: [];
    }
    
    public function getCompleteUserData(int $userId): array
    {
        $user = $this->getUserById($userId);
        
        if (empty($user)) {
            return [];
        }
    
        return [
            'user' => $user,
            'skills' => $this->getUserSkills($userId),
            'applications' => $this->getUserApplications($userId),
            'wishlist' => $this->getUserWishlist($userId)
        ];
    }
    
    private function getUserSkills(int $userId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT s.id, s.name 
            FROM UserSkill us
            JOIN Skills s ON us.id_skill = s.id
            WHERE us.id_user = :user_id
        ");
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll();
    }
    
    private function getUserApplications(int $userId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT /* requête existante pour les applications */
        ");
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll();
    }
    
    private function getUserWishlist(int $userId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT /* requête existante pour la wishlist */
        ");
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll();
    }
}