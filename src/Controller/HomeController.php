<?php

namespace App\Controller;

use App\Entity\Admin\Messages;
use App\Entity\News;
use App\Form\Admin\MessagesType;
use App\Repository\Admin\CommentRepository;
use App\Repository\AnnouncementRepository;
use App\Repository\EventRepository;
use App\Repository\ImageRepository;
use App\Repository\NewsRepository;
use App\Repository\SettingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Bridge\Google\Smtp\GmailTransport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(SettingRepository $settingRepository,NewsRepository $newsRepository,AnnouncementRepository $announcementRepository,EventRepository $eventRepository)
    {

        $setting=$settingRepository->findAll();
        $slider=$newsRepository->findBy(['status'=>'True'],['title'=>'ASC'] ,4);
        $news=$newsRepository->findBy([],['title'=>'DESC'] ,4);
        $announcement=$announcementRepository->findBy(['status'=>'True'],['date'=>'DESC'] ,4);
        $event=$eventRepository->findBy(['status'=>'True'],['date'=>'DESC'] ,4);
        // dump($slider);
        //die();

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'setting'=>$setting,
            'slider'=>$slider,
            'news'=>$news,
            'announcement'=>$announcement,
            'event'=>$event,
        ]);
    }

    /**
     * @Route("/news/{id}", name="news_show", methods={"GET"})
     */
    public function show(News $news,$id,ImageRepository $imageRepository,CommentRepository $commentRepository): Response
    {
        $images = $imageRepository->findBy(['news'=>$id]);
        //sadece statu sü true olanlar gosterilsin
        //$user = $this->getUser();
        //$comments=$commentRepository->getAllCommentsUser($user->getId());
         $comments = $commentRepository->findBy(['newsid'=>$id, 'status'=>'True']);
        //$announcements = $announcementRepository->findBy(['announcementid'=>$id, 'status'=>'True']);
        //image ları al newsshow a gonder
        return $this->render('home/newsshow.html.twig', [
            'news' => $news,
            'images' => $images,
            'comments'=>$comments,
            //'announcements'=>$announcements,
        ]);
    }

    /**
     * @Route("/about", name="home_about")
     */
    public function about(SettingRepository $settingRepository): Response
    {
        $setting=$settingRepository->findAll();
        return $this->render('home/aboutus.html.twig', [
            'setting'=>$setting,
        ]);
    }

    /**
     * @Route("/contact", name="home_contact",  methods={"GET","POST"})
     */
    public function contact(SettingRepository $settingRepository,Request $request): Response
    {
        $message = new Messages();
        $form = $this->createForm(MessagesType::class, $message);
        $form->handleRequest($request);
        $submittedToken = $request->request->get('token');

        $setting=$settingRepository->findAll();

        if ($form->isSubmitted()) {
            //if condition for token
            if($this->isCsrfTokenValid('form-message', $submittedToken)) {
                $entityManager = $this->getDoctrine()->getManager();
                //------
                $message->setStatus('New');
                $message->setIp($_SERVER['REMOTE_ADDR']);
                $entityManager->persist($message);
                $entityManager->flush();
                //---
                $this->addFlash('success', 'Your message has been sent successfuly');

                //----------send email---------------
                $email = (new Email())
                    ->from($setting[0]->getSmtpemail())
                    ->to($form['email']->getData())
                    //->cc('cc@example.com')
                    //->bcc('bcc@example.com')
                    //->replyTo('fabien@example.com')
                    //->priority(Email::PRIORITY_HIGH)
                    ->subject('Time for Symfony Mailer!')
                    ->text('Sending emails is fun again!');
                //->html();


                $transport = new GmailTransport($setting[0]->getSmtpemail(), $setting[0]->getSmtppassword());
                $mailer = new Mailer($transport);
                $mailer->send($email);



                return $this->redirectToRoute('home_contact');
            }
        }

        $setting=$settingRepository->findAll();
        return $this->render('home/contact.html.twig', [
            'setting'=>$setting,
            'form' => $form->createView(),
        ]);
    }



}
