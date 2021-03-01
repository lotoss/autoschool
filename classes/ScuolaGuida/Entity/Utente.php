<?php

namespace ScuolaGuida\Entity;

class Utente
{
    const ADMIN_PERMISSIONS = 2;
    const STUDENTE_PERMISSIONS = 1;

    public function hasPermission($permission): bool
    {
        return $this->permissions >= $permission;
    }
}
