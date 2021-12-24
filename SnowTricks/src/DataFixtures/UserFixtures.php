<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;
use App\Entity\User;

class UserFixtures extends Fixture
{
    
    
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');

        for($i = 1; $i < 10; $i++) {

            $user = new User();

            $user->setUsername($faker->userName)
                 ->setEmail($faker->email)
                 ->setPassword(password_hash('123456', PASSWORD_DEFAULT))
                 ->setAvatar('http://via.placeholder.com/350x150');

            $manager->persist($user);

            //enregistrer l'utilisateur dans une référence
            $this->addReference('user_' . $i, $user);
        }

        $manager->flush();
    }
}
