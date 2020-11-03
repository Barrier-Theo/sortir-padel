<?php

namespace App\Controller;

use App\Form\ImportType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController{


    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {

        return $this->render('admin.html.twig', []);
    }

    /**
     * @Route("/admin/import", name="admin_import")
     */
    public function import(Request $request): Response
    {
        $form = $this->createForm(ImportType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $csvFile = $form['csv']->getData();

              // If a file is uploaded
              if ($csvFile) {
                  // this is needed to safely include the file name as part of the URL
                  $newFilename = 'importUser'.$csvFile->guessExtension();
                
                  // Move the file to the directory where brochures are stored
                  try {
                      $csvFile->move(
                          //Path parameter is in "services.yaml"
                          $this->getParameter('csv_directory'),
                          $newFilename
                      );
                  } catch (FileException $e) {
                    // handle exception if something happens during file upload and redirecting to the page
                    $this->addFlash('error', 'Erreur pendant l\'import du fichier');
                    $this->redirectToRoute('admin');
                  }             
              }
              // Call import form TraitementImport
              
              //$import->import($pathName, $form);
              //Redirect to admin page
              //$this->redirectToRoute('admin.import');
        }
        return $this->render('importAdmin.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    

}