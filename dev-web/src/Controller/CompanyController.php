<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Model\Model;

class CompanyController extends AbstractController
{
    private Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    #[Route("/entreprises", name: "companies", methods: ["GET"])]
    public function show_companies(): Response
    {
        return new Response('Entreprises');
    }

    #[Route('/entreprise/{company_id}', name: 'company', methods: ['GET'])]
    public function show_company(int $company_id): Response
    {
        return $this->render('Entreprise.html.twig');
    }

    #[Route("/ajout/entreprise", name: "company_register_page", methods: ["GET"])]
    public function company_register_page(): Response
    {
        return $this->render("wk.html.twig");
    }

    #[Route("/ajout/entreprise", name: "company_register", methods: ["POST"])]
    public function company_register(Request $request): Response
    {
        $name = $request->request->get("name");
        $description = $request->request->get("description");
        $contact_mail = $request->request->get("contact-mail");
        $contact_phone = $request->request->get("contact-phone");

        $this->model->newcompany($name, $description, $contact_mail, $contact_phone, $contact_phone);

        return new Response("Inscription terminée !");
    }
}
