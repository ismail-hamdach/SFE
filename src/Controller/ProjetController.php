<?php

namespace App\Controller;

use App\Entity\Projet;
use App\Form\ProjetType;
use App\Repository\ProjetRepository;
use App\Repository\TacheRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// /**
//  * @Route("/admin/projet")
//  */
class ProjetController extends AbstractController
{
    /**
     * @Route("/admin/projet/", name="projet_index", methods={"GET", "POST"})
     * @Route("/gerant/projet/", name="projet_index_gerant", methods={"GET", "POST"})
     * @Route("/client/projet/", name="projet_index_client", methods={"GET", "POST"})
     */
    public function index(Request $request, ProjetRepository $projetRepository, TacheRepository $tacheRepository): Response
    {
        $query = ['id' => null];
        $routeName = $request->get('_route');
        $form = $this->createFormBuilder($query)
            ->add('id', TextType::class, [
                'label' => 'Recherche',
                'attr' => ['placeholder' => 'Réfernece']
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $query = $form->getData();
            return $this->redirectToRoute($routeName == 'projet_index' ? 'projet_search' : ($routeName == 'projet_index_gerant' ? 'projet_search_gerant' : 'projet_search_client'), ['id' => $query['id']]);
        }

        switch($routeName){
            case 'projet_index_gerant' : 
                $projets = $projetRepository->findByResponsable($this->getUser());
                foreach($projets as $projet)
                    $avancement[$projet->getId()] = $tacheRepository->AvancementProjet($projet);
                $type = 'gerant';
                break;
            case 'projet_index' : 
                $projets =  $projetRepository->findAll();
                foreach($projets as $projet)
                    $avancement[$projet->getId()] = $tacheRepository->AvancementProjet($projet);
                $type = 'admin';
                break;
            case 'projet_index_client' : 
                $projets = $projetRepository->findBy(['client' => $this->getUser()]);
                foreach($projets as $projet)
                    $avancement[$projet->getId()] = $tacheRepository->AvancementProjet($projet);
                $type = 'client';
                break;
        }

        
        return $this->render('projet/index.html.twig', [
            'projets' => $projets,
            'avancement' => $avancement,
            'form' => $form->createView(),
            'type' => $type,
        ]);
    }

    /**
     * @Route("/admin/projet/result/{id}", name="projet_search", methods={"GET"})
     * @Route("/gerant/projet/result/{id}/", name="projet_search_gerant", methods={"GET"})
     * @Route("/client/projet/result/{id}/", name="projet_search_client", methods={"GET"})
     */
    public function search(Projet $projet, Request $request, TacheRepository $tacheRepository): Response
    {   
        $routeName = $request->get('_route');
        switch ($routeName) {
            case 'projet_search_gerant':
                $type = 'gerant';
                if($projet && $projet->getResponsable()->getId() != $this->getUser()->getId())
                    $projet = null;
                break;
             case 'projet_search_client':
                $type = 'client';
                if($projet && $projet->getClient()->getId() != $this->getUser()->getId())
                    $projet = null;
                break;
             case 'projet_search_gerant':
                    $type = 'admin';
                break;
            
            default:
                    $type = null;
                    $projet = null;
                break;
        }
        if($projet)
            $avancement = $tacheRepository->AvancementProjet($projet);
        
        return $this->render('projet/show.html.twig', [
            'projet' => $projet,
            'type' =>  $type,
            'avancement' => $avancement,
        ]);
    }

    /**
     * @Route("/admin/projet/new", name="projet_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $projet = new Projet();
        $form = $this->createForm(ProjetType::class, $projet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $projet->setDateCreation(new \DateTime('now'));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($projet);
            $entityManager->flush();

            return $this->redirectToRoute('projet_index');
        }

        return $this->render('projet/new.html.twig', [
            'projet' => $projet,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/projet/{id}", name="projet_show", methods={"GET"})
     * @Route("/gerant/projet/{id}", name="projet_show_gerant", methods={"GET"})
     * @Route("/client/projet/{id}", name="projet_show_client", methods={"GET"})
     */
    public function show(Projet $projet, Request $request, TacheRepository $tacheRepository): Response
    {
        $routeName = $request->get('_route');
        switch ($routeName) {
            case 'projet_show':
                $type = 'admin';
                break;
            case 'projet_show_gerant':
                $type = 'gerant';
                $projet = $projet->getResponsable() == $this->getUser() ? $projet : null;
                break;
            case 'projet_show_client':
                $type = 'client';
                $projet = $projet->getClient() == $this->getUser() ? $projet : null;
                break;
            default:
                $projet = null;
                break;
        }
        if($projet)
            $avancement = $tacheRepository->AvancementProjet($projet);
        return $this->render('projet/show.html.twig', [
            'projet' => $projet,
            'type' => $type,
            'avancement' => $avancement,
        ]);
    }

    /**
     * @Route("/admin/projet/{id}/edit", name="projet_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Projet $projet): Response
    {
        $form = $this->createForm(ProjetType::class, $projet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('projet_index');
        }

        return $this->render('projet/edit.html.twig', [
            'projet' => $projet,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/projet/{id}", name="projet_delete", methods={"POST"})
     */
    public function delete(Request $request, Projet $projet): Response
    {
        if ($this->isCsrfTokenValid('delete'.$projet->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($projet);
            $entityManager->flush();
        }

        return $this->redirectToRoute('projet_index');
    }
}
