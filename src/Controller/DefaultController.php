<?php


namespace App\Controller;

use App\Form\ArtistNameListType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        $form = $this->createForm(ArtistNameListType::class, null, [
            'action' => $this->generateUrl('post'),
        ]);

        return $this->render('default/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/post", name="post")
     */
    public function post()
    {
        return $this->render('default/post.html.twig', [
            'controller_name' => 'PostController',
        ]);
    }
}
