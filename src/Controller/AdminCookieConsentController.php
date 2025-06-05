<?php

namespace App\Controller;

use App\Entity\CookieConsent;
use App\Repository\CookieConsentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminCookieConsentController extends AbstractController
{
    #[Route('/admin/cookie-consent/capture', name: 'admin_cookie_consent_capture', methods: ['POST'])]
    public function capture(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$this->isGranted('ROLE_ADMIN')) {
            return new JsonResponse(['error' => 'Accès non autorisé.'], 403);
        }

        if (!isset($data['consent'], $data['datetime'])) {
            return new JsonResponse(['error' => 'Données incomplètes.'], 400);
        }

        $consent = new CookieConsent();
        $consent->setType($data['consent']);
        $consent->setCreatedAt(new \DateTimeImmutable($data['datetime'] ?? 'now'));

        $prefs = $data['preferences'] ?? null;

        if (is_array($prefs)) {
            $consent->setStatistiques((bool) ($prefs['statistiques'] ?? false));
            $consent->setMarketing((bool) ($prefs['marketing'] ?? false));
        } else {
            $consent->setStatistiques(false);
            $consent->setMarketing(false);
        }

        $em->persist($consent);
        $em->flush();

        return new JsonResponse(['success' => true]);
    }

    #[Route('/admin/cookie-consent/store', name: 'admin_cookie_consent_store', methods: ['POST'])]
    public function store(Request $request, EntityManagerInterface $em): JsonResponse
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return new JsonResponse(['error' => 'Non autorisé'], 403);
        }

        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            return new JsonResponse(['error' => 'Données non valides'], 400);
        }

        $consent = new CookieConsent();
        $consent->setType($data['type'] ?? 'personnalized');
        $consent->setStatistiques((bool) ($data['statistiques'] ?? false));
        $consent->setMarketing((bool) ($data['marketing'] ?? false));
        $consent->setCreatedAt(new \DateTimeImmutable());

        $em->persist($consent);
        $em->flush();

        return new JsonResponse(['success' => true]);
    }
}
