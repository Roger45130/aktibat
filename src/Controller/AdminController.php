<?php

namespace App\Controller;

use App\Repository\BlogRepository;
use App\Repository\CommentaireRepository;
use App\Repository\ContactRepository;
use App\Repository\CookieConsentRepository;
use App\Repository\UserRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin_index')]
    public function index(
        Request $request,
        UserRepository $userRepository,
        ContactRepository $contactRepository,
        BlogRepository $blogRepository,
        CommentaireRepository $commentaireRepository,
        CookieConsentRepository $cookieConsentRepository
    ): Response {
        // Traitement des dates de filtre
        $startDate = $request->query->get('start_date');
        $endDate = $request->query->get('end_date');

        if (!$startDate || !$endDate) {
            $start = new DateTime('-30 days');
            $end = new DateTime('now');
        } else {
            $start = DateTime::createFromFormat('Y-m-d', $startDate);
            $end = DateTime::createFromFormat('Y-m-d', $endDate);
            if (!$start || !$end) {
                $this->addFlash('error', 'Format de date invalide.');
                return $this->redirectToRoute('admin_index');
            }
            $end->setTime(23, 59, 59);
        }

        // Données filtrées par période
        $usersCount = $userRepository->countCreatedBetween($start, $end);
        $contactsCount = $contactRepository->countBetweenDates($start, $end);
        $blogsCount = $blogRepository->countCreatedBetween($start, $end);
        $commentsCount = $commentaireRepository->countBetweenDates($start, $end);

        // Consentements cookies
        $totalConsent = $cookieConsentRepository->countBetweenDates($start, $end);
        $statConsent = $cookieConsentRepository->countStatistiquesBetweenDates($start, $end);
        $marketingConsent = $cookieConsentRepository->countMarketingBetweenDates($start, $end);
        $refusedConsent = $cookieConsentRepository->countRefusedBetweenDates($start, $end);

        return $this->render('admin/index.html.twig', [
            'usersCount' => $usersCount,
            'contactsCount' => $contactsCount,
            'blogsCount' => $blogsCount,
            'commentsCount' => $commentsCount,
            'totalConsent' => $totalConsent,
            'statConsent' => $statConsent,
            'marketingConsent' => $marketingConsent,
            'refusedConsent' => $refusedConsent,
            'allCookiesCount' => $totalConsent,
            'statsCookiesCount' => $statConsent,
            'marketingCookiesCount' => $marketingConsent,
            'noCookiesCount' => $refusedConsent,
            'start_date' => $start->format('Y-m-d'),
            'end_date' => $end->format('Y-m-d'),
        ]);
    }
}
