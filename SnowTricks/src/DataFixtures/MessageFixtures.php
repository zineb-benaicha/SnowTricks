<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\Message;

class MessageFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies()
    {
        return [
            UserFixtures::class,
            FigureFixtures::class
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');
        
        for($i = 1; $i <8; $i++) {
            $message = new Message();

            $message->setContent($faker->text(200))
                    ->setCreationDate($faker->dateTime())
                    ->setUser($this->getReference('user_' . $faker->numberBetween(1, 9)))
                    ->setFigure($this->getReference('figure_' . $faker->numberBetween(1, 3)));

            $manager->persist($message);
        }
        $manager->flush();
    }
}
