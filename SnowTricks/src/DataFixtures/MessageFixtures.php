<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;
use App\Entity\Message;
use App\Entity\User;
use App\Entity\Figure;

class MessageFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');
        
        for($i = 1; $i <8; $i++) {
            $message = new Message();
            $message->setContent($faker->text(200))
                    ->setCreationDate($faker->date_create())
                    ->setUser($this->getReference('user_' . $faker->random_int(1, 9)))
                    ->setFigure($this->getReference('figure_' . $faker->random_int(1,3)));
        }

        
        $manager->flush();
    }
}
