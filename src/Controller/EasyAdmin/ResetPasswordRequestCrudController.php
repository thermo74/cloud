<?php

namespace App\Controller\EasyAdmin;

use App\Entity\ResetPasswordRequest;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ResetPasswordRequestCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ResetPasswordRequest::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setSearchFields(['id', 'selector', 'hashedToken'])
            ->setPaginatorPageSize(20);
    }

    public function configureFields(string $pageName): iterable
    {
        $id = IntegerField::new('id', 'ID');
        $requestedAt = DateField::new('requested_at');
        $expiresAt = DateField::new('expires_at');
        $user = AssociationField::new('user');
        $selector = TextField::new('selector');
        $hashedToken = TextField::new('hashedToken');
        $requestedAt = DateTimeField::new('requestedAt');
        $expiresAt = DateTimeField::new('expiresAt');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $requestedAt, $expiresAt, $user];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $selector, $hashedToken, $requestedAt, $expiresAt, $user];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$id, $requestedAt, $expiresAt, $user];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$requestedAt, $expiresAt, $user];
        }
    }
}
