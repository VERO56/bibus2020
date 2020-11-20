<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Service d'importation du CSV des utilisateurs
 *
 */
class CsvImport
{
    private $userManager;
    
    private $doctrineManager;
    
    private $uploadDir;
    
    protected $em;
    protected $passwordEncoder;

    public function __construct($entityManager, $passwordEncoder)
    {
        $this->em = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
    }
   
   
    /**
     * Upload directory
     * 
     * @return string
     */
    function getUploadDir()
    {
        return $this->uploadDir;
    }

    /**
     * Called by service container
     * 
     * @param string $uploadDir
     */
    function setUploadDir($uploadDir)
    {
        $this->uploadDir = $uploadDir;
    }
   

    
    /**
     * User manager
     * 
     * @return UserInterface
     */
    function getUserManager()
    {
        return $this->userManager;
    }
    
    /**
     * Called by service container
     * 
     * @param UserInterface $userManager
     */
    public function setUserManager(UserInterface $userManager)
    {
        $this->userManager = $userManager;
    }
    
    public function importFile(UploadedFile $file)
    {
        $path = realpath($this->getUploadDir());
        $file->move($path, $file->getClientOriginalName());
        $handle = fopen($path.'/'.$file->getClientOriginalName(), "r");
        $errors = array();

        $lineNumber = 0;
        while ($row = fgetcsv($handle)) {
            $lineNumber++;
            if (!$this->importUser($row)) {
                $errors[] = array(
                    'severity' => 'warning',
                    'message' => "La ligne $lineNumber n'a pas pu être importée"
                );
            }
        }
        
        $this->em->flush();
        return $errors;
    }
   
    
    protected function importUser( $data)
    {
        /** @var User $user */
        $user = $this->getUser($data);
        if (!$user) {
            return false;
        }
        
        $user->setIdentifier($data[0]);
        
        
        $password = $data[1];
                $encoded = $this->passwordEncoder->encodePassword($user, $password);

        $user->setPassword($encoded);
        $user->setEmail($data[2]);
        $user->setIsActive(true);

     
        $this->em->persist($user);
        
        return true;
    }
    
    protected function getUser($data)
    {
        // We need 3 non empty values
        if (count(array_filter($data)) !== count($data) || count($data) != 3) {
            return false;
        }
        
        $user = $this->em->getRepository(User::class)
            ->findOneBy(array('identifier' => $data[0]));
        
        return $user ?: new User();
    } 
}
