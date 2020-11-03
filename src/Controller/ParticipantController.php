<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ParticipantType;
use App\Repository\ParticipantRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route("/participant")
 */
class ParticipantController extends AbstractController
{
    /**
     * @Route("/", name="participant_index", methods={"GET"})
     */
    public function index(ParticipantRepository $participantRepository): Response
    {
        return $this->render('participant/index.html.twig', [
            'participants' => $participantRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="participant_new", methods={"GET","POST"})
     */
    public function new(Request $request,  UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $participant = new Participant();
        $form = $this->createForm(ParticipantType::class, $participant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $participant->setMotDePasse($passwordEncoder->encodePassword(
                    $participant,
                    $form->get('motDePasse')->getData()
                )
            );
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($participant);
            $entityManager->flush();

            return $this->redirectToRoute('participant_index');
        }

        return $this->render('participant/new.html.twig', [
            'participant' => $participant,
            'form' => $form->createView(),
        ]);
    }



    /**
     * @Route("/show_me", name="participant_show_me", methods={"GET"})
     */
    public function showMe(Request $request, ParticipantRepository $participantRepository, UserInterface $user): Response
    {
        $idParticipant = $user->getId();
        $participant = $participantRepository->find($idParticipant);
        return $this->render('participant/show_me.html.twig', [
            'participant' => $participant,
        ]);
    }

    /**
     * @Route("/edit_me}", name="participant_edit_me", methods={"GET","POST"})
     */
    public function editMe(Request $request, ParticipantRepository $participantRepository, UserInterface $user, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $idUser = $user->getId();
        $participant = $participantRepository->find($idUser);

        $form = $this->createForm(ParticipantType::class, $participant);
        $form->remove('administrateur');
        $form->remove('actif');

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $participant->setMotDePasse($passwordEncoder->encodePassword(
                $participant,
                $form->get('motDePasse')->getData()
            )
            );
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('participant_show_me');
        }

        return $this->render('participant/edit_me.html.twig', [
            'participant' => $participant,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}", name="participant_show", methods={"GET"})
     */
    public function show(Participant $participant): Response
    {
        return $this->render('participant/show.html.twig', [
            'participant' => $participant,
        ]);
    }


    /**
     * @Route("/{id}/edit", name="participant_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Participant $participant, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $form = $this->createForm(ParticipantType::class, $participant);
        $form->get('motDePasse')->setData('');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $participant->setMotDePasse($passwordEncoder->encodePassword(
                $participant,
                $form->get('motDePasse')->getData()
            ));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($participant);
            $entityManager->flush();

            return $this->redirectToRoute('participant_index');
        }

        return $this->render('participant/edit.html.twig', [
            'participant' => $participant,
            'form' => $form->createView(),


        ]);
    }

    /**
     * @Route("/{id}", name="participant_delete", methods={"post"})
     */
    public function delete(Request $request, Participant $participant): Response
    {
        if ($this->isCsrfTokenValid('delete'.$participant->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($participant);
            $entityManager->flush();
        }

        return $this->redirectToRoute('participant_index');
    }



}
