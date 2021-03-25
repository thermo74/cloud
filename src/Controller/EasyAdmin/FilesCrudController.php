<?php

namespace App\Controller\EasyAdmin;

use App\Entity\Files;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class FilesCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Files::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Files')
            ->setEntityLabelInPlural('Files')
            ->setSearchFields(['id', 'name', 'mime', 'size', 'path'])
            ->setPaginatorPageSize(20);
    }

    public function configureFields(string $pageName): iterable
    {
        $name = TextField::new('name');
        $categories = AssociationField::new('categories');
        $mime = TextField::new('mime');
        $size = IntegerField::new('size');
        $uploadDate = DateField::new('upload_date');
        $path = TextField::new('path');
        $id = IntegerField::new('id', 'ID');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $name, $categories, $mime, $size, $uploadDate];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $name, $mime, $size, $uploadDate, $path, $categories];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$name, $categories, $mime, $size, $uploadDate, $path];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$name, $categories, $mime, $size, $uploadDate, $path];
        }
    }
}
