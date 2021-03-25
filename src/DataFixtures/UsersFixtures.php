<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class UsersFixtures extends Fixture implements DependentFixtureInterface
{

    private $encoderFactory;

    public function __construct(EncoderFactoryInterface $encoderFactory)
    {
        $this->encoderFactory = $encoderFactory;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setName('Test')
            ->setLastname('LastNameTest')
            ->setEmail('user@test.localhost')
            ->setPhone('')
            ->setRoles([''])
            ->addSubRole($manager->getRepository('App:Roles')->findOneBy([ 'name' => 'Role1' ]))
            ->setIsVerified(false)
            ->setPassword($this->encoderFactory->getEncoder(User::class)->encodePassword('123456', null))
        ;

        $modo = new User();
        $modo->setName('Modo')
            ->setLastname('LastNameModo')
            ->setEmail('modo@test.localhost')
            ->setPhone('')
            ->setRoles(['ROLE_MODERATOR'])
            ->addSubRole($manager->getRepository('App:Roles')->findOneBy([ 'name' => 'Role1' ]))
            ->addSubRole($manager->getRepository('App:Roles')->findOneBy([ 'name' => 'Role2' ]))
            ->addSubRole($manager->getRepository('App:Roles')->findOneBy([ 'name' => 'Role3' ]))
            ->setIsVerified(false)
            ->setPassword($this->encoderFactory->getEncoder(User::class)->encodePassword('123456', null))
        ;

        $admin = new User();
        $admin->setName('Admin')
            ->setLastname('LastNameAdmin')
            ->setEmail('admin@test.localhost')
            ->setPhone('')
            ->setRoles(['ROLE_ADMIN', 'ROLE_MODERATOR'])
            ->setIsVerified(false)
            ->setPassword($this->encoderFactory->getEncoder(User::class)->encodePassword('123456', null))
        ;

        $manager->persist($user);
        $manager->persist($modo);
        $manager->persist($admin);
        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            RolesFixtures::class,
        );
    }
}
