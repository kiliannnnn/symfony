<?php

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Email;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact')]
    public function contact(Request $request, MailerInterface $mailer): Response
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $email = (new Email())
                ->from($data['email'])
                ->to($this->getParameter('mailer_from') ?? 'no-reply@example.com')
                ->subject('[Contact] ' . $data['subject'])
                ->text(sprintf("From: %s (%s)\n\n%s", $data['name'] ?? 'Anonymous', $data['email'], $data['message']))
            ;

            $mailer->send($email);

            $this->addFlash('success', 'Votre message a bien été envoyé. Nous vous répondrons rapidement.');
            return $this->redirectToRoute('contact');
        }

        return $this->render('contact/contact.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
