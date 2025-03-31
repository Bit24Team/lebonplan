<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
}