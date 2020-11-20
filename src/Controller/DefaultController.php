<?php

namespace App\Controller;

use App\Entity\Config;
use App\Service\DirectoryViewer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class DefaultController extends AbstractController
{
   
    /**
     * @Template()
     */
    public function indexAction()
    {
        return array(
            'horairesNavettesDir' => $this->getParameter('horaires_dir'),
            'planLignesDir' => $this->getParameter('lignes_dir'),
            'planDeviationsDir' => $this->getParameter('deviation_dir'),
            'informationDir' => $this->getParameter('information_dir')
        );
    }
    
    /**
     * @Template()
     * @param type $path Chemin du dossier
     */
    public function walkDirAction($path, DirectoryViewer $directoryViewer)
    {
        if ($path == 'actus') {
            return $this->redirect('http://109.7.10.86/echangesbibus/');
        }
     
        $directories = $directoryViewer->getDirectories($path);
        $files = $directoryViewer->getFiles($path);
        $dirs = explode('/', $path) ? : $path;
        $currentDir = array_pop($dirs);
        
        return array(
            'directories' => $directories,
            'files' => $files,
            'previousDir' => join('/', $dirs),
            'currentDir' => $currentDir,
            'path' => $path
        );
    }
    
    /**
     * Télécharge le fichier
     * 
     * @param type $file Chemin vers le fichier
     * @return Response
     * @throws NotFoundHttpException
     */
    public function downloadFileAction($file)
    {
        $documentsDirectory = $this->getParameter('kernel.root_dir').'/../'.
            $this->getParameter('documents_directory').'/';
        $filePath = realpath($documentsDirectory.$file);
        
        if (!$filePath || !file_exists($filePath)) {
            throw new NotFoundHttpException("Le fichier $file n'a pas été trouvé");
        }
        
        $headers = array(
            'Content-type' => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="'.basename($file).'"',
            'Content-Length' => filesize($filePath)
        );
        
        return new Response(file_get_contents($filePath), 200, $headers);
    }
    
    /**
     * @Template()
     */
    public function helpAction()
    {
        return array();
    }
    
    /**
     * @return RedirectResponse
     */
    public function redirectEchoAction()
    {
        $echoFormLink = $this->getDoctrine()->getManager()
                ->getRepository(Config::class)
                ->findOneBy(array('name' => 'echo_form_link'));
        
        return new RedirectResponse(
            $echoFormLink->getValue() ? 
               $echoFormLink->getValue() : 
                $this->getParameter('echo_form_link'), 
            301
        );
    }
}
