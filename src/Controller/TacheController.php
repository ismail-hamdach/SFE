<?php

namespace App\Controller;

use App\Entity\Tache;
use App\Form\TacheType;
use App\Repository\TacheRepository;
use App\Repository\ProjetRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// /**
//  * @Route("/tache")
//  */
class TacheController extends AbstractController
{
    /**
     * @Route("/gerant/tache/index/{idp}", name="tache_index", methods={"GET"})
     * @Route("/employe/tache/index/", name="tache_index_employe", methods={"GET"})
     */
    public function index(Request $request, TacheRepository $tacheRepository, ProjetRepository $projetRepository): Response
    {
        $routeName = $request->get('_route');
        if($routeName == 'tache_index'){
            $projet = $projetRepository->find($request->get('idp'));
            if($projet->getResponsable()->getId() == $this->getUser()->getId()){
                $taches = $tacheRepository->findByProjet($projet);
            }
            else{
                $taches = null;
            }
            $type = 'gerant';
        }else{
            $taches = $tacheRepository->findBy(['employe' => $this->getUser()], ['etat' => 'ASC', 'dateCreation' => 'DESC']);
            $type = 'employe';
        }
        if (!isset($projet)) {
            $projet = null;
        }
        
        
        return $this->render('tache/index.html.twig', [
            'taches' => $taches,
            'projet' => $projet ,
            'type' => $type,
        ]);
    }

    /**
     * @Route("/gerant/tache/new/{idp}", name="tache_new", methods={"GET","POST"})
     */
    public function new(Request $request, ProjetRepository $projetRepository): Response
    {
        $projet = $projetRepository->find($request->get('idp'));
        if($projet && $projet->getResponsable()->getId() == $this->getUser()->getId()){
            // $choices = [];
            // $projets = $projetRepository->findByResponsable($this->getUser());
            // foreach($projets as $projet){
            //     $choices [] = [$projet->getTitre() => $projet];
            // }
            $tache = new Tache();
            $form = $this->createFormBuilder($tache)
                ->add('titre')
                ->add('description', TextareaType::class)
                // ->add('etat', ChoiceType::class, [
                //     'choices' => [
                //         'En cours' => false,
                //         'Complete' => true,
                //     ]
                // ])
                ->add('duree', null, [
                    'label' => 'Durée'
                ])
                ->add('employe', null, [
                    'label' => 'Afféctué à '
                ])
                // ->add('projet', ChoiceType::class, [
                //     'choices' => [
                //         $projet -> getTitre() => $projet
                //     ]
                // ])
                ->getForm();

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid() ) {
                $tache->setProjet($projet);
                $tache->setEtat(false);
                $tache->setDateCreation(new \DateTime('now'));
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($tache);
                $entityManager->flush();

                return $this->redirectToRoute('tache_index', ['idp' => $projet->getId()]);
            }
        }else{
            return $this->redirectToRoute('projet_index_gerant');
        }

        return $this->render('tache/new.html.twig', [
            'tache' => $tache,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("gerant/tache/show/{id}", name="tache_show", methods={"GET", "POST"})
     * @Route("employe/tache/show/{id}", name="tache_show_employe", methods={"GET", "POST"})
     */
    public function show(Tache $tache, ProjetRepository $projetRepository, Request $request): Response
    {
        $routeName = $request->get('_route');
        if($routeName == 'tache_show'){
            $type = 'gerant';
            $projet = $tache->getProjet();
            if($tache->getProjet()->getResponsable()->getId() == $this->getUser()->getId() && $projet == $tache->getProjet()){
                return $this->render('tache/show.html.twig', [
                    'tache' => $tache,
                    'type' => $type,
                ]);
            }else{
                return $this->render('tache/show.html.twig', [
                    'tache' => null,
                    'type' => $type,
                ]);
            }
        }else{
            $type = 'employe';
            if($tache->getEmploye() == $this->getUser()){
                return $this->render('tache/show.html.twig', [
                    'tache' => $tache,
                    'type' => $type,
                ]);
            }else{
                return $this->render('tache/show.html.twig', [
                    'tache' => null,
                    'type' => $type,
                ]);
            }
        }
        
    }

    /**
     * @Route("gerant/tache/{id}/edit/{idp}", name="tache_edit", methods={"GET","POST"})
     * @Route("gerant/tache/{id}/edit/", name="tache_edit_employe", methods={"GET","POST"})
     */
    public function edit(Request $request, Tache $tache): Response
    {
        $routeName = $request->get('_route');
        if($routeName == 'tache_edit'){
            $form = $this->createFormBuilder($tache)
                ->add('titre')
                ->add('description', TextareaType::class)
                ->add('etat', ChoiceType::class, [
                    'choices' => [
                        'En cours' => false,
                        'Complete' => true,
                    ]
                ])
                ->add('duree', null, [
                    'label' => 'Durée'
                ])
                ->add('employe', null, [
                    'label' => 'Afféctué à '
                ])
                // ->add('projet', ChoiceType::class, [
                //     'choices' => [
                //         $projet -> getTitre() => $projet
                //     ]
                // ])
                ->getForm();
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->getDoctrine()->getManager()->flush();

                return $this->redirectToRoute('tache_index', ['idp' => $request->get('idp')]);
            }

            return $this->render('tache/edit.html.twig', [
                'tache' => $tache,
                'form' => $form->createView(),
            ]);
        }else{
            if($tache->getEmploye() == $this->getUser()){
                $tache->setEtat(!$tache->getEtat());
                $this->getDoctrine()->getManager()->flush();
            }
            return $this->redirectToRoute('tache_index_employe');
        }
        
    }

    /**
     * @Route("/{id}", name="tache_delete", methods={"POST"})
     */
    public function delete(Request $request, Tache $tache): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tache->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($tache);
            $entityManager->flush();
        }

        return $this->redirectToRoute('tache_index');
    }
}
