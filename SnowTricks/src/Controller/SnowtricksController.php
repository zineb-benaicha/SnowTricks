<?php

namespace App\Controller;

use App\Entity\Figure;
use App\Entity\Group;
use App\Entity\Media;
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
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\File;


class SnowtricksController extends AbstractController
{
    public function extractVideoUrlFromEmbedBalise(String $embed = null)
    {
        if($embed === null) {
            return false;
        }
        else
        {
            $balisesList = explode(",", $embed);

            if(!empty($balisesList)){

                foreach($balisesList as $balise) {
                    //supprimer l'element s'il sagit d'une case vide
                    if($balise === ''){
                        unset($balisesList[array_search($balise, $balisesList)]);
                    }
                    else
                    {
                        //convertir la chaine en un tableau
                        $balise_array = str_split($balise);
                        //cherche la position du parametre src
                        $position_src_debut = strpos($balise, 'src="') + 5;

                        //si le mot clé src a été trouvé
                        if($position_src_debut > 5) {
                            //chercher la fin du parametre src
                            $i = $position_src_debut;
                            while($balise_array[$i] !== '"'){
                                $i++;
                            }
                            $position_src_fin = $i;
                            $lenght = $position_src_fin - $position_src_debut;
                            $srcBalisesList[] = substr($balise, $position_src_debut, $lenght);
                        }
                    }
                }
                return $srcBalisesList;
                
            }
        }
    }
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
                     ->add('videos', TextareaType::class,[
                         'mapped' => false,
                         'required' => false,
                         'attr' => [
                             'placeholder' => 'insérez les balises embed ici, et séparer les avec des virgules'
                         ]
                     ])                    
                     ->getForm();

        $form->handleRequest($request);
        
       
        if($form->isSubmitted() && $form->isValid()) {
            
        // 1- traiter les images reçus via le formulaire
            $images = $form->get('images')->getData();
            
            if ($images) {
                foreach($images as $key => $image) {
                    //générer un nom de fichier aléatoire pour chaque image
                    $file = md5(uniqid()) . '.' . $image->guessExtension();
                    //var_dump($image->guessExtension());

                    //copier le fichier dans le dossier uploads
                    $image->move(
                        $this->getParameter('images_directory'),
                        $file
                    );

                    //enregistrer l'image au niveau de la BDD
                    $img = new Media();
                    $img->setUrl($file);
                    $img->setType(Media::IMAGE_TYPE);
                    if($key == 0) {
                        $img->setIsPrincipal(true);
                    }
                    else {
                        $img->setIsPrincipal(false);
                    }
                    //lier l'image à la figure
                    $figure->addMediaList($img);
                }
            }
            //l'utilisateur n'a choisi aucune image mettre une image par defaut
            else {
                //enregistrer l'image au niveau de la BDD
                $img = new Media();
                $img->setUrl('snowboard.jpg');
                $img->setType(Media::IMAGE_TYPE);
                $img->setIsPrincipal(true);
                //lier l'image à la figure
                $figure->addMediaList($img);
            } 

        // 2- traiter les video reçus via le formulaire
        $videos = $form->get('videos')->getData();
            //récupérer les URLs reçus sous forme de tableau
        $videoListUrl = $this->extractVideoUrlFromEmbedBalise($videos);

        if(!empty($videoListUrl)){
            foreach($videoListUrl as $videoUrl) {
                $video = new Media();
                $video->setType(Media::VIDEO_TYPE);
                $video->setUrl($videoUrl);
                $video->setIsPrincipal(false);
                //lier la video à la figure
                $figure->addMediaList($video);
            }
        }

        $figure->setCreationDate(new \DateTime());
        $figure->setLastUpdateDate(new \DateTime());

        //Enregistrer le figure dans la BDD
        $manager->persist($figure);
        $manager->flush();

            return $this->redirectToRoute("home");
        }

        return $this->render('snowtricks/create_figure.html.twig', [
            'formFigure' => $form->createView()
        ]);
    }

    /**
     * @Route("figure/{id}/edit", name="figure_edit")
     */
    public function updateFigure(Figure $figure,Request $request, ManagerRegistry $doctrine)
    {
        
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
                     ->add('videos', TextareaType::class,[
                         'mapped' => false,
                         'required' => false,
                         'attr' => [
                             'placeholder' => 'insérez les balises embed ici, et séparer les avec des virgules'
                         ]
                     ])
                     ->add('principal_image', FileType::class, [
                        'multiple' => false,
                        'mapped' => false,
                        'required' => false
                 ])                  
                     ->getForm();

        $form->handleRequest($request);

        //I- Si le formulaire a été soumis traiter les informations issues du formulaire
            if($form->isSubmitted() && $form->isValid()) {
                
            // 1- traiter les images reçus via le formulaire
                $images = $form->get('images')->getData();

                foreach($images as $key => $image) {
                    //générer un nom de fichier aléatoire pour chaque image
                    $file = md5(uniqid()) . '.' . $image->guessExtension();
                    //var_dump($image->guessExtension());

                    //copier le fichier dans le dossier uploads
                    $image->move(
                        $this->getParameter('images_directory'),
                        $file
                    );

                    //enregistrer l'image au niveau de la BDD
                    $img = new Media();
                    $img->setUrl($file);
                    $img->setType(Media::IMAGE_TYPE);
                    if($key == 0) {
                        $img->setIsPrincipal(true);
                    }
                    else {
                        $img->setIsPrincipal(false);
                    }
                    //lier l'image à la figure
                    $figure->addMediaList($img);
                }

            // 2- traiter l'image principale reçue
                if ($form->get('principal_image')->getData()) {

                    $principalImageReceived = $form->get('principal_image')->getData();
                    //générer un nom de fichier aléatoire pour l'image principale
                    $file = md5(uniqid()) . '.' . $principalImageReceived->guessExtension();
                    //copier le fichier dans le dossier uploads
                    $principalImageReceived->move(
                        $this->getParameter('images_directory'),
                        $file
                    );

                    $principalImage = $figure->getPrincipalImage();
                    
                    $principalImage->setUrl($file);
                    
                    $figure->setPrincipalImage($principalImage);
                }
            // 3- traiter les video reçus via le formulaire
                $videos = $form->get('videos')->getData();
                //récupérer les URLs reçus sous forme de tableau
                $videoListUrl = $this->extractVideoUrlFromEmbedBalise($videos);

                if(!empty($videoListUrl)){
                    foreach($videoListUrl as $videoUrl) {
                        $video = new Media();
                        $video->setType(Media::VIDEO_TYPE);
                        $video->setUrl($videoUrl);
                        $video->setIsPrincipal(false);
                        //lier la video à la figure
                        $figure->addMediaList($video);
                    }
                }
            

            // 4- mettre a jour la date de mise à jour
                $figure->setLastUpdateDate(new \DateTime());

            // 5-Enregistrer le figure dans la BDD
                $manager->persist($figure);
                $manager->flush();

                return $this->redirectToRoute("home");
            }
        //II- Si le formulaire n'a pas été soumis, envoyer la liste des images et vidéos de la figure
       
        return $this->render('snowtricks/edit_figure.html.twig', [
            'formFigure' => $form->createView(),
            'imagesList' => $figure->getImagesList(),
            'videosList' => $figure->getVideosList()
        ]);
    }

    /**
     * @Route("/image/{id}/edit", name="image_edit")
     */
    public function updateImage(Media $media, ManagerRegistry $doctrine, Request $request)
    {
        $form = $this->createFormBuilder($media)
                     ->add('image', FileType::class, [
                            'multiple' => false,
                            'mapped' => false,
                            'required' => false
                     ])
                   
                     ->getForm();

        $form->handleRequest($request);
        
       
        if($form->isSubmitted() && $form->isValid()) {
            

        }
        
    }
    /**
     * @Route("/video/{id}/edit", name="video_edit")
     */
    public function updateVideo(Media $media, ManagerRegistry $doctrine, Request $request)
    {
        $manager = $doctrine->getManager();

        $form = $this->createFormBuilder($media)
                     ->add('url', TextType::class, [
                            'mapped' => true,
                            'required' => false
                     ])
                   
                     ->getForm();

        $form->handleRequest($request);
       
        if($form->isSubmitted() && $form->isValid()) {
            $url = $form->get('url')->getData();
            $media->setUrl($url);
            $manager->persist($media);
            $manager->flush();
            return $this->redirectToRoute("figure_edit", ['id' => $media->getFigure()->getId()]);
        }
        return $this->redirectToRoute("figure_edit", [
           'id' => $media->getFigure()->getId(),
           'editVideo' => 1
        ]);
    
        
    }
    /**
     * @Route("/media/{id}/delete", name="media_delete")
     */
    public function deleteMedia(Media $media, ManagerRegistry $doctrine): RedirectResponse
    {
        $manager = $doctrine->getManager();
        $manager->remove($media);
        $manager->flush();
        return $this->redirectToRoute("figure_edit", ['id' => $media->getFigure()->getId()]);

        
    }

    /**
     * @Route("/figure/{id}", name="figure_show")
     */
    public function showFigure(Figure $figure)
    {

        return $this->render('snowtricks/show_figure.html.twig',[
            'figure' => $figure,
            'imagePrincipale' => $figure->getPrincipalImage()->getUrl(),
            'imageList' => $figure->getImagesList(),
            'videoList' => $figure->getVideosList(),
            'messages' => $figure->getMessagesList()
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
