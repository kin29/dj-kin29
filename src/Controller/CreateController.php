<?php


namespace App\Controller;


use App\Form\ArtistNameListType;
use App\Service\Spotify\ArtistTopTrackGetter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

/**
 * @Route("/create")
 */
class CreateController extends AbstractController
{
    /**
     * @var ArtistTopTrackGetter
     */
    private $artistTopTrackGetter;

    public function __construct(ArtistTopTrackGetter $artistTopTrackGetter)
    {
        $this->artistTopTrackGetter = $artistTopTrackGetter;
    }

    /**
     * @Route("/", name="create")
     * @return Response
     */
    public function create(Request $request): Response
    {
        $form = $this->createForm(ArtistNameListType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $artistNamesData = [
                $data['artistName1'],
                $data['artistName2'],
                $data['artistName3'],
                $data['artistName4'],
                $data['artistName5'],
            ];

            $artistNames = [];
            foreach ($artistNamesData as $data) {
                if($data) $artistNames[] = $data;
            }
            list($tracks, $artists) = $this->artistTopTrackGetter->get($artistNames);

            return $this->redirect($this->generateUrl('create_complete'));
            //renderは効かない
        }

        return $this->render('create/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/complete", name="create_complete")
     * @return Response
     */
    public function createComplete()
    {
        return $this->render('create/complete.html.twig', [
//                'tracks' => $tracks,
//                'artists' => $artists
        ]);
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
