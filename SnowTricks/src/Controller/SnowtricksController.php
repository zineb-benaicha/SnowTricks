<?php

namespace App\Controller;

use App\Entity\Figure;
use App\Entity\Group;
use App\Repository\FigureRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;


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
     * @Route("figure/new", name="figure_create")
     */
    public function createFigure(Request $request, ManagerRegistry $doctrine)
    {
        $figure = new Figure();
        $manager = $doctrine->getManager();

        $form = $this->createFormBuilder($figure)
                     ->add('name')
                     ->add('description')
                     ->add('groupe', EntityType::class, [
                            'class' => Group::class,
                            'choice_label' => 'name',
                     
                     ])
                     ->add('images', FileType::class, [
                            'multiple' => true,
                            'mapped' => false,
                            'required' => false
                     ])
                     
                     ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            //traiter les images reçus via le formulaire
            $images = $form->get('images')->getData();
            foreach($images as $image) {
                //générer un nom de fichier aléatoire pour chauqe image
                $file = md5(uniqid() . '.' . $image->guessExtension());

                //copier le fichier dans le dossier uploads
                $image->move(
                    $this->getParameter('images_directory'),
                    $file
                );
            }

            $figure->setCreationDate(new \DateTime());
            $figure->setLastUpdateDate(new \DateTime());
            
            
            $manager->persist($figure);
            $manager->flush();
            return $this->redirectToRoute("home");
        }

        return $this->render('snowtricks/create_figure.html.twig', [
            'formFigure' => $form->createView()
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
        return $this->redirectToRoute("home");
    }

    
}
