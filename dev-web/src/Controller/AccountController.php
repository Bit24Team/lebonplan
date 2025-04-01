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
        return $this->render('account/base.twig');
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
        $session->set("user", $request->get("user"));
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

}

