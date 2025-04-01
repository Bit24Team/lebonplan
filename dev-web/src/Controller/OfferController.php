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
    public function show_offers(): Response
    {
        return $this->render("offer/search.twig");
    }

    #[Route("/offre/{offer_id}", name: "offer", methods: ["GET"])]
    public function show_offer(int $offer_id): Response
    {
        return $this->render("offer/offer.twig");
    }
    #[Route("/offre/{offer_id}", name: "submit_application", methods: ["POST"])]
    public function submit_application(
        int $offer_id,
        Request $request,
        SessionInterface $session
    ): Response {
        return $this->redirectToRoute("offer", ["offer_id" => $offer_id]);
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
        $skills = explode(",", $skills);
        $this->model->create_offer(
            $manager_id,
            $skills,
            $title, 
            $description,
            $salary,
            $data["startDate"],
            $duration
        );

        return $this->redirectToRoute("account");
    }

    #[Route("/api/offers", name: "api_offers", methods: ["GET"])]
    public function get_offer(): JsonResponse
    {
        // Assuming you have a model to handle the database operations
        // $offers = $this->model->getOffers();
        return $this->json([]);
    }
}
