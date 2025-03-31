<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Model\OfferModel;

class OfferController extends AbstractController
{
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
}