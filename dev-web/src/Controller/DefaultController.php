<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends AbstractController
{
    // Page d'accueil
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        return new Response('Hello, World !');
    }
    // Page des offres
    #[Route('/offres', name:'offers', methods: ['GET'])]
    public function show_offers(): Response
    {
        return new Response('Offres');
    }
    // Page d'une offre unique
    #[Route('/offre/{offer_id}', name:'offer', methods: ['GET'])]
    public function show_offer(int $offer_id): Response
    {
        return new Response("Offre N°$offer_id");
    }
    // Page des entreprises
    #[Route("/entreprises", name:"companies", methods: ["GET"])]
    public function show_companies(): Response
    {
        return new Response('Entreprises');
    }
    // Page d'une entreprise unique
    #[Route('entreprise/{company_id}', name:'company', methods: ['GET'])]
    public function show_company(int $company_id): Response
    {
        return new Response("Entreprise N°$company_id");
    }
    // Page du compte
    #[Route("/compte", name:"account", methods: ["GET"])]
    public function account(): Response
    {
        return new Response("Compte");
    }
    // Page de connexion
    #[Route("/connexion", name:"login_page", methods: ["GET"])]
    public function login_page(): Response
    {
        return $this->render('login.html.twig');
    }
    // Requête de connexion
    #[Route("/connexion", name:"login", methods: ["POST"])]
    public function login(Request $request, SessionInterface $session): Response
    {
        $username = $request->request->get('username');
        $password = $request->request->get('password');
        $user_id = 
        $session->set('user', [
            'user_id' => $username,
            'role' => 'admin',
        ]);
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
    #[Route("/inscription", name:"register_page", methods: ["GET"])]
    public function register_page(): Response
    {
        return new Response("Page d'inscription");
    }
    // Requête s'inscription
    #[Route("/inscription", name:"register", methods: ["POST"])]
    public function register(Request $request, SessionInterface $session): Response
    {
        $username = $request->request->get("username");
        $password = $request->request->get("password");
        $session->set("user", [
            "username"=> $username,
            "password"=> $password,
        ]);
        return new Response("Inscription terminée !");
    }
}