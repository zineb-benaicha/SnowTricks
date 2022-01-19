<?php

namespace App\DataFixtures;

use App\Entity\Media;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;


class MediaFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies()
    {
        return [
            FigureFixtures::class
        ];
    }

    public function load(ObjectManager $manager): void
    {

        
        for($i = 1; $i < 4; $i++) {
            $image = new Media();

            $image->setType(Media::IMAGE_TYPE)
              ->setUrl('../../public/image/snowboard.jpg')
              ->setIsPrincipal(true)
              ->setFigure($this->getReference('figure_' . $i))
              ->setIsPrincipal(true);
            
            $manager->persist($image);
            
        }
        $manager->flush();
    }
}
