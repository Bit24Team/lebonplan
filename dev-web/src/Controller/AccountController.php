<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{
    #[Route("/compte", name: "account", methods: ["GET"])]
    public function account(): Response
    {
        return new Response("Compte");
    }

    #[Route("/connexion", name: "login_page", methods: ["GET"])]
    public function login_page(): Response
    {
        return $this->render('login.twig', ['is_active'=>'']);
    }

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
        $session->remove('user');
        return new Response("Déconnexion réussie !");
    }

    #[Route("/inscription", name: "register_page", methods: ["GET"])]
    public function register_page(): Response
    {
        return $this->render("login.twig", ['is_active'=>' active']);
    }

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
}
