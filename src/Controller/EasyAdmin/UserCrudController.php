<?php

namespace App\Controller\EasyAdmin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setSearchFields(['id', 'email', 'roles', 'name', 'lastname', 'phone'])
            ->setPaginatorPageSize(20);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable('new');
    }

    public function configureFields(string $pageName): iterable
    {
        $id = IntegerField::new('id', 'ID');
        $email = EmailField::new('email');
        $name = TextField::new('name');
        $lastname = TextField::new('lastname');
        $phone = TextField::new('phone');
        $roles = ArrayField::new('roles');
        $password = TextField::new('password');
        $isVerified = Field::new('isVerified');
        $subRoles = AssociationField::new('subRoles');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $email, $name, $lastname, $isVerified];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $email, $roles, $password, $name, $lastname, $phone, $isVerified, $subRoles];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$email, $name, $lastname, $phone, $isVerified];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$email, $name, $lastname, $phone, $roles, $isVerified];
        }
    }
}
