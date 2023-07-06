<?php

namespace App\DataFixtures;

use App\Entity\Participant;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $userPasswordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail("admin@sortir.com");
        $user->setUsername("admin");
        $user->setActive(true);
        $user->setPassword(
            $this->userPasswordHasher->hashPassword(
                $user,
                'Pa$$w0rd'
            )
        );
        $user->setRoles(["ROLE_ADMIN"]);
        $user->setResetToken("qdquipufhmzquegzqfblzdvhqbf");
        $participant = new Participant();
        $participant->setUser($user);
        $participant->setPhone("0000000000");
        $participant->setAdmin(true);
        $participant->setMail($user->getEmail());
        $participant->setFirstname("DAD");
        $participant->setLastname("Min");
        $participant->setCampus(null);
        $participant->setImage(null);
        $user->setParticipant($participant);
        $manager->persist($user);
        $manager->persist($participant);
        $manager->flush();
    }
}