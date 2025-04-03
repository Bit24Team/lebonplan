<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Model\DashboardModel;

class DashboardController extends AbstractController
{
    private DashboardModel $model;

    public function __construct(DashboardModel $model)
    {
        $this->model = $model;
    }

    #[Route('/admin', name: 'admin_dashboard')]
    public function adminDashboard(SessionInterface $session): Response
    {
        $user = $session->get('user');
        if (!$user || $user['permission'] != 2) {
            return $this->redirectToRoute('login_page');
        }

        $stats = $this->model->getAdminStats();
        $charts = $this->model->getAdminChartsData();
        $recentActivity = $this->model->getRecentActivity();

        return $this->render('account/admin.twig', [
            'stats' => $stats,
            'applications_chart' => $charts['applications_chart'],
            'users_chart' => $charts['users_chart'],
            'recent_activity' => $recentActivity,
            'active_tab' => 'dashboard'
        ]);
    }

    #[Route('/admin/users', name: 'admin_users')]
    public function adminUsers(SessionInterface $session): Response
    {
        $user = $session->get('user');
        if (!$user || $user['permission'] != 2) {
            return $this->redirectToRoute('login_page');
        }

        $users = $this->model->getAllUsers();

        return $this->render('account/admin.twig', [
            'active_tab' => 'users',
            'users' => $users
        ]);
    }

    #[Route('/admin/companies', name: 'admin_companies')]
    public function adminCompanies(SessionInterface $session): Response
    {
        $user = $session->get('user');
        if (!$user || $user['permission'] != 2) {
            return $this->redirectToRoute('login_page');
        }

        $companies = $this->model->getAllCompanies();

        return $this->render('account/admin.twig', [
            'active_tab' => 'companies',
            'companies' => $companies
        ]);
    }

    #[Route('/admin/offers', name: 'admin_offers')]
    public function adminOffers(Request $request, SessionInterface $session): Response
    {
        $user = $session->get('user');
        if (!$user || $user['permission'] != 2) {
            return $this->redirectToRoute('login_page');
        }

        $filter = $request->query->get('filter', 'all');
        $offers = $this->model->getAllOffers($filter);

        return $this->render('account/admin.twig', [
            'active_tab' => 'offers',
            'offers' => $offers,
            'filter' => $filter
        ]);
    }

    #[Route('/admin/applications', name: 'admin_applications')]
    public function adminApplications(Request $request, SessionInterface $session): Response
    {
        $user = $session->get('user');
        if (!$user || $user['permission'] != 2) {
            return $this->redirectToRoute('login_page');
        }

        $filter = $request->query->get('filter', 'all');
        $applications = $this->model->getAllApplications($filter);

        return $this->render('account/admin.twig', [
            'active_tab' => 'applications',
            'applications' => $applications,
            'filter' => $filter
        ]);
    }

    #[Route('/admin/settings', name: 'admin_settings')]
    public function adminSettings(SessionInterface $session): Response
    {
        $user = $session->get('user');
        if (!$user || $user['permission'] != 2) {
            return $this->redirectToRoute('login_page');
        }

        $settings = $this->model->getSiteSettings();

        return $this->render('account/admin.twig', [
            'active_tab' => 'settings',
            'settings' => $settings
        ]);
    }

    #[Route('/admin/settings/save', name: 'admin_settings_save', methods: ['POST'])]
    public function saveSettings(Request $request, SessionInterface $session): Response
    {
        $user = $session->get('user');
        if (!$user || $user['permission'] != 2) {
            return $this->redirectToRoute('login_page');
        }

        $this->model->saveSiteSettings([
            'site_name' => $request->request->get('siteName'),
            'site_email' => $request->request->get('siteEmail'),
            'maintenance_mode' => $request->request->get('maintenanceMode'),
            'registration_allowed' => $request->request->get('registrationAllowed')
        ]);

        return $this->redirectToRoute('admin_settings');
    }

    #[Route('/manager', name: 'manager_dashboard')]
    public function managerDashboard(SessionInterface $session): Response
    {
        $user = $session->get('user');
        if (!$user || $user['permission'] != 2) {
            return $this->redirectToRoute('login_page');
        }

        $data = $this->model->getManagerData($user['id']);

        return $this->render('account/company.twig', [
            'company' => $data['company'] ?? null,
            'offers' => $data['offers'] ?? [],
            'total_applications' => $data['total_applications'] ?? 0,
            'new_applications' => $data['new_applications'] ?? 0,
            'all_applications' => $data['all_applications'] ?? []
        ]);
    }

    #[Route('/user', name: 'user_dashboard')]
    public function userDashboard(SessionInterface $session): Response
    {
        $user = $session->get('user');
        if (!$user) {
            return $this->redirectToRoute('login_page');
        }

        $data = $this->model->getUserData($user['id']);

        return $this->render('account/user.twig', [
            'user' => $user,
            'user_skills' => $data['user_skills'],
            'available_skills' => $data['available_skills'], // Ajout des compétences disponibles
            'applications' => $data['applications'],
            'wishlist' => $data['wishlist']
        ]);
    }
    #[Route('/user/add-skill', name: 'user_add_skill', methods: ['POST'])]
    public function addUserSkill(Request $request, SessionInterface $session): Response
    {
        $user = $session->get('user');
        if (!$user) {
            return $this->redirectToRoute('login_page');
        }

        $skillId = $request->request->get('skill_id');
        if ($skillId) {
            $this->model->addSkillToUser($user['id'], $skillId);
        }

        return $this->redirectToRoute('user_dashboard');
    }
    #[Route('/user/remove-skill', name: 'user_remove_skill', methods: ['POST'])]
    public function removeUserSkill(Request $request, SessionInterface $session): Response
    {
        $user = $session->get('user');
        if (!$user) {
            return $this->redirectToRoute('login_page');
        }

        $skillId = $request->request->get('skill_id');
        if ($skillId) {
            $this->model->removeSkillFromUser($user['id'], $skillId);
        }

        return new Response('', Response::HTTP_OK);
    }
    #[Route('/manager/offer/{offer_id}/edit', name: 'edit_offer_page', methods: ['GET'])]
    public function editOfferPage(int $offer_id, SessionInterface $session): Response
    {
        $user = $session->get('user');
        if (!$user || $user['permission'] != 1) {
            return $this->redirectToRoute('login_page');
        }
    
        $offer = $this->model->getOfferById($offer_id);
        if (!$offer) {
            throw $this->createNotFoundException('Offre non trouvée');
        }
    
        $skills = $this->model->getSkillsForOffer($offer_id);
        $allSkills = $this->model->getAllAvailableSkills(0); // Passer 0 pour toutes les compétences
    
        return $this->render('offer/edit.twig', [
            'offer' => $offer,
            'skills' => $skills,
            'all_skills' => $allSkills
        ]);
    }
    
    #[Route('/manager/offer/{offer_id}/edit', name: 'edit_offer', methods: ['POST'])]
    public function editOffer(int $offer_id, Request $request, SessionInterface $session): Response
    {
        $user = $session->get('user');
        if (!$user || $user['permission'] != 1) {
            return $this->redirectToRoute('login_page');
        }
    
        $data = [
            'title' => $request->request->get('title'),
            'description' => $request->request->get('description'),
            'salary' => $request->request->get('salary'),
            'start_date' => $request->request->get('start_date'),
            'duration' => $request->request->get('duration')
        ];
    
        $this->model->updateOffer($offer_id, $data);
    
        $this->addFlash('success', 'Offre mise à jour avec succès');
        return $this->redirectToRoute('manager_dashboard');
    }
    
    #[Route('/manager/application/{application_id}', name: 'view_application', methods: ['GET'])]
    public function viewApplication(int $application_id, SessionInterface $session): Response
    {
        $user = $session->get('user');
        if (!$user || $user['permission'] != 1) {
            return $this->redirectToRoute('login_page');
        }
    
        $application = $this->model->getApplicationById($application_id);
        if (!$application) {
            throw $this->createNotFoundException('Candidature non trouvée');
        }
    
        return $this->render('application/view.twig', [
            'application' => $application
        ]);
    }
    
    #[Route('/manager/application/{application_id}/update-status', name: 'update_application_status', methods: ['POST'])]
    public function updateApplicationStatus(int $application_id, Request $request, SessionInterface $session): Response
    {
        $user = $session->get('user');
        if (!$user || $user['permission'] != 1) {
            return $this->redirectToRoute('login_page');
        }
    
        $status = $request->request->get('status');
        $validStatuses = ['accepté', 'refusé', 'en attente'];
    
        if (!in_array($status, $validStatuses)) {
            $this->addFlash('error', 'Statut invalide');
            return $this->redirectToRoute('view_application', ['application_id' => $application_id]);
        }
    
        $this->model->updateApplicationStatus($application_id, $status);
        $this->addFlash('success', 'Statut de la candidature mis à jour');
    
        return $this->redirectToRoute('view_application', ['application_id' => $application_id]);
    }
    
    #[Route('/manager/offer/{offer_id}/add-skill', name: 'add_skill_to_offer', methods: ['POST'])]
    public function addSkillToOffer(int $offer_id, Request $request, SessionInterface $session): Response
    {
        $user = $session->get('user');
        if (!$user || $user['permission'] != 1) {
            return $this->redirectToRoute('login_page');
        }
    
        $skillId = $request->request->get('skill_id');
        if ($skillId) {
            $this->model->addSkillToOffer($offer_id, $skillId);
        }
    
        return $this->redirectToRoute('edit_offer_page', ['offer_id' => $offer_id]);
    }
    
    #[Route('/manager/offer/{offer_id}/remove-skill', name: 'remove_skill_from_offer', methods: ['POST'])]
    public function removeSkillFromOffer(int $offer_id, Request $request, SessionInterface $session): Response
    {
        $user = $session->get('user');
        if (!$user || $user['permission'] != 1) {
            return $this->redirectToRoute('login_page');
        }
    
        $skillId = $request->request->get('skill_id');
        if ($skillId) {
            $this->model->removeSkillFromOffer($offer_id, $skillId);
        }
    
        return new Response('', Response::HTTP_OK);
    }
    
    #[Route('/manager/create-skill', name: 'create_skill', methods: ['POST'])]
    public function createSkill(Request $request, SessionInterface $session): Response
    {
        $user = $session->get('user');
        if (!$user || $user['permission'] != 1) {
            return $this->redirectToRoute('login_page');
        }
    
        $name = $request->request->get('name');
        if ($name) {
            $this->model->createSkill($name);
            $this->addFlash('success', 'Compétence créée avec succès');
        }
    
        return $this->redirectToRoute('edit_offer_page', ['offer_id' => $request->request->get('offer_id')]);
    }
}
