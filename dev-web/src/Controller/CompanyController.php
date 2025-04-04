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
public function show_company(int $company_id, Request $request): Response
{
    $company = $this->model->getCompanyById($company_id);
    
    if (!$company) {
        throw $this->createNotFoundException('Entreprise non trouvée');
    }

    // Gestion de la notation si l'utilisateur est connecté
    $user = $request->getSession()->get('user');
    $userRating = null;
    
    if ($user) {
        $sql = "SELECT amount FROM Evaluations WHERE from_user = :user_id AND to_company = :company_id";
        $stmt = $this->model->getPDO()->prepare($sql);
        $stmt->execute([
            ':user_id' => $user['id'],
            ':company_id' => $company_id
        ]);
        $userRating = $stmt->fetchColumn();
    }

    return $this->render('company/company.twig', [
        'company' => $company,
        'user_rating' => $userRating,
        'user' => $user
    ]);
}

#[Route('/entreprise/{company_id}/rate', name: 'rate_company', methods: ['POST'])]
public function rateCompany(int $company_id, Request $request): Response
{
    $user = $request->getSession()->get('user');
    
    if (!$user) {
        return $this->redirectToRoute('login_page');
    }

    $rating = $request->request->getInt('rating');
    
    if ($rating < 1 || $rating > 5) {
        $this->addFlash('error', 'La note doit être entre 1 et 5');
        return $this->redirectToRoute('company', ['company_id' => $company_id]);
    }

    $this->model->rate_company($user['id'], $company_id, $rating);
    
    $this->addFlash('success', 'Merci pour votre notation !');
    return $this->redirectToRoute('company', ['company_id' => $company_id]);
}

    #[Route("/ajout/entreprise", name: "company_register_page", methods: ["GET"])]
    public function company_register_page(): Response
    {
        return $this->render('account/login_company.twig',['is_active' => ' active']);
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
    #[Route('/entreprise/{company_id}/modifier', name: 'edit_company_page', methods: ['GET'])]
public function editCompanyPage(int $company_id, Request $request): Response
{
    $user = $request->getSession()->get('user');
    $company = $this->model->getCompanyById($company_id);
    
    if (!$company) {
        throw $this->createNotFoundException('Entreprise non trouvée');
    }

    // Vérifier que l'utilisateur est le gestionnaire de l'entreprise
    if (!$user || $user['id'] != $company['id_manager']) {
        $this->addFlash('error', 'Vous n\'avez pas les droits pour modifier cette entreprise');
        return $this->redirectToRoute('company', ['company_id' => $company_id]);
    }

    return $this->render('company/edit.twig', [
        'company' => $company
    ]);
}

#[Route('/entreprise/{company_id}/modifier', name: 'edit_company', methods: ['POST'])]
public function editCompany(int $company_id, Request $request): Response
    {
        $user = $request->getSession()->get('user');
        $company = $this->model->getCompanyById($company_id);

        if (!$company) {
            throw $this->createNotFoundException('Entreprise non trouvée');
        }

        // Vérification des droits
        if (!$user || $user['id'] != $company['id_manager']) {
            $this->addFlash('error', 'Vous n\'avez pas les droits pour modifier cette entreprise');
            return $this->redirectToRoute('company', ['company_id' => $company_id]);
        }

        // Récupération des données du formulaire
        $name = $request->request->get('name');
        $description = $request->request->get('description');
        $email = $request->request->get('contact_mail');
        $phone = $request->request->get('contact_phone');

        // Validation basique
        if (empty($name)) {
            $this->addFlash('error', 'Le nom de l\'entreprise est obligatoire');
            return $this->redirectToRoute('edit_company_page', ['company_id' => $company_id]);
        }

        // Appel au modèle pour la modification
        $success = $this->model->modify_company(
            $name,
            $description,
            $email,
            $phone,
            $company_id
        );

        if ($success) {
            $this->addFlash('success', 'Les informations de l\'entreprise ont été mises à jour');
        } else {
            $this->addFlash('error', 'Aucune modification effectuée');
        }

        return $this->redirectToRoute('company_dashboard');
    }
}
