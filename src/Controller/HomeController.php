<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\Sortie;
use App\Form\SearchSortieType;
use App\Repository\SortieRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(Request $request, SortieRepository $sortieRepository): Response
    {
/*         $campusRepo = $this->getDoctrine()->getRepository(Campus::class);
        $campus = $campusRepo->findAll();
        */

        $sortieRepo = $this->getDoctrine()->getRepository(Sortie::class);
        $sorties = $sortieRepo->findAll(); 

        $searchSortieForm = $this->createForm(SearchSortieType::class);

        if($searchSortieForm->handleRequest($request)->isSubmitted() && $searchSortieForm->isValid()) {
            $critere = $searchSortieForm->getData();
            $sorties = $sortieRepository->searchSorties($critere);
        }
        return $this->render('index.html.twig', [
            'search_from' => $searchSortieForm->createView(),
            'sorties' => $sorties
        ]);
    }
}
