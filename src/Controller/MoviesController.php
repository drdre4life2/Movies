<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Repository\MovieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class MoviesController extends AbstractController
{
   private $em;
   public function __construct(EntityManagerInterface $em)
   {
     $this->em = $em;
   }

    #[Route('/movies', name: 'app_movies')]
    public function index()
    {
        $repository = $this->em->getRepository(Movie::class);
        $movie = $repository->findAll();
        return $this->render('index.html.twig');
    }
}
