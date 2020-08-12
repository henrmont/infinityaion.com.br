<?php

namespace App\Controller;

use App\Entity\Aion;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use App\Security\LoginAuthenticator;
use DateTime;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    private $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, LoginAuthenticator $authenticator): Response
    {
        return $this->redirectToRoute('site');
        
        try{
            $user = new User();
            $aion = new Aion();
            $form = $this->createForm(RegistrationFormType::class, $user);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManagerUser = $this->getDoctrine()->getManager();
                $chkmail = $entityManagerUser->getRepository(User::class)->findBy([
                    'email'     =>  $request->request->get('registration_form')
                ]);

                $con = $this->getDoctrine()->getConnection();
                $con->beginTransaction();

                if(empty($chkmail)){
                    // encode the plain password
                    $user->setPassword(
                        $passwordEncoder->encodePassword(
                            $user,
                            $form->get('plainPassword')->getData()
                        )
                    );
                    $user->setCoin(0);
                    $user->setTagFeed(false);
                    $user->setTagCoin(false);
                    $user->setTagShop(false);
                    $user->setTagNotify(false);
                    $user->setTagTicket(false);
                    $user->setIsActive(false);
                    $user->setIsReport(false);
                    $user->setIsSuspect(false);
                    $user->setCreatedAt(new \DateTime('now'));
                    $user->setModifiedAt(new \DateTime('now'));


                    $data = $request->request->get('registration_form');

                    $date = new DateTime('now');
                    $date->modify('+3 day');

                    $aion->setName($data['username']);
                    $aion->setMembership(2);
                    $aion->setExpire($date);
                    $aion->setActivated(0);
                    $aion->setPassword(base64_encode(sha1($data['plainPassword'], true)));

                    $entityManagerUser = $this->getDoctrine()->getManager();
                    $entityManagerUser->persist($user);
                    $entityManagerUser->flush();

                    $entityManagerAion = $this->getDoctrine()->getManager('aion');
                    $entityManagerAion->persist($aion);
                    $entityManagerAion->flush();

                    // generate a signed url and email it to the user
                    $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                        (new TemplatedEmail())
                            ->from(new Address('noreply@infinityaion.com.br', 'No Reply'))
                            ->to($user->getEmail())
                            ->subject('Infinity Aion - Registration')
                            ->htmlTemplate('registration/confirmation_email.html.twig')
                    );
                    // do anything else you need here, like send an email

                    $con->commit();

                    $this->addFlash(
                        'success',
                        'Conta criada com sucesso. Verifique seu email para confirmar.'
                    );
                    return $this->redirectToRoute('site');
                } else {
                    $con->rollBack();
                    
                    $this->addFlash(
                        'notice',
                        'Email já registrado.'
                    );
                    return $this->redirectToRoute('site');
                }
            }

            return $this->render('registration/register.html.twig', [
                'registrationForm' => $form->createView(),
            ]);
        }catch(\Exception $e){
            $e->getMessage();
            $this->addFlash(
                'notice',
                'Usuário já registrado.'
            );
            return $this->redirectToRoute('site');
        }
    }

    /**
     * @Route("/verify/email", name="app_verify_email")
     */
    public function verifyUserEmail(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());

            return $this->redirectToRoute('site');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $em = $this->getDoctrine()->getManager();
        $em->getRepository(Aion::class)->enableAccount($user->getUsername());
        $this->addFlash('success', 'Conta ativada com sucesso.');

        return $this->redirectToRoute('shop');
    }
}
