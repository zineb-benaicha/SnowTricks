<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Group;
use App\Entity\Figure;


class FigureFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {     

        $figures = [
            1 => [
                'name' => 'mute',
                'description' => 'saisie de la carre frontside de la planche entre les deux pieds avec la main avant ;',
                'groupe' => new Group('Groupe1')
            ],

            2 => [
                'name' => 'sad',
                'description' => 'saisie de la carre backside de la planche, entre les deux pieds, avec la main avant ;',
                'groupe' => new Group('Groupe2')
            ],

            3 => [
                'name' => 'indy ',
                'description' => 'saisie de la carre backside de la planche, entre les deux pieds, avec la main avant ;',
                'groupe' => new Group('Pas de groupe')
            ]
        ];

        foreach( $figures as $key => $value) {

            $figure = new Figure();

            $figure->setName($value['name'])
                   ->setDescription($value['description'])
                   ->setCreationDate(new \DateTime())
                   ->setLastUpdateDate(new \DateTime())
                   ->setGroupe($value['groupe']);
            
            $manager->persist($figure);

            //enregistrer la figure dans une référence
            $this->addReference('figure_' . $key, $figure);
        }

        $manager->flush();

    }
}
