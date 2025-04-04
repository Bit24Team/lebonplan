<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use App\Model\OffersModel;

class OfferController extends AbstractController
{
    private OffersModel $model;

    public function __construct(OffersModel $model)
    {
        $this->model = $model;
    }

    #[Route("/offres", name: "offers", methods: ["GET"])]
    public function show_offers(Request $request): Response
    {
        $page = max(1, (int)$request->query->get('page', 1));
        $perPage = 10;

        $totalOffers = $this->model->count_all_offers();
        $offers = $this->model->get_paginated_offers($page, $perPage);

        $totalPages = ceil($totalOffers / $perPage);

        return $this->render("offer/search.twig", [
            'offers' => $offers,
            'current_page' => $page,
            'total_pages' => $totalPages,
        ]);
    }
    #[Route("/ajouter/offre", name: "add_offer_page", methods: ["GET"])]
    public function add_offer_page(): Response
    {
        return $this->render("offer/add.twig");
    }
    #[Route("/ajouter/offre", name: "add_offer", methods: ["POST"])]
    public function add_offer(
        Request $request,
        SessionInterface $session
    ): Response {
        $manager_id = $session->get("user")["id"];
        //récupère les données du formulaire en json
        $json = $request->getContent();
        $data = json_decode($json, true);
        if ($data === null) {
            return $this->json(["error" => "Invalid JSON"], 400);
        }
        //récupère les données du formulaire
        $title = $data["title"];
        $description = $data["description"];
        $salary = $data["salary"];
        $skills = $data["skills"];
        $start_date = strtotime($data["startDate"]);
        $end_date = strtotime($data["endDate"]);
        $duration = $end_date - $start_date;
        $this->model->create_offer(
            $manager_id,
            $skills,
            $title, 
            $description,
            $salary,
            $data["startDate"],
            $duration
        );

        return $this->redirectToRoute("manager_dashboard");
    }

    #[Route("/api/offers", name: "api_offers", methods: ["GET"])]
    public function get_offer(): JsonResponse
    {
        // Assuming you have a model to handle the database operations
        // $offers = $this->model->getOffers();
        return $this->json([]);
    }
    
}
