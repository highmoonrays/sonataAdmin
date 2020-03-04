<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Hslavich\OneloginSamlBundle\Security\Authentication\Token\SamlTokenInterface;
use Hslavich\OneloginSamlBundle\Security\User\SamlUserFactoryInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserFactory implements SamlUserFactoryInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * UserFactory constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param SamlTokenInterface $token
     * @return User|UserInterface
     */
    public function createUser(SamlTokenInterface $token): User
    {
        $attributes = $token->getAttributes();
        $user = new User();
        $user->setRoles(array("ROLE_USER"));
        $user->setEmail($token->getUsername());
        $user->setPassword('notused');

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }
}
