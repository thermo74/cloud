<?php

namespace App\Controller\EasyAdmin;

use App\Entity\Categories;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CategoriesCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Categories::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Categories')
            ->setEntityLabelInPlural('Categories')
            ->setSearchFields(['id', 'name'])
            ->setPaginatorPageSize(20);
    }

    public function configureFields(string $pageName): iterable
    {
        $name = TextField::new('name');
        $roles = AssociationField::new('roles');
        $isActive = Field::new('is_active');
        $id = IntegerField::new('id', 'ID');
        $files = AssociationField::new('files');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $name, $roles, $isActive];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $name, $isActive, $roles, $files];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$name, $roles, $isActive];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$name, $roles, $isActive];
        }
    }
}
