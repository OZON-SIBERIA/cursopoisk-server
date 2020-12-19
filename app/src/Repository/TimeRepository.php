<?php


namespace App\Repository;

use App\Entity\Time;
use Doctrine\ORM\EntityManagerInterface;

class TimeRepository
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    /**
     * @param Time $time
     */
    public function save(Time $time)
    {
        $this->entityManager->persist($time);
        $this->entityManager->flush();
    }

    /**
     * @param $criteria
     * @return Time[]
     */
    public function findBy($criteria): array
    {
        return $this->entityManager->getRepository(Time::class)->findBy($criteria);
    }

    /**
     * @param $criteria
     * @return object
     */
    public function findOneBy($criteria)
    {
        return $this->entityManager->getRepository(Time::class)->findOneBy($criteria);
    }

    /**
     * @param $criteria
     * @return object[]
     */
    public function findAll()
    {
        return $this->entityManager->getRepository(Time::class)->findAll();
    }
}