<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Form\MovieFormType;
use App\Repository\MovieRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Builder\Method;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class MoviesController extends AbstractController
{
    private $em;
    private $movieRepository;
    public function __construct(MovieRepository $movieRepository,  EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->movieRepository = $movieRepository;
    }

    #[Route('/movies', methods: ['GET'], name: 'movies')]
    public function index()
    {
        $movies = $this->movieRepository->findAll(Movie::class);
        return $this->render('movies/index.html.twig', ['movies' => $movies]);
    }


    #[Route('/movies/create', name: 'create_movie')]
    public function create(Request $request)
    {
        $movie = new Movie();
        $form = $this->createForm(MovieFormType::class, $movie);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $newMovie = $form->getData();
            $imagePath = $form->get('imagePath')->getData();
            if ($imagePath) {
                $newFileName = uniqid() . '.' . $imagePath->guessExtension();
                try {
                    $imagePath->move(
                        $this->getParameter('kernel.project_dir') . '/public/uploads',
                        $newFileName
                    );
                } catch (FileException $e) {
                    return new Response($e->getMessage());
                }
                $newMovie->setImagePath('/uploads/' . $newFileName);
            }
            $this->em->persist($newMovie);
            $this->em->flush();
            return $this->redirectToRoute('movies');
        }
        return $this->render('movies/create.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/movies/edit/{id}', name: 'edit_movies')]
    public function edit($id,  Request $request)
    {
        $movie = $this->movieRepository->find($id);
        $form = $this->createForm(MovieFormType::class, $movie);

        $form->handleRequest($request);
        $imagePath = $form->get('imagePath')->getData();

        if ($form->isSubmitted() && $form->isValid()) {

            if ($imagePath) {
                if ($movie->getImagePath() !== null) {
                    $imageWithPath = $this->getParameter('kernel.project_dir') . $movie->getImagePath();
                    if ($imageWithPath) {
                       // dd('yeah');
                        $this->getParameter('kernel.project_dir') . $movie->getImagePath();
                        $newFileName = uniqid() . '.' . $imagePath->guessExtension();

                      try{
                        $imagePath->move($this->getParameter('kernel.project_dir') . '/public/uploads', $newFileName
                        );

                          }catch(FileException $e){
                          return new Response($e->getMessage());
                          }
                        $movie->setImagePath('/uploads/' . $newFileName );
                        $this->em->flush();
                        return $this->redirectToRoute('movies');
                    }
                }
            } else {
                $movie->setTitle($form->get('title')->getData());
                $movie->setReleaseYear($form->get('releaseYear')->getData());
                $movie->setDescription($form->get('description')->getData());
                $this->em->flush();
                return $this->redirectToRoute('movies');
            }
        }
        return $this->render('movies/edit.html.twig', [
            'movie' => $movie,
            'form' => $form->createView()
        ]);
    }

    #[Route('/movies/delete/{id}', methods: ['GET', 'DELETE'], name: 'delete_movies')]
    public function delete($id):Response
    {
        $movie = $this->movieRepository->find($id);
        $this->em->remove($movie);
        $this->em->flush();
        
        return $this->redirectToRoute('movies');
    }


    #[Route('/movies/{id}',  methods: ['GET'], name: 'show_movies')]
    public function show($id)
    {
        $repository = $this->em->getRepository(Movie::class);
        $movie = $repository->find($id);
        return $this->render('movies/show.html.twig', ['movie' => $movie]);
    }
}
