<?php

namespace App\Controller;

use App\Form\MotPassType;
use App\Form\ProfileType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ProfileController extends AbstractController
{
    /**
     * @Route("profile", name="profile")
     * @Route("admin/profile", name="profile_admin")
     * @Route("employe/profile", name="profile_employe")
     * @Route("client/profile", name="profile_client")
     */
    public function index(): Response
    {
        return $this->render('profile/index.html.twig');
    }

    /**
     * @Route("profile/mode", name="profile_mode")
     */
    public function changerMode(): Response
    {
        return $this->render('profile/profileMode.html.twig');
    }
    
   /**
     * @Route("admin/profile/edit", name="edit_profile")
     * @Route("employe/profile/edit", name="edit_profile_employe")
     * @Route("client/profile/edit", name="edit_profile_client")
     */
    public function edit(Request $request): Response
    {
        
        $form = $this->createForm(ProfileType::class, $this->getUser());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $routeName = $request->get('_route');
            $this->getDoctrine()->getManager()->flush();
            switch ($routeName) {
                case 'edit_profile':
                    return $this->redirectToRoute('profile_admin');
                    break;
                case 'edit_profile_employe':
                    return $this->redirectToRoute('profile_employe');
                    break;
                case 'edit_profile_client':
                    return $this->redirectToRoute('profile_client');
                    break;
                default:
                    return $this->redirectToRoute('app_login');
                    break;
            }
           
        }

        return $this->render('profile/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("profile/password", name="changer_mot_passe")
     */
    public function passwordChanging(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response{
        $password = ["oldPassword" => '', "newPassword" => '', "newPassword2" => '', ];
        $form = $this->createForm(MotPassType::class, $password);
        $form->handleRequest($request);
        $error = null;
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $form->getData();
            $user = $this->getUser();
            $match = $passwordEncoder->isPasswordValid($user, $password["oldPassword"]);
            if ($match) {
                $passwordEncoder = $passwordEncoder->encodePassword(
                    $user,
                    $password["newPassword"]
                );
                $user->setPassword($passwordEncoder);
                $this->getDoctrine()->getManager()->flush();
                
            
                
                return $this->redirectToRoute('app_logout');
                       
            }else{
                $error = "L'encien mot de passe n'est pas";
            }
            
           
        }
        return $this->render('profile/edit.html.twig', [
            'form' => $form->createView(),
            'erreur' => $error,
        ]);
    }

    /**
     * @Route("redirection", name="user_redirection")
     */
    public function redirection(): Response
    {
        switch ($this->getUser()->getRoles()[0]) {
            case 'ROLE_ADMIN':
                return $this->redirectToRoute('user_index');
                break;
             case 'ROLE_GERANT':
                return $this->redirectToRoute('projet_index_gerant');
                break;
             case 'ROLE_EMPLOYE':
                return $this->redirectToRoute('tache_index_employe');
                break;
             case 'ROLE_CLIENT':
                return $this->redirectToRoute('projet_index_client');
                break;
            
            default:
                return $this->redirectToRoute('app_login');
                break;
        }
        return $this->render('profile/index.html.twig');
    }
}
