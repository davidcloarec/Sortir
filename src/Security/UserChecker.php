<?php

namespace App\Security;

use App\Security\AccountDisabledException;
use App\Entity\User as AppUser;
use Symfony\Component\Security\Core\Exception\AccountExpiredException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{

    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof AppUser) {
            return;
        }

        // L’utilisateur n’est pas activé par l’administrateur
        if (!$user->isActive()) {
            throw new CustomUserMessageAccountStatusException("Vous n'avez pas les droits.");

        }

//        if ($user->isDeleted()) {
//            // the message passed to this exception is meant to be displayed to the user
//            throw new CustomUserMessageAccountStatusException('Your user account no longer exists.');
//        }
    }

    public function checkPostAuth(UserInterface $user): void
    {

    }

}