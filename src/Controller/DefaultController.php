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
        $form = $this->createForm(ArtistNameListType::class);

        return $this->render('default/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
