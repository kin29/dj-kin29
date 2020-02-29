<?php


namespace App\Controller;


use App\Form\ArtistNameListType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/create")
 */
class CreateController extends AbstractController
{
    /**
     * @Route("/", name="create")
     * @return Response
     */
    public function create(): Response
    {
        $form = $this->createForm(ArtistNameListType::class, null, [
            //todo 'action' => $this->generateUrl('post'),
        ]);

        return $this->render('create/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/complete", name="create_complate")
     * @return Response
     */
    public function createComplete(): Response
    {
        return $this->render('create/complete.html.twig');
    }

    /**
     * @Route("/failure", name="create_failure")
     * @return Response
     */
    public function createFailure(): Response
    {
        return $this->render('create/failure.html.twig');
    }
}
