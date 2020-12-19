<?php


namespace App\Repository;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserRepository
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @var UserPasswordEncoderInterface
     */
    private UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(EntityManagerInterface $entityManager,
                                UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
    }


    /**
     * Create a new user.
     *
     * @param string $firstname
     * @param string $lastname
     * @param string $email
     * @param string $password
     * @param UserPasswordEncoderInterface $passwordEncoder
     *
     * @return User
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createUser(string $firstname, string $lastname, string $email,
                               string $password): User
    {
        $user = new User();
        $user->setUserName($firstname);
        $user->setLastname($lastname);
        $user->setEmail($email);
        $user->setPassword(
            $this->passwordEncoder->encodePassword($user, $password)
        );

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    /**
     * @param $criteria
     * @return User[]
     */
    public function findBy($criteria): array
    {
        return $this->entityManager->getRepository(User::class)->findBy($criteria);
    }

    public function findOneBy($criteria)
    {
        return $this->entityManager->getRepository(User::class)->findOneBy($criteria);
    }

    public function saveUserToken($user, $token)
    {
        $user->setToken($token);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}