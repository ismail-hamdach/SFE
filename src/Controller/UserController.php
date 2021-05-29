<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\ClientType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// /**
//  * @Route("/admin/user", name="admin_user")
//  */
class UserController extends AbstractController
{

    private $passChanged;

    public function __construct()
    {
        $this->passChanged = ["id" => null, "password" => null];
    }
    /**
     * @Route("/admin/user/", name="user_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findByRole("ROLE_EMPLOYE"),
        ]);
    }

    /**
     * @Route("/admin/user/new", name="user_new", methods={"GET","POST"})
     */
    public function new(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $passwordEncoder = $passwordEncoder->encodePassword(
                $user,
                $user->getPassword()
            );
            $user->setPassword($passwordEncoder);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/user/{id}", name="user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/admin/user/{id}/edit", name="user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        if($this->passChanged ["id"] != $user->getId() ){
            $this->passChanged = ["id" => $user->getId() , "password" => $user -> getPassword()];
        }
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if($this->passChanged ['password'] != $user->getPassword()){
                 $passwordEncoder = $passwordEncoder->encodePassword(
                     $user,
                     $user->getPassword()
                 );
                 $user->setPassword($passwordEncoder);
                 $this->passChanged = ["id" => null, "password" => null];
            }
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    
        

        
        

    /**
     * @Route("/admin/user/{id}", name="user_delete", methods={"POST"})
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_index');
    }


    //-------------------------------- Clients Section --------------------------------------------


    /**
     * @Route("/admin/client/", name="client_index", methods={"GET"})
     */
    public function indexClient(UserRepository $userRepository): Response
    {
        return $this->render('client/index.html.twig', [
            'users' => $userRepository->findByRole("ROLE_CLIENT"),
        ]);
    }

    /**
     * @Route("/admin/client/new", name="client_new", methods={"GET","POST"})
     */
    public function newClient(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();
        $form = $this->createForm(ClientType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $user->setRoles(['ROLE_CLIENT']);
            $passwordEncoder = $passwordEncoder->encodePassword(
                $user,
                $user->getPassword()
            );
            $user->setPassword($passwordEncoder);
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('client_index');
        }

        return $this->render('client/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/client/{id}", name="client_show", methods={"GET"})
     */
    public function showClient(User $user): Response
    {
        return $this->render('client/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/admin/client/{id}/edit", name="client_edit", methods={"GET","POST"})
     */
    public function editClient(Request $request, User $user, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        
        if($this->passChanged ["id"] != $user->getId() ){
            $this->passChanged = ["id" => $user->getId() , "password" => $user -> getPassword()];
        }

        $form = $this->createForm(ClientType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

           if($this->passChanged ['password'] != $user->getPassword()){
                $passwordEncoder = $passwordEncoder->encodePassword(
                    $user,
                    $user->getPassword()
                );
                $user->setPassword($passwordEncoder);
                $this->passChanged = ["id" => null, "password" => null];
           }

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('client_index');
        }

        return $this->render('client/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/client/{id}", name="client_delete", methods={"POST"})
     */
    public function deleteClient(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('client_index');
    }
}
