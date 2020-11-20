<?php

namespace App\Util;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ConstructDirectoryViewer
{
    private $rootDirectory;
    private $applicationDirectory;
    
    /**
     * Constructeur 
     * 
     * @param string $applicationDirectory
     * @param string $rootDirectory
     */
    public function __construct(string $applicationDirectory, string $rootDirectory)
    {
        $this->applicationDirectory = realpath($applicationDirectory);
        $this->rootDirectory = '/'.ltrim($rootDirectory, '/');
    }
}