<?php


namespace App\Service;


use App\Entity\Files;
use Symfony\Component\Security\Core\User\UserInterface;

class hasAccess
{
    public function index(Files $file, UserInterface $user): bool
    {
        $roles = $user->getRoles();
        if (in_array('ROLE_ADMIN', $roles) || in_array('ROLE_MODERATOR', $roles)){
            return true;
        }
        $fileCategories = $file->getCategories();
        foreach ($fileCategories as $category){
            $fileCategory[] = $category;
        }
        if (isset($fileCategory)){
            $subRoles = $user->getSubRoles();
            foreach ($subRoles as $role){
                if ($role->getIsActive()){
                    $subCats = $role->getCategories();
                    foreach ($subCats as $cat){
                        if (in_array($cat, $fileCategory)){
                            return true;
                        }
                    }
                }
            }
            return false;
        }
        return false;
    }
}