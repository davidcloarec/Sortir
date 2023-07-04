<?php

namespace App\Security;

use Symfony\Component\Security\Core\Exception\AccountStatusException;

class AccountDisabledException extends AccountStatusException
{

    /**
     * {@inheritdoc}
     */
    public function getMessageKey()
    {
        return 'Votre compte est désactivé.';
    }
}