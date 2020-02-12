<?php

namespace App\Controller\Admin;

use App\Entity\News;
use App\Form\Admin\ImageType;
use App\Form\News1Type;
use App\Repository\NewsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
//use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("admin/news")
 */
class NewsController extends AbstractController
{
    /**
     * @Route("/", name="admin_news_index", methods={"GET"})
     */
    public function index(NewsRepository $newsRepository): Response
    {
        $news=$newsRepository->getAllNews();
        return $this->render('admin/news/index.html.twig', [
            'news' => $news,
        ]);
    }

    /**
     * @Route("/new", name="admin_news_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $news = new News();
        $form = $this->createForm(News1Type::class, $news);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            //*******file upload****/
            /** @var file $file */
            $file = $form['image'] ->getData();
            if($file) {
                $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();

                try {
                    $file->move(
                        $this->getParameter('images_directory'),
                        $fileName
                    );
                } catch(FileException $e) {

                }
                $news->setImage($fileName); //related upload file name with news table image field
            }
            //file uploand end
            $entityManager->persist($news);
            $entityManager->flush();

            return $this->redirectToRoute('admin_news_index');
        }

        return $this->render('admin/news/new.html.twig', [
            'news' => $news,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="admin_news_show", methods={"GET"})
     */
    public function show(News $news): Response
    {
        return $this->render('admin/news/show.html.twig', [
            'news' => $news,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="admin_news_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, News $news): Response
    {
        $form = $this->createForm(News1Type::class, $news);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form['image'] ->getData();
            if($file) {
                //dosya adı. dosyanın uzantisi
                $fileName = $this -> generateUniqueFileName() . '.' . $file->guessExtension();

                try {
                    $file->move(
                        $this->getParameter('images_directory'), //in Services.yaml defines folder for upload images
                        $fileName
                    );
                } catch(FileException $e) {

                }
                $news->setImage($fileName);
            }


            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_news_index');
        }

        return $this->render('admin/news/edit.html.twig', [
            'news' => $news,
            'form' => $form->createView(),
        ]);
    }

    private function generateUniqueFileName() {
        /** md5() reduces the similartiy of the file names
         *generated vy uniqid(), which is based on timestamps
         */
        return md5(uniqid());
    }

    /**
     * @Route("/{id}", name="admin_news_delete", methods={"DELETE"})
     */
    public function delete(Request $request, News $news): Response
    {
        if ($this->isCsrfTokenValid('delete'.$news->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($news);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_news_index');
    }
}
