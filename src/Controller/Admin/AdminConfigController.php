<?php

namespace App\Controller\Admin;

use App\Entity\Config;
use App\Form\ConfigType;
use App\Repository\ConfigRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class AdminConfigController extends AbstractController
{
    /**
     * @Template()
     * @Route("/admin/config", name="config_index", methods={"GET"})
     */
    public function index(ConfigRepository $configRepository): Response
    {
        
        return $this->render('admin/config/index.html.twig', [
            'configs' => $configRepository->findAll(),
            
        ]);
      
        
    }
    /**
     * @Template()
     * @Route("/admin/config/{id}/edit", name="config_edit", methods={"GET","POST"})
     */
    public function editAction(Request $request, Config $config): Response
    {
        $form = $this->createForm(ConfigType::class, $config);

        $form->handleRequest($request);
        $session = new Session();
        
        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            
            foreach ($form->getData() as $name => $value) {
                /* @var $config Config */
                $config = $manager->getRepository(Config::class)
                    ->findOneBy(array('name' => $name));
                
                if ($config) {
                    $config->setValue($value);
                } else {
                    $config = new Config();
                    $config->setName($name);
                    $config->setValue($value);
                }
                $manager->persist($config);
            }
            $manager->flush();
            return $this->redirectToRoute('config_index');
            $session->getFlashBag()->add('success', "La configuration a été sauvegardée");
        } else {
            $session->getFlashBag()->add('error', "Erreur lors de la sauvegarde (formulaire incorrect)");
        
        }
        return $this->render('admin/config/edit.html.twig', [
            'config' => $config,
            'form' => $form->createView(),
        ]);
    }

   
}
