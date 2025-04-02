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

        return $this->render('admin.twig', [
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

        return $this->render('admin.twig', [
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

        return $this->render('admin.twig', [
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

        return $this->render('admin.twig', [
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

        return $this->render('admin.twig', [
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

        return $this->render('admin.twig', [
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
        if (!$user || $user['permission'] != 1) {
            return $this->redirectToRoute('login_page');
        }

        $data = $this->model->getManagerData($user['id']);

        return $this->render('company.twig', [
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
        $userSession = $session->get('user');
        if (!$userSession) {
            return $this->redirectToRoute('login_page');
        }

        // Récupération des données via le modèle
        $userData = $this->model->getUserFullData($userSession['id']);
        $dashboardData = $this->model->getUserData($userSession['id']);

        return $this->render('user.twig', [
            'user' => array_merge($userSession, $userData), // Fusion des données de session et de la BDD
            'user_skills' => $dashboardData['user_skills'],
            'applications' => $dashboardData['applications'],
            'wishlist' => $dashboardData['wishlist']
        ]);
    }
}