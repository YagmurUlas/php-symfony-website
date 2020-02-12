<?php

namespace App\Controller;

use App\Entity\News;
use App\Form\NewsType;
use App\Repository\NewsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user/news")
 */
class NewsController extends AbstractController
{
    /**
     * @Route("/", name="user_news_index", methods={"GET"})
     */
    public function index(NewsRepository $newsRepository): Response
    {
        $user = $this->getUser(); //get login user data
        return $this->render('news/index.html.twig', [
            'news' => $newsRepository->findBy(['userid'=>$user->getId()]),
        ]);
    }

    /**
     * @Route("/new", name="user_news_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $news = new News();
        $form = $this->createForm(NewsType::class, $news);
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

            $user = $this->getUser(); //get login user data
            $news->setUserid($user->getId());
            $news->setStatus("New"); //User yeni news eklediginde true false yapamayacak yerine new olucak admin tarafından onaylanicak

            $entityManager->persist($news);
            $entityManager->flush();

            return $this->redirectToRoute('user_news_index');
        }

        return $this->render('news/new.html.twig', [
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
     * @Route("/{id}", name="user_news_show", methods={"GET"})
     */
    public function show(News $news): Response
    {
        return $this->render('news/show.html.twig', [
            'news' => $news,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_news_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, News $news): Response
    {
        $form = $this->createForm(NewsType::class, $news);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //-----------------------------------------
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
            //--------------------------------------------
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_news_index');
        }

        return $this->render('news/edit.html.twig', [
            'news' => $news,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_news_delete", methods={"DELETE"})
     */
    public function delete(Request $request, News $news): Response
    {
        if ($this->isCsrfTokenValid('delete'.$news->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($news);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_news_index');
    }
}
