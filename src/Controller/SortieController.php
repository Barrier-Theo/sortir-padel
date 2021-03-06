<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/sortie")
 */
class SortieController extends AbstractController
{
    /**
     * @Route("/admin/", name="sortie_index", methods={"GET"})
     */
    public function index(SortieRepository $sortieRepository): Response
    {
        return $this->render('sortie/index.html.twig', [
            'sorties' => $sortieRepository->findAll(),
        ]);
    }

    /**
     * @Route("/admin/new", name="sortie_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $sortie = new Sortie();
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $id = $this->getUser()->getId();
            $participantRepo = $this->getDoctrine()->getRepository(Participant::class);
            $organisateur = $participantRepo->find($id);
            $sortie->setOrganisateur($organisateur);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($sortie);
            $entityManager->flush();
            $this->addFlash("success", ["text" => "La sortie a été créée !", "couleur" => "#4CB050"]);
            
            return $this->redirectToRoute('sortie_index');
        }

        return $this->render('sortie/new.html.twig', [
            'sortie' => $sortie,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/{id}", name="sortie_show", methods={"GET"})
     */
    public function show(Sortie $sortie): Response
    {
        return $this->render('sortie/show.html.twig', [
            'sortie' => $sortie,
        ]);
    }

    /**
     * @Route("/sortievisualisation/{id}", name="sortie_visualisation", methods={"GET"})
     */
    public function displayFromTable(Sortie $sortie)
    {
        return $this->render('sortie/displayFromSorties.html.twig', [
            'sortie' => $sortie,
        ]);
    }

    /**
     * @Route("/admin/{id}/edit", name="sortie_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Sortie $sortie): Response
    {
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);
        
        if(!$this->isGranted('ROLE_ADMIN')){
            if($sortie->getOrganisateur()->getId() !== $this->getUser()->getId() || $sortie->getEtat()->getLibelle() !== 'Créée'){
                return $this->redirectToRoute('home');
            }
        }


        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash("success", ["text" => "La sortie a été modifiée !", "couleur" => "#4CB050"]);

            if ($this->isGranted('ROLE_ADMIN')) {
                return $this->redirectToRoute('sortie_index');
             } else {
                return $this->redirectToRoute('home');
             }
            
        }

        return $this->render('sortie/edit.html.twig', [
            'sortie' => $sortie,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/{id}", name="sortie_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Sortie $sortie): Response
    {
        if ($this->isCsrfTokenValid('delete' . $sortie->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($sortie);
            $entityManager->flush();
            $this->addFlash("success", ["text" => "La sortie {$sortie->getNom()} a été supprimée !", "couleur" => "#4CB050"]);
        }

        return $this->redirectToRoute('sortie_index');
    }

    /**
     * @Route("/new", name="sortie_new_user", methods={"GET","POST"})
     */
    public function newSortieUser(Request $request): Response
    {
        $sortie = new Sortie();
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $id = $this->getUser()->getId();
            $organisateur = new Participant();
            $participantRepo = $this->getDoctrine()->getRepository(Participant::class);
            $organisateur = $participantRepo->find($id);
            $sortie->setOrganisateur($organisateur);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($sortie);
            $entityManager->flush();
            $this->addFlash('success', ["text" => "Sortie ajoutée", "couleur" => "#4CB050"]);
            return $this->redirectToRoute('home');
        }

        return $this->render('sortie/newSortieUser.html.twig', [
            'sortie' => $sortie,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/annulation", name="annulation_sortie", methods={"POST"})
     */
    public function annulationSortie(Request $request, SortieRepository $sortieRepository, EtatRepository $etatRepository, EntityManagerInterface $em): Response
    {
        $id = $request->get('sortieId');
        $infoAnnulation = $request->get('infoAnnulation');

        if ($id != null & $infoAnnulation != null) {
            $sortie =  $sortieRepository->find($id);
            $etat = $etatRepository->findOneBy(['libelle' => 'Annulée']);
            $sortie->setEtat($etat)
                ->setInfosSortie('Annulée : ' . $infoAnnulation);
            $em->persist($sortie);
            $em->flush();
        } else {
            $this->addFlash('error', 'annulation impossible');
        }

        return $this->render('sortie/index.html.twig', [
            'sorties' => $sortieRepository->findAll()
        ]);
    }
}
