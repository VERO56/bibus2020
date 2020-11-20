<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use App\Form\CsvImportType;
use App\Service\CsvImport;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;

class AdminImportController extends AbstractController
{
    public static function getSubscribedServices()
{
    return array_merge(
        parent::getSubscribedServices(),
        [
            'app.csv_import' => CsvImport::class,
        ]
    );
}
    /**
     * @Template()
     * @Route("/admin/import", name="import_index", methods={"GET", "POST"})
     */
    public function indexAction(Request $request)
    {
        $form = $this->createForm(CsvImportType::class);
        $form->handleRequest($request);
        $session = new Session();
        
        if ($form->isSubmitted() && $form->isValid()) {
          //  dd($form['csvfile']->getData());
            /* @var $csvImportService \App\Service\CsvImport */
            $csvImportService = $this->get('app.csv_import');
            $errors = $csvImportService->importFile($form['csvfile']->getData());
            
            foreach ($errors as $error) {
                $session->getFlashBag()->add($error['severity'], $error['message']);
            }
            $session->getFlashBag()->add('success', "L'importation est terminée");
        } else {
            $session->getFlashBag()->add('error', "Erreur lors de l'importation (formulaire incorrect)");
        }
        
        
        return $this->render('admin/import/index.html.twig', [
            
            'form' => $form->createView(),
        ]);
    }
    
    public function processAction(Request $request)
    {
        $form = $this->createForm(CsvImportType::class);
        $form->handleRequest($request);
        $session = new Session();
        
        if ($form->isValid()) {
            /* @var $csvImportService \App\Service\CsvImport */
            $csvImportService = $this->get('app.csv_import');
            $errors = $csvImportService->importFile($form['csvfile']->getData());
            
            foreach ($errors as $error) {
                $session->getFlashBag()->add($error['severity'], $error['message']);
            }
            $session->getFlashBag()->add('success', "L'importation est terminée");
        } else {
            $session->getFlashBag()->add('error', "Erreur lors de l'importation (formulaire incorrect)");
        }
        
        return new RedirectResponse($this->generateUrl('oziolab_bibus_admin_import'));
    }
}
