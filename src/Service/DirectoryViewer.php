<?php

namespace App\Service;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * DirectoryViewer
 * 
 * Service pour récupérer le contenu d'un dossier
 *
 */
class DirectoryViewer
{
    private $rootDirectory;
    private $applicationDirectory;
    
    /**
     * Constructeur 
     * 
     * @param string $applicationDirectory
     * @param string $rootDirectory
     */
    public function __construct($applicationDirectory, $rootDirectory)
    {
        $this->applicationDirectory = realpath($applicationDirectory);
        $this->rootDirectory = '/'.ltrim($rootDirectory, '/');
    }
    
    /**
     * Retourne le contenu d'un dossier
     * 
     * @param string $directory Répertoire à scanner
     * @return DirectoryIterator[]
     * @throws NotFoundHttpException
     */
    protected function getContent($directory, $type)
    {
        $path = realpath($this->applicationDirectory.$this->rootDirectory.'/'.$directory);
        
        if (!$path) {
            throw new NotFoundHttpException("Le dossier $directory n'existe pas");
        }
        
        $iterator = new \DirectoryIterator($path);
        $content = array();
        
        foreach ($iterator as $fileInfo) {
            $filename = preg_replace('/[^\da-z]/i', '-', $fileInfo->getFilename());
            if ($fileInfo->isDot()) {
                continue;
            } elseif ($type == 'directory' && $fileInfo->isDir()) {
                $content[$filename] = clone $fileInfo;
            } elseif ($type == 'file' && $fileInfo->isFile()) {
                $content[$filename] = clone $fileInfo;
            }
        }
        
        ksort($content);
        
        return $content;
    }
    
    /**
     * Retourne tous les répertoires du dossier
     * 
     * @param type $directory Dossier à scanner
     * @return DirectoryIterator[]
     */
    public function getDirectories($directory)
    {
        return $this->getContent($directory, 'directory');
    }
    
    /**
     * Retourne tous les fichiers du dossier
     * 
     * @param type $directory Dossier à scanner
     * @return DirectoryIterator[]
     */
    public function getFiles($directory)
    {
        return $this->getContent($directory, 'file');
    }
}