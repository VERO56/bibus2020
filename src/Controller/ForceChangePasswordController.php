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
       // $user =$this->getUser();
        //$user = new User();
        $form = $this->createForm(ForceChangePasswordFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
           /* try {
                $user = $this->getDoctrine()->getRepository(User::class)->find($identifier);
            } catch (ExceptionInterface $e) {
                $this->addFlash('danger', "Ce matricule n'existe pas.");
            }*/

            //Recuperer le nouveau mot de passe tapé par l'utilisateur
            //$newpassword = $passwordEncoder->encodePassword($user, $user->getNewpassword());
            
            
           /* $newpassword = $passwordEncoder->encodePassword(
                $user,
                $form->get('newpassword')->getData()
            );*/
        

            //$user->setPassword($newpassword);
            //$user->setNewpassword($newpassword);
            
            //recuperer l'ancien mot de passe dans la base de donnéees
           // $oldpassword = $user->getPassword();
          
                $newpassword = $passwordEncoder->encodePassword(
                    $user,
                    $form->get('newpassword')->getData()
                );
                $user->setPassword($newpassword);
            

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Le mot de passe est bien changé');
        
            # Redirection sur la page de connexion
            return $this->redirectToRoute('app_logout');
        }
        return $this->render(
            'change_password/change_password.html.twig',
            array('form' => $form->createView())
        );
    }
}
