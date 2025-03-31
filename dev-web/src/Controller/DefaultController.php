<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

include './Model/Model.php';

class DefaultController extends AbstractController
{
    // Page d'accueil
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('index.twig');
    }
    // Page des offres
    #[Route('/offres', name: 'offers', methods: ['GET'])]
    public function show_offers(): Response
    {
        return new Response('Offres');
    }
    // Page d'une offre unique
    #[Route('/offre/{offer_id}', name: 'offer', methods: ['GET'])]
    public function show_offer(int $offer_id): Response
    {
        return new Response("Offre N°$offer_id");
    }
    // Page des entreprises
    #[Route("/entreprises", name: "companies", methods: ["GET"])]
    public function show_companies(): Response
    {
        return new Response('Entreprises');
    }
    // Page d'une entreprise unique
    #[Route('entreprise/{company_id}', name: 'company', methods: ['GET'])]
    public function show_company(int $company_id): Response
    {
        return $this->render('Entreprise.html.twig');
    }
    // Page du compte
    #[Route("/compte", name: "account", methods: ["GET"])]
    public function account(): Response
    {
        return new Response("Compte");
    }
    // Page de connexion
    #[Route("/connexion", name: "login_page", methods: ["GET"])]
    public function login_page(): Response
    {
        return $this->render('login.twig', ['is_active'=>'']);
    }
    // Requête de connexion
    #[Route("/connexion", name: "login", methods: ["POST"])]
    public function login(Request $request, SessionInterface $session): Response
    {
        $username = $request->request->get('username');
        $password = $request->request->get('password');
        //$user_id = auth($username,$password);
        //$session->set('user', [
        //    'user_id' => $user_id
        //]);
        return new Response("Connexion réussie !");
    }
    #[Route('/deconnexion', name: 'logout', methods: ['GET'])]
    public function logout(SessionInterface $session): Response
    {
        // Supprimer les données de la session (déconnexion)
        $session->remove('user');
        return new Response("Déconnexion réussie !");
    }
    // Page d'inscription
    #[Route("/inscription", name: "register_page", methods: ["GET"])]
    public function register_page(): Response
    {
        return $this->render("login.twig", ['is_active'=>' active']);
    }
    // Requête s'inscription
    #[Route("/inscription", name: "register", methods: ["POST"])]
    public function register(Request $request, SessionInterface $session): Response
    {
        $username = $request->request->get("username");
        $password = $request->request->get("password");
        // $id = registeruser
        // $session->set("user", [
        //     "username" => $username,
        //     "id" => $id,
        // ]);
        return new Response("Inscription terminée !");
    }
    #[Route("/ajout/entreprise", name: "company_register_page", methods: ["GET"])]
    public function company_register_page(): Response
    {
        return $this->render("wk.html.twig");
    }
    #[Route("/ajout/entreprise", name: "register_page", methods: ["POST"])]
    public function company_register(Request $request): Response
    {
        $idmanager = $request->request->get("idmanager");
        $name = $request->request->get("name");
        $description = $request->request->get("description");
        $contact_mail = $request->request->get("contact-mail");
        $contact_phone = $request->request->get("contact-phone");
        
        return new Response("Inscription terminée !");
    }
}
