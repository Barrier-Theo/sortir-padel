<?php

namespace App\Controller;

use App\Data\SearchData;
use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Inscription;
use App\Entity\Sortie;
use App\Form\SearchSortieType;
use App\Repository\SortieRepository;
use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\Length;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(Request $request, SortieRepository $sortieRepository): Response
    {
        $this->denyAccessUnlessGranted("ROLE_USER");
        $entityManager = $this->getDoctrine()->getManager();
        $userId = $this->getUser()->getId();
        $sortieRepo = $this->getDoctrine()->getRepository(Sortie::class);
        $sorties = $sortieRepo->findAll();

        $etatRepo = $this->getDoctrine()->getRepository(Etat::class);

        $data = new SearchData();
        $form = $this->createForm(SearchSortieType::class, $data);
        $form->handleRequest($request);
        $sorties = $sortieRepository->searchSorties($data, $userId);
        $date = new DateTime('now');
        $date->add(DateInterval::createFromDateString('-1 months'));

        $dateNow = new DateTime('now');

        // Delete sorties that last more than 1 month
        foreach ($sorties as $sortie) {
            if ($sortie->getDateHeureDebut() <= $date) {
                unset($sorties[array_search($sortie, $sorties)]);
            }

            // Sortie clôturée
            if ($sortie->isSortieCloturee() == true && $sortie->getEtat()->getLibelle() != "Annulée") {
                $etat = $etatRepo->findOneBy(['libelle' => 'Clôturée']);
                $sortie->setEtat($etat);
            }

            // Sortie en cours
            $dateNow = new DateTime('now');
            $dateMax = clone ($sortie->getDateHeureDebut());
            $dateMax->add(DateInterval::createFromDateString('+' . $sortie->getDuree() . 'hours'));
            if ($dateNow >= $sortie->getDateHeureDebut() && $dateNow <= $dateMax) {
                $etat = $etatRepo->findOneBy(['libelle' => 'Activité en cours']);
                $sortie->setEtat($etat);
            }

            // Sortie terminée
            if ($dateNow >= $dateMax) {
                $etat = $etatRepo->findOneBy(['libelle' => 'Passée']);
                $sortie->setEtat($etat);
            }
            $entityManager->persist($sortie);
        }

        $entityManager->flush();

        return $this->render('index.html.twig', [
            'sorties' => $sorties,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/addparticipant/{sortieId}", name="home_addparticipant")
     */
    public function addParticipant($sortieId, Request $request, SortieRepository $sortieRepository)
    {
        $sortieRepo = $this->getDoctrine()->getRepository(Sortie::class);
        $sortie = $sortieRepo->find($sortieId);
        $entityManager = $this->getDoctrine()->getManager();

        $etatRepo = $this->getDoctrine()->getRepository(Etat::class);
        $etat = $etatRepo->findOneBy(['libelle' => 'Clôturée']);

        if (!$sortie->isSortieFull()) {
            $user = $this->getUser();

            $inscription = new Inscription();
            $inscription->setParticipant($user);
            $inscription->setSortie($sortie);
            $entityManager->persist($inscription);
        } else {
            $sortie->setEtat($etat);
            $entityManager->persist($sortie);
        }

        $entityManager->flush();

        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/deleteparticipant/{sortieId}", name="home_deleteparticipant")
     */
    public function deleteParticipant($sortieId, Request $request, SortieRepository $sortieRepository)
    {
        $dateNow = new DateTime('now');
        $entityManager = $this->getDoctrine()->getManager();

        $etatRepo = $this->getDoctrine()->getRepository(Etat::class);
        $etat = $etatRepo->findOneBy(['libelle' => 'Ouverte']);

        $sortieRepo = $this->getDoctrine()->getRepository(Sortie::class);
        $sortie = $sortieRepo->find($sortieId);

        $inscriptionRepo = $this->getDoctrine()->getRepository(Inscription::class);
        $inscriptions = $inscriptionRepo->findAll();

        if ($sortie->isSortieFull() == true && $dateNow <= $sortie->getDateLimiteInscription()) {
            $sortie->setEtat($etat);
            $entityManager->persist($sortie);
        }

        foreach ($inscriptions as $inscription) {
            if ($inscription->getSortie()->getId() == $sortieId && $inscription->getParticipant()->getId() == $this->getUser()->getId()) {
                $entityManager->remove($inscription);
            }
        }
        $entityManager->flush();

        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/publishsortie/{sortieId}", name="home_publishsortie")
     */
    public function publishSortie($sortieId)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $etatRepo = $this->getDoctrine()->getRepository(Etat::class);
        $etat = $etatRepo->findOneBy(['libelle' => 'Ouverte']);

        $sortieRepo = $this->getDoctrine()->getRepository(Sortie::class);
        $sortie = $sortieRepo->find($sortieId);
        $sortie->setEtat($etat);
        $entityManager->persist($sortie);
        $entityManager->flush();

        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/cancelsortie/{sortieId}", name="home_cancelsortie")
     */
    public function cancelSortie($sortieId)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $etatRepo = $this->getDoctrine()->getRepository(Etat::class);
        $etat = $etatRepo->findOneBy(['libelle' => 'Annulée']);

        $sortieRepo = $this->getDoctrine()->getRepository(Sortie::class);
        $sortie = $sortieRepo->find($sortieId);
        $sortie->setEtat($etat);
        $entityManager->persist($sortie);
        $entityManager->flush();

        return $this->redirectToRoute('home');
    }
}
