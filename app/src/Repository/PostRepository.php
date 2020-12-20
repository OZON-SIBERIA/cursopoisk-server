<?php


namespace App\Repository;


use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;

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

    /**
     * @param $criteria
     * @return Post[]
     */
    public function findBy($criteria): array
    {
        return $this->entityManager->getRepository(Post::class)->findBy($criteria);
    }

    /**
     * @param $criteria
     * @return object
     */
    public function findOneBy($criteria)
    {
        return $this->entityManager->getRepository(Post::class)->findOneBy($criteria);
    }

    /**
     * @param $criteria
     * @return object[]
     */
    public function findAll()
    {
        return $this->entityManager->getRepository(Post::class)->findAll();
    }

    public function getMaxPages($criteria, $limit)
    {
        return round($this->entityManager->getRepository(Post::class)->count($criteria) / $limit);
    }

    public function findPaginate($criteria, $page, $limit)
    {
        $maxPages = $this->getMaxPages($criteria, $limit);

        if ($maxPages <= 1) {
            $offset = null;
        } else {
            $offset = $limit * ($page - 1);
        }

        return $this->entityManager->getRepository(Post::class)->findBy($criteria, array(), $limit, $offset);
    }
}