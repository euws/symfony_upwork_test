<?php

namespace App\Service;

class UserRegistrationEmailTokenHasher
{
    public function generateToken(in $id):string
    {
        return bin2hex(random_bytes(16));
    }
}
