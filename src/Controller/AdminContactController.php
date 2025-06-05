<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/contact', name: 'admin_contact_')]
class AdminContactController extends AbstractController
{
    /**
     * Affiche tous les messages de contact dans l'interface d'administration.
     */
    #[Route('/', name: 'index')]
    public function index(ContactRepository $contactRepository): Response
    {
        // Récupérer tous les messages triés par date de création décroissante
        $contacts = $contactRepository->findBy([], ['createdAt' => 'DESC']);

        // Rendu de la vue admin avec tous les messages
        return $this->render('admin_contact/index.html.twig', [
            'contacts' => $contacts,
        ]);
    }

    /**
     * Affiche un message spécifique dans un formulaire prérempli (lecture seule).
     */
    #[Route('/voir/{id}', name: 'voir', methods: ['GET'])]
    public function voir(Contact $contact): Response
    {
        // Rendu d’un formulaire prérempli pour lire le contenu complet d’un message
        return $this->render('admin_contact/voir.html.twig', [
            'contact' => $contact,
        ]);
    }

    /**
     * Exporte tous les messages de contact en fichier PDF, avec possibilité d’impression.
     */
    #[Route('/export/pdf', name: 'export_pdf', methods: ['GET'])]
    public function exportPdf(ContactRepository $contactRepository): Response
    {
        // Récupération des messages
        $contacts = $contactRepository->findBy([], ['createdAt' => 'DESC']);

        // Configuration Dompdf
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        $dompdf = new Dompdf($pdfOptions);

        // Génération du HTML via un template Twig
        $html = $this->renderView('admin_contact/export_pdf.html.twig', [
            'contacts' => $contacts
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Récupération du contenu binaire du PDF
        $pdfOutput = $dompdf->output();

        // Envoi dans une réponse Symfony
        return new Response(
            $pdfOutput,
            Response::HTTP_OK,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="contacts_' . date('d-m-Y_H-i') . '.pdf"',
            ]
        );
    }
}
