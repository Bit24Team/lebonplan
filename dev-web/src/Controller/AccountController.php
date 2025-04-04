<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Model\AccountModel;


class AccountController extends AbstractController
{
    private AccountModel $model;

    public function __construct(AccountModel $model)
    {
        $this->model = $model;
    }

    #[Route('/deconnexion', name: 'logout', methods: ['GET'])]
    public function logout(SessionInterface $session): Response
    {
        $session->remove('user');
        return $this->redirectToRoute('index');
    }

    #[Route("/inscription", name: "register_page", methods: ["GET"])]
    public function register_page(): Response
    {
        return $this->render("account/login.twig", ['is_active' => ' active']);
    }

    #[Route("/inscription", name: "register", methods: ["POST"])]
    public function register(Request $request, SessionInterface $session): Response
    {
        return $this->login_or_register($request, $session);
    }
    #[Route("/connexion", name: "login_page", methods: ["GET"])]
    public function login_page(Request $request, SessionInterface $session): Response
    {
        $error = $request->get("error") ?? null;
        if ($session->has("user")) {
            return $this->redirectToRoute('index');
        }
        return $this->render('account/login.twig', ['is_active' => '','error' => $error]);
    }
    #[Route("/connexion", name: "login", methods: ["POST"])]
    public function login(Request $request, SessionInterface $session): Response
    {
        return $this->login_or_register($request, $session);
    }

    private function login_or_register(Request $request, SessionInterface $session): Response
    {
        $auth_type = $request->request->get("auth_type");
        if ($auth_type === "login") {
            $email = $request->request->get("email");
            $password = $request->request->get("password");
            $data = $this->model->login($email, $password);
            if ($data) {
                $session->set('user', $data);
                return $this->redirectToRoute('index');
            } else {
                return $this->redirectToRoute('login_page', ['error' => 'Invalid credentials']);
            }
        } elseif ($auth_type === "register") {
            $first_name = $request->request->get("first_name");
            $last_name = $request->request->get("last_name");
            $email = $request->request->get("email");
            $password = $request->request->get("password");
            $phone = $request->request->get("phone");
            $school = $request->request->get("school");
            $class = $request->request->get("class");
            $this->model->createUser($first_name, $last_name, $password, $email, 1, $phone, $school . "-" . $class);
            return $this->redirectToRoute('login_page');
        }
        return new Response("Erreur lors de l'authentification.");
    }
    #[Route('/mon-profil/editer', name: 'edit_profile')]
    public function editProfile(SessionInterface $session): Response
    {
        $user = $session->get('user');
        if (!$user) {
            return $this->redirectToRoute('login_page');
        }

        // Ajoutez ici la logique pour récupérer les données du profil à éditer
        return $this->render('account/edit_profile.twig', [
            'user' => $user
        ]);
    }

    #[Route('/mon-profil/mot-de-passe', name: 'change_password')]
    public function changePassword(SessionInterface $session): Response
    {
        $user = $session->get('user');
        if (!$user) {
            return $this->redirectToRoute('login_page');
        }

        return $this->render('account/change_password.twig');
    }
    #[Route('/mon-profil/editer/mise-a-jour', name: 'update_profile', methods: ['POST'])]
    public function updateProfile(Request $request, SessionInterface $session): Response
    {
        $user = $session->get('user');
        if (!$user) {
            return $this->redirectToRoute('login_page');
        }
    
        // Traitement de la mise à jour du profil
        // $request->request contient les données du formulaire
        // Redirigez vers la page de profil avec un message de succès
        
        $this->addFlash('success', 'Profil mis à jour avec succès');
        return $this->redirectToRoute('user_dashboard');
    }
    
    #[Route('/mon-profil/mot-de-passe/mise-a-jour', name: 'update_password', methods: ['POST'])]
    public function updatePassword(Request $request, SessionInterface $session): Response
    {
        $user = $session->get('user');
        if (!$user) {
            return $this->redirectToRoute('login_page');
        }
    
        // Traitement du changement de mot de passe
        // Vérifiez l'ancien mot de passe et mettez à jour
        
        $this->addFlash('success', 'Mot de passe mis à jour avec succès');
        return $this->redirectToRoute('user_dashboard');
    }
    #[Route("/redirection", name: "redirect_page", methods: ["GET"])]
    public function redirect_page(Request $request, SessionInterface $session): Response
    {
        $error = $request->get("error") ?? null;
        if ($session->has("user")) {
            return $this->redirectToRoute('index');
        }
        return $this->render('account/Redirection_page.twig');
    }
    #[Route("/inscription/entreprise", name: "company_register_page", methods: ["GET"])]
    public function company_register_page(Request $request, SessionInterface $session): Response
    {
        $error = $request->get("error") ?? null;
        if ($session->has("user")) {
            return $this->redirectToRoute('index');
        }
        return $this->render('account/login_company.twig');
    }
    #[Route("/inscription/entreprise", name: "company_register", methods: ["POST"])]
    public function company_register(Request $request, SessionInterface $session): Response
    {
        return $this->login_or_register($request, $session);
    }
}

