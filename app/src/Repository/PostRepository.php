<?php


namespace App\Repository;


use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;

class PostRepository
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
     * @param Post $post
     */
    public function save(Post $post)
    {
        $this->entityManager->persist($post);
        $this->entityManager->flush();
    }
}