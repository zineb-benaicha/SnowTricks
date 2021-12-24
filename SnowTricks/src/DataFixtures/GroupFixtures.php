<?php

namespace App\DataFixtures;

use App\Entity\Group;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class GroupFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $group = new Group('Pas de groupe');
        //$group->setName('Pas de groupe');
        $manager->persist($group);

        for ($i = 1; $i < 6; $i++) {
            $group = new Group("Groupe$i");
            //$group->setName("Groupe$i");
            $manager->persist($group);
        }
        $manager->flush();
    }
}
