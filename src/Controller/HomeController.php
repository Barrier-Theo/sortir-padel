<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\Sortie;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController{


    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        $campusRepo = $this->getDoctrine()->getRepository(Campus::class);
        $campus = $campusRepo->findAll();

        $sortieRepo = $this->getDoctrine()->getRepository(Sortie::class);
        $sorties = $sortieRepo->findAll();

        return $this->render('index.html.twig', [
            "campus" => $campus,
            "sorties" => $sorties
        ]);
    }

}