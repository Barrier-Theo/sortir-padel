<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ImportType;
use App\Repository\CampusRepository;
use App\Repository\EtatRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminController extends AbstractController{


    /**
     * @Route("/admin", name="admin")
     */
    public function index(ParticipantRepository $participantRepository, SortieRepository $sortieRepository): Response
    {
        $userActif = $participantRepository->findBy(['actif' => 1]);
        $sorties = $sortieRepository->findAll();
        $date = new DateTime('now');
        $date->add(DateInterval::createFromDateString('-1 months'));
        $sortieMois = null;
        $inscriptionDuMois = null;
        if($sorties != null){
            foreach($sorties as $sortie){
                if($sortie->getDateHeureDebut() > $date){
                    $sortieMois[] = $sortie;
                    $inscriptions = $sortie->getInscriptions();
                    if($inscriptions != null){
                        foreach($inscriptions as $inscription){
                                $inscriptionDuMois[] = $inscription;
                        }
                    }
                }
            }
        }
        
        return $this->render('admin.html.twig', [
            'userActif' => $userActif,
            'sortieDuMois' => $sortieMois,
            'inscriptionDuMois' => $inscriptionDuMois
        ]);
    }

    /**
     * @Route("/admin/import", name="admin_import")
     */
    public function import(Request $request, CampusRepository $campusRepository, EntityManagerInterface $em
    , UserPasswordEncoderInterface $passwordEncoder, ParticipantRepository $participantRepository)
    {
        $form = $this->createForm(ImportType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $csvFile = $form['csv']->getData();
              if ($csvFile) {
                  $newFilename = 'importUser.txt';
                  try {
                      $csvFile->move(
                          //Path parameter is in "services.yaml"
                          $this->getParameter('csv_directory'),
                          $newFilename
                      );
                  } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur pendant l\'import du fichier');
                    return $this->redirectToRoute('admin');
                  }             
              }
              
        $pathCSV = $this->getParameter('csv_directory');
        if (($handle = fopen($pathCSV.'/importUser.txt', 'r')) !== FALSE) {

            
                //On boucle sur chaque ligne du csv
                while (($data = fgetcsv($handle, 600, ";")) !== FALSE) {
                    if(!(strlen($data[0])> 0 && strlen($data[0]) < 255) ||
                        !(strlen($data[1])> 0 && strlen($data[1]) < 255) ||
                        !(strlen($data[2])> 0 && strlen($data[2]) < 255) ||
                        !(strlen($data[3])> 0 && strlen($data[3]) < 15) ||
                        !(strlen($data[4])> 0 && strlen($data[4]) < 255) ||
                        !(strlen($data[5])> 0 && strlen($data[5]) < 255) ||
                        !($data[6] == 0 || $data[6]==1) ||
                        !($data[7] == 0 || $data[7]==1) ){
                        $this->addFlash('error', 'le fichier csv ne respecte pas le standard d\'import');
                        return $this->redirectToRoute('admin_import');
                    }
                    if($campusRepository->findOneBy(['nom' => $data[8]])== null){
                        $this->addFlash('error', 'le campus : ' .$data[8].'n\'existe pas');
                        return $this->redirectToRoute('admin_import');
                    }
                    if($participantRepository->findOneBy(['mail' => $data[4]]) != null){
                        $this->addFlash('error', 'le mail ' . $data[4]. 'est déja utilisé');
                        return $this->redirectToRoute('admin_import');
                    }
                }
        }
        if (($handle = fopen($pathCSV.'/importUser.txt', 'r')) !== FALSE) {
                //On boucle sur chaque ligne du csv
                while (($data = fgetcsv($handle, 600, ";")) !== FALSE) {
                    $campus = $campusRepository->findOneBy(['nom' => $data[8]]);
                    if($campus != null){
                        $participant = new Participant();
                        $participant->setPseudo($data[0])
                                ->setNom($data[1])
                                ->setPrenom($data[2])
                                ->setTelephone($data[3])
                                ->setMail($data[4])
                                ->setAdministrateur($data[6])
                                ->setActif($data[7])
                                ->setCampus($campus);
                        $participant->setMotDePasse($passwordEncoder->encodePassword(
                                    $participant,
                                    $data[5]
                                ));
                    $em->persist($participant);
                    }
                }
                $em->flush();
                $this->addFlash('success', 'Import réussi');
                unlink($pathCSV.'/importUser.txt');
            } 
        }
        return $this->render('importAdmin.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    

}