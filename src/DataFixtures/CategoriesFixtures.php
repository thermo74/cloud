<?php

namespace App\DataFixtures;

use App\Entity\Categories;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CategoriesFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $role1 = $manager->getRepository('App:Roles')->findOneBy([ 'name' => 'Role1' ]);
        $role2 = $manager->getRepository('App:Roles')->findOneBy([ 'name' => 'Role2' ]);
        $role3 = $manager->getRepository('App:Roles')->findOneBy([ 'name' => 'Role3' ]);
        $role4 = $manager->getRepository('App:Roles')->findOneBy([ 'name' => 'Role4' ]);

        $category1 = new Categories();
        $category1->setName('Category 1')
            ->setIsActive(true)
            ->addRole($role1)
        ;

        $category2 = new Categories();
        $category2->setName('Category 2')
            ->setIsActive(true)
            ->addRole($role2)
        ;

        $category3 = new Categories();
        $category3->setName('Category 3')
            ->setIsActive(true)
            ->addRole($role3)
        ;

        $category4 = new Categories();
        $category4->setName('Category 4')
            ->setIsActive(true)
            ->addRole($role4)
        ;

        $manager->persist($category1);
        $manager->persist($category2);
        $manager->persist($category3);
        $manager->persist($category4);
        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
          RolesFixtures::class,
        );
    }
}
