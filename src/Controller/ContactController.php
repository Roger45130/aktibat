<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function contact(
        Request $request,
        EntityManagerInterface $em,
        MailerInterface $mailer
    ): Response {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contact->setCreatedAt(new \DateTimeImmutable());
            $em->persist($contact);
            $em->flush();

            $email = (new Email())
                ->from($contact->getEmail())
                ->to('contact@aktibat.com')
                ->subject('Nouveau message de contact')
                ->html('<p>Nom : ' . $contact->getFullname() . '</p><p>Email : ' . $contact->getEmail() . '</p><p>Message : ' . $contact->getMessage() . '</p>');

            $mailer->send($email);

            $this->addFlash('success', 'Message envoyé avec succès.');
            return $this->redirectToRoute('app_contact');
        }

        return $this->render('app/contact.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
