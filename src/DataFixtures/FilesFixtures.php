<?php

namespace App\DataFixtures;

use App\Entity\Files;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class FilesFixtures extends Fixture implements DependentFixtureInterface
{

    private $encoderFactory;

    public function __construct(EncoderFactoryInterface $encoderFactory)
    {
        $this->encoderFactory = $encoderFactory;
    }

    public function load(ObjectManager $manager)
    {

        $category1 = $manager->getRepository('App:Categories')->findOneBy([ 'name' => 'Category 1' ]);
        $category2 = $manager->getRepository('App:Categories')->findOneBy([ 'name' => 'Category 2' ]);
        $category3 = $manager->getRepository('App:Categories')->findOneBy([ 'name' => 'Category 3' ]);
        $category4 = $manager->getRepository('App:Categories')->findOneBy([ 'name' => 'Category 4' ]);

        $pdf = new Files();
        $pdf->setName('pdf')
            ->setMime('application/pdf')
            ->setSize(93854)
            ->setUploadDate(new \DateTime('NOW'))
            ->addCategory($category1)
        ;
        $jpg = new Files();
        $jpg->setName('jpg')
            ->setMime('image/jpeg')
            ->setSize(938534)
            ->setUploadDate(new \DateTime('NOW'))
            ->addCategory($category2)
        ;

        $zip = new Files();
        $zip->setName('zip')
            ->setMime('application/zip')
            ->setSize(9385422222)
            ->setUploadDate(new \DateTime('NOW'))
            ->addCategory($category3)
        ;

        $xls = new Files();
        $xls->setName('xls')
            ->setMime('application/xls')
            ->setSize(58393)
            ->setUploadDate(new \DateTime('NOW'))
            ->addCategory($category4)
        ;

        $manager->persist($pdf);
        $manager->persist($jpg);
        $manager->persist($zip);
        $manager->persist($xls);
        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            CategoriesFixtures::class,
        );
    }
}
