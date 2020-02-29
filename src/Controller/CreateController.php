<?php


namespace App\Controller;


use App\Form\ArtistNameListType;
use App\Service\Spotify\ArtistTopTrackGetter;
use App\Service\Spotify\AuthorizationHandler as SpotifyAuth;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

/**
 * @Route("/create_controller")
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
}
