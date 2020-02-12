<?php


namespace App\Controller\Admin;

use App\Entity\Announcement;
use App\Form\AnnouncementType;
use App\Repository\AnnouncementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("admin/announcement")
 */
class AnnouncementController extends AbstractController
{
    /**
     * @Route("/", name="admin_announcement_index", methods={"GET"})
     */
    public function index(AnnouncementRepository $announcementRepository): Response
    {
        return $this->render('admin/announcement/index.html.twig', [
            'announcements' => $announcementRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="admin_announcement_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $announcement = new Announcement();
        $form = $this->createForm(AnnouncementType::class, $announcement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($announcement);
            $entityManager->flush();

            return $this->redirectToRoute('admin_announcement_index');
        }

        return $this->render('admin/announcement/new.html.twig', [
            'announcement' => $announcement,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="admin_announcement_show", methods={"GET"})
     */
    public function show(Announcement $announcement): Response
    {
        return $this->render('admin/announcement/show.html.twig', [
            'announcement' => $announcement,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="admin_announcement_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Announcement $announcement): Response
    {
        $form = $this->createForm(AnnouncementType::class, $announcement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_announcement_index');
        }

        return $this->render('admin/announcement/edit.html.twig', [
            'announcement' => $announcement,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="admin_announcement_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Announcement $announcement): Response
    {
        if ($this->isCsrfTokenValid('delete'.$announcement->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($announcement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_announcement_index');
    }
}
