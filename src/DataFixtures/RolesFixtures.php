<?php

namespace App\DataFixtures;

use App\Entity\Roles;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RolesFixtures extends Fixture
{
    public const ROLE_1 = 'Role1';
    public const ROLE_2 = 'Role2';
    public const ROLE_3 = 'Role3';
    public const ROLE_4 = 'Role4';

    public function load(ObjectManager $manager)
    {
        $role1 = new Roles();
        $role1->setName('Role1')
            ->setIsActive(true);

        $role2 = new Roles();
        $role2->setName('Role2')
            ->setIsActive(true);

        $role3 = new Roles();
        $role3->setName('Role3')
            ->setIsActive(true);

        $role4 = new Roles();
        $role4->setName('Role4')
            ->setIsActive(true);

        $manager->persist($role1);
        $manager->persist($role2);
        $manager->persist($role3);
        $manager->persist($role4);
        $manager->flush();

        $this->addReference(self::ROLE_1, $role1);
        $this->addReference(self::ROLE_2, $role2);
        $this->addReference(self::ROLE_3, $role3);
        $this->addReference(self::ROLE_4, $role4);

    }
}
