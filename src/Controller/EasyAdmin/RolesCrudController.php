<?php

namespace App\Controller\EasyAdmin;

use App\Entity\Roles;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class RolesCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Roles::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Roles')
            ->setEntityLabelInPlural('Roles')
            ->setSearchFields(['id', 'name'])
            ->setPaginatorPageSize(20);
    }

    public function configureFields(string $pageName): iterable
    {
        $name = TextField::new('name');
        $categories = AssociationField::new('categories');
        $users = AssociationField::new('users');
        $isActive = Field::new('is_active');
        $id = IntegerField::new('id', 'ID');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $name, $categories, $users, $isActive];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $name, $isActive, $categories, $users];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$name, $categories, $users, $isActive];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$name, $categories, $users, $isActive];
        }
    }
}
