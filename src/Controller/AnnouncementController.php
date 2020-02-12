<?php

namespace App\Controller;

use App\Entity\Announcement;
use App\Form\Announcement1Type;
use App\Repository\AnnouncementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user/announcement")
 */
class AnnouncementController extends AbstractController
{
    /**
     * @Route("/", name="user_announcement_index", methods={"GET"})
     */
    public function index(AnnouncementRepository $announcementRepository): Response
    {
        $user = $this->getUser();
        return $this->render('announcement/index.html.twig', [
            'announcements' => $announcementRepository->findBy(['userid'=>$user->getId()]),
        ]);
    }

    /**
     * @Route("/new", name="user_announcement_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $announcement = new Announcement();
        $form = $this->createForm(Announcement1Type::class, $announcement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            //hangi user ise onun icin usesr bilgilerini aliyoruz
            $user = $this->getUser(); //get login user data
            $announcement->setUserid($user->getId());
            $announcement->setStatus("New"); //User yeni news eklediginde true false yapamayacak yerine new olucak admin tarafından onaylanicak


            $entityManager->persist($announcement);
            $entityManager->flush();

            return $this->redirectToRoute('user_announcement_index');
        }

        return $this->render('announcement/new.html.twig', [
            'announcement' => $announcement,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_announcement_show", methods={"GET"})
     */
    public function show(Announcement $announcement): Response
    {
        return $this->render('announcement/show.html.twig', [
            'announcement' => $announcement,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_announcement_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Announcement $announcement): Response
    {
        $form = $this->createForm(Announcement1Type::class, $announcement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_announcement_index');
        }

        return $this->render('announcement/edit.html.twig', [
            'announcement' => $announcement,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_announcement_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Announcement $announcement): Response
    {
        if ($this->isCsrfTokenValid('delete'.$announcement->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($announcement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_announcement_index');
    }
}
