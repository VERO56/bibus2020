<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ForceChangePasswordFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ForceChangePasswordController extends AbstractController
{


    /**
     * @Route("/change-password/{id}", name="app_change_password", defaults={"identifier=null"})
     * @Method("POST")
     * @param Request $request
     * @param AuthenticationUtils $authenticationUtils
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param $identifier
     * @return Response
     */
    public function ForceChangePasswordAction(Request $request, User $user, UserPasswordEncoderInterface $passwordEncoder)
    {
        $form = $this->createForm(ForceChangePasswordFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if(!$passwordEncoder->isPasswordValid($user, $form->get('newpassword')->getData())) {

                $newpassword = $passwordEncoder->encodePassword(
                    $user,
                    $form->get('newpassword')->getData()
                );
                $user->setPassword($newpassword);
                $user->setPasswordChangedAt(new \DateTime());

                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

                $this->addFlash('success', 'Le mot de passe est bien changé');

                # Redirection sur la page de connexion
                return $this->redirectToRoute('app_logout');
            }
            else {
                $this->addFlash('warning', 'Le nouveau mot de passe ne doit pas être identique au précédent');
                return $this->redirectToRoute('app_change_password', ["id"=>$user->getId()]);
            }
        }
        return $this->render(
            'change_password/change_password.html.twig',
            array('form' => $form->createView())
        );
    }
}
