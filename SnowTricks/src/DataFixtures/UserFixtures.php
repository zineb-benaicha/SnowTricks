<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

        public function __construct(UserPasswordHasherInterface $hasher)
        {
            $this->hasher = $hasher;
        }
    
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');

        for($i = 1; $i < 10; $i++) {

            $user = new User();

            $user->setUsername($faker->userName)
                 ->setEmail($faker->email)
                 ->setPassword($this->hasher->hashPassword($user, '123456'))
                 ->setAvatar('http://via.placeholder.com/350x150');

            $manager->persist($user);

            //enregistrer l'utilisateur dans une référence
            $this->addReference('user_' . $i, $user);
        }

        $manager->flush();
    }
}
