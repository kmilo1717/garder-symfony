<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Form\RegistrationFormType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;

class AuthController extends AbstractController
{
    private $entityManager;
    private $passwordHasher;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasher $passwordHasher)
{
    $this->entityManager = $entityManager;
    $this->passwordHasher = $passwordHasher;
}

    /**
     * @Route("/login", name="login")
     */
    public function login(Request $request, SessionInterface $session): Response
    {
        if ($request->isMethod('POST')) {
            $username = $request->request->get('username');
            $password = $request->request->get('password');

            // Busca al usuario en la base de datos
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => $username]);

            if ($user && password_verify($password, $user->getPassword())) {
                // Almacena la sesión del usuario
                $session->set('user', [
                    'id' => $user->getId(),
                    'username' => $user->getUsername(),
                    'roles' => $user->getRoles(),
                ]);

                return $this->redirectToRoute('home');
            }

            $this->addFlash('error', 'Credenciales inválidas.');
        }

        return $this->render('auth/login.html.twig');
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout(SessionInterface $session): Response
    {
        $session->invalidate(); // Elimina la sesión del usuario
        return $this->redirectToRoute('login');
    }

    /**
     * @Route("/register", name="register")
     */
    public function register(Request $request)
    {
        // Creamos una nueva instancia de User
        $user = new User();
        
        // Creamos el formulario para el registro
        $form = $this->createForm(RegistrationFormType::class, $user);

        // Procesamos la solicitud del formulario
        $form->handleRequest($request);

        // Si el formulario es válido y se envía
        if ($form->isSubmitted() && $form->isValid()) {
            // Encriptamos la contraseña
            $hashedPassword = $this->passwordHasher->hashPassword($user, $user->getPassword()); // Cambié el método de encodePassword a hashPassword
            $user->setPassword($hashedPassword);
            $user->setRoles(['ROLE_USER']); // Asignamos el rol de usuario

            // Guardamos el usuario en la base de datos
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            // Redirigimos al login después de un registro exitoso
            return $this->redirectToRoute('login');
        }

        // Renderizamos la vista del formulario
        return $this->render('auth/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}