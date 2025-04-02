<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Model\CompanyModel;

class CompanyController extends AbstractController
{
    private CompanyModel $model;

    public function __construct(CompanyModel $model)
    {
        $this->model = $model;
    }

    #[Route("/entreprises", name: "companies", methods: ["GET"])]
    public function show_companies(): Response
    {
        return $this->render('company/search.twig');
    }

    #[Route('/entreprise/{company_id}', name: 'company', methods: ['GET'])]
    public function show_company(int $company_id): Response
    {
        return $this->render('company/company.twig');
    }

    #[Route("/ajout/entreprise", name: "company_register_page", methods: ["GET"])]
    public function company_register_page(): Response
    {
        return $this->render('company/register.twig');
    }
    #[Route("/ajout/entreprise", name: "company_register", methods: ["POST"])]
    public function company_register(Request $request): Response
    {
        $manager_id = $request->request->get("idmanager");
        $name = $request->request->get("name");
        $description = $request->request->get("description");
        $contact_mail = $request->request->get("contact_mail");
        $contact_phone = $request->request->get("contact_phone");

        $this->model->newcompany($manager_id, $name, $description, $contact_mail, $contact_phone);

        return $this->redirectToRoute('index');
    }
}
