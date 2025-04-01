<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use App\Model\OfferModel;

class OfferController extends AbstractController
{
    //private OfferModel $model;
    #[Route('/offres', name: 'offers', methods: ['GET'])]
    public function show_offers(): Response
    {
        return new Response('Offres');
    }

    #[Route('/offre/{offer_id}', name: 'offer', methods: ['GET'])]
    public function show_offer(int $offer_id): Response
    {
        return new Response("Offre N°$offer_id");
    }
    #[Route('/offre/{offer_id}', name: 'submit_application', methods: ['POST'])]
    public function submit_application(int $offer_id, Request $request, SessionInterface $session): Response
    {
        return new Response('Candidature envoyée !');
    }
    #[Route('/ajouter/offre', name:'add_offer_page', methods: ['GET'])]
    public function add_offer_page(): Response
    {
        return $this->render('add_offer.twig', ['is_active' => 'active']);
    }
    #[Route('/ajouter/offre', name:'add_offer', methods: ['POST'])]
    public function add_offer(int $offer_id, Request $request, SessionInterface $session): Response
    {
        $manager_id = $session->get('user')['id'];
        $title = $request->request->get("title");
        $description = $request->request->get("description");
        $location = $request->request->get("location");
        $salary = $request->request->get("salary");
        $contract_type = $request->request->get("contract_type");

        // Assuming you have a model to handle the database operations
        // $this->model->addOffer($company_id, $title, $description, $location, $salary, $contract_type);

        return new Response('Offre ajoutée !');
    }

    #[Route('/api/offers', name: 'api_offers', methods: ['GET'])]
    public function get_offer(): JsonResponse
{
    // Assuming you have a model to handle the database operations
    // $offers = $this->model->getOffers();
    return $this->json([
        
    ]);
}
#[Route('/test/offre', name:'offer_test', methods: ['GET'])]
    public function offer_test(): Response
    {
        return $this->render('offer_form_test.twig');
    }
}