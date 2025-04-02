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
public function show_companies(Request $request): Response
{
    $page = $request->query->getInt('page', 1);
    $company_name = $request->query->get('company_name');
    $company_desc = $request->query->get('company_desc');
    $company_email = $request->query->get('company_email');
    $company_phone = $request->query->get('company_phone');
    $company_rating = $request->query->getInt('company_rating');

    $result = $this->model->researchCompaniesPaginated(
        $company_name,
        $company_desc,
        $company_email,
        $company_phone,
        $company_rating,
        $page
    );

    return $this->render('company/search.twig', [
        'companies' => $result['companies'],
        'pagination' => [
            'current_page' => $result['current_page'],
            'total_pages' => $result['total_pages'],
            'total_items' => $result['total'],
        ],
        'search_params' => [
            'company_name' => $company_name,
            'company_desc' => $company_desc,
            'company_email' => $company_email,
            'company_phone' => $company_phone,
            'company_rating' => $company_rating,
        ]
    ]);
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
