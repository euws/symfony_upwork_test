<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserService
{
    protected $em;
    protected $encoder;

    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $encoder)
    {
        $this->em = $em;
        $this->encoder = $encoder;
    }

    public function createUser(string $username, string $email, string $password): User
    {
        $user = new User($username);
        $user->setPassword($this->encoder->encodePassword($user, $password));
        $user->setEmail($email);
        $user->setUsername($username);
        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    public function editUser(User $user, string $email, string $username): User
    {
        $user->setEmail($email);
        $user->setUsername($username);
        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    public function changePassword(User $user, string $password): User
    {
        $user->setPassword($this->encoder->encodePassword($user, $password));
        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }
}
