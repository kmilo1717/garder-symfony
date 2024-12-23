<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class BaseController extends AbstractController
{
    protected $entityManager;
    protected $session;

    public function __construct(EntityManagerInterface $entityManager, SessionInterface $session)
    {
        $this->entityManager = $entityManager;
        $this->session = $session;
    }


    protected function getUser()
    {


        $userData = $this->session->get('user');
        
        if (!$userData) {
            return null;
        }

        $user = $this->entityManager->getRepository(User::class)->find($userData['id']);
        if (!$user) {
            return null;
        }

        return $user;
    }
}
