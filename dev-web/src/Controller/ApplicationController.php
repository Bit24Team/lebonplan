<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Model\ApplicationModel;

class ApplicationController extends AbstractController
{
    private ApplicationModel $model;

    public function __construct(ApplicationModel $model)
    {
        $this->model = $model;
    }

    #[Route('/offre/{offer_id}/postuler', name: 'apply_offer', methods: ['GET'])]
    public function showApplicationForm(int $offer_id, SessionInterface $session): Response
    {
        $offer = $this->model->getOfferDetails($offer_id);
        
        if (!$offer) {
            throw $this->createNotFoundException('Offre non trouvée');
        }

        $user = $this->getUserFromSession($session); // À adapter selon votre système d'authentification

        if (!$user) {
            return $this->redirectToRoute('login_page');
        }

        if ($this->model->hasAlreadyApplied($offer_id, $user['id'])) {
            $this->addFlash('warning', 'Vous avez déjà postulé à cette offre');
            return $this->redirectToRoute('apply_offer', ['offer_id' => $offer_id]);
        }

        return $this->render('offer/application.twig', [
            'offer_name' => $offer['title'],
            'company_name' => $offer['company_name'],
            'company_id' => $offer['company_id'],
            'start_date' => $offer['start_date'],
            'id' => $offer['id'],
            'description' => $offer['description'],
            'skills' => $offer['skills'],
            'duration' => $this->formatDuration($offer['duration']),
            'salary' => $offer['salary']
        ]);
    }

    #[Route('/offre/{offer_id}/postuler', name: 'submit_application', methods: ['POST'])]
public function submitApplication(int $offer_id, Request $request, SessionInterface $session): Response
{
    $offer = $this->model->getOfferDetails($offer_id);
    
    if (!$offer) {
        throw $this->createNotFoundException('Offre non trouvée');
    }

    $user = $this->getUserFromSession($session);

    if (!$user) {
        return $this->redirectToRoute('login_page');
    }

    if ($this->model->hasAlreadyApplied($offer_id, $user['id'])) {
        $this->addFlash('warning', 'Vous avez déjà postulé à cette offre');
        return $this->redirectToRoute('apply_offer', ['offer_id' => $offer_id]);
    }

    try {
        // Gestion des fichiers uploadés
        $cvFile = $request->files->get('cv');
        $motivationFile = $request->files->get('motivation_letter');

        if (!$cvFile || !$motivationFile) {
            throw new \Exception('Veuillez fournir un CV et une lettre de motivation');
        }

        $uploadDir = $this->getParameter('kernel.project_dir') . '/public/uploads/';
        
        // Plus besoin de convertir en tableau, on passe directement l'objet UploadedFile
        $cvPath = $this->model->uploadFile($cvFile, $uploadDir);
        $motivationPath = $this->model->uploadFile($motivationFile, $uploadDir);

        // Création de la candidature
        $this->model->createApplication(
            $offer_id,
            $user['id'],
            $cvPath,
            $motivationPath
        );

        $this->addFlash('success', 'Votre candidature a bien été envoyée !');
        return $this->redirectToRoute('apply_offer', ['offer_id' => $offer_id]);

    } catch (\Exception $e) {
        $this->addFlash('error', $e->getMessage());
        return $this->redirectToRoute('apply_offer', ['offer_id' => $offer_id]);
    }
}

    private function getUserFromSession(SessionInterface $session): ?array
    {
        // À adapter selon votre système de session
        return $session->get('user');
    }

    private function formatDuration(int $seconds): string
    {
        $months = floor($seconds / (30 * 24 * 60 * 60));
        return $months . ' mois';
    }
}