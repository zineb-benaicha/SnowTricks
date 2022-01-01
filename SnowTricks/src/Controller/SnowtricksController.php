<?php

namespace App\Controller;

use App\Entity\Figure;
use App\Repository\FigureRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class SnowtricksController extends AbstractController
{
    /*private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }*/

    /**
     * @Route("/snowtricks", name="snowtricks")
     */
    public function index(): Response
    {
        return $this->render('snowtricks/index.html.twig', [
            'controller_name' => 'SnowtricksController',
        ]);
    }

    /**
     * @Route("/", name="home")
     */
    public function home(FigureRepository $repo)
    {
        $figuresList = $repo->findAll();
        return $this->render('snowtricks/home.html.twig',[
            'figuresList' => $figuresList
        ]);
    }

    /**
     * @Route("/figure/{id}", name="figure_show")
     */
    public function showFigure(Figure $figure)
    {
        return $this->render('snowtricks/show_figure.html.twig',[
            'figure' => $figure
        ]);
    }

    /**
     * @Route("/figure/{id}/delete", name="figure_delete")
     * @return RedirectResponse
     */
    public function deleteFigure(Figure $figure, ManagerRegistry $doctrine): RedirectResponse
    {
        $manager = $doctrine->getManager();
        $manager->remove($figure);
        $manager->flush();
        return $this->redirectToRoute("/");


    }
}
