<?php

namespace App\Controller;

use App\Repository\ActiviteAgentsRepository;
use App\Repository\InfoImportRepository;
use App\Traitement\DateImport;
use DateTime;
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

        return $this->render('index.html.twig',[
        ]);
    }

}