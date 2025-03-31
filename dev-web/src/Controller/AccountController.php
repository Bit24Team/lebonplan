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
        $first_name = $request->request->get("first_name");
        $last_name = $request->request->get("last_name");
        $email = $request->request->get("email");
        $password = $request->request->get("password");
        $phone = $request->request->get("phone");
        $school = $request->request->get("school");
        $class = $request->request->get("class");

        $this->model->createUser($first_name,$last_name,$password,$email,1,$phone,$school . "-" . $class);

        return new Response("Inscription terminée !");
    }
    #[Route("/inscription", name: "login", methods: ["POST"])]
    public function login(Request $request, SessionInterface $session): Response
    {
        $email = $request->request->get("email");
        $password = $request->request->get("password");


        //$this->model->login($email,$password)

        if ($this->model->login($email,$password)){
            return new Response("Connexion réussi");
        }
        else{
            return new Response("Mot de passe ou utilisateur incorect");
        }
    }

}

