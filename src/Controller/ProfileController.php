<?php

namespace App\Controller;

use App\Form\AccountFormType;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Twig\Environment;


/**
 * @Route("/{_locale<%app.supported_locales%>}/profile")
 */
class ProfileController extends AbstractController
{
    private $entityManager;
    private $twig;
    private $emailVerifier;

    public function __construct(EntityManagerInterface $entityManager, Environment $twig, EmailVerifier $emailVerifier)
    {
        $this->entityManager = $entityManager;
        $this->twig = $twig;
        $this->emailVerifier = $emailVerifier;
    }

    /**
     * @Route("/", name="profile")
     */
    public function index()
    {
        return $this->redirectToRoute('profile_files');
    }

    /**
     * @Route("/account", name="profile_account")
     * @param UserInterface $user
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    public function account(UserInterface $user, Request $request, UserPasswordEncoderInterface $passwordEncoder):Response
    {
        $form = $this->createForm(AccountFormType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $form->get('plainPassword')->getData();
            if ($password):
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $password
                    )
                );
            endif;
            $newEmail = $form->get('email')->getData();
            $oldEmail = $user->getUsername();
            if ( $oldEmail !== $newEmail) {
                $user->setEmail($newEmail);
                $user->setIsVerified(false);
                $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                    (new TemplatedEmail())
                        ->from(new Address('register@test.localhost', 'weSub'))
                        ->to($newEmail)
                        ->subject('Please Confirm your Email')
                        ->htmlTemplate('registration/confirmation_email.html.twig')
                );
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
        }
        return $this->render('profile/account/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
