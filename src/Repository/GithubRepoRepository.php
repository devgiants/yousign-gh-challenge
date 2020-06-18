<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\GithubRepo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method GithubRepo|null find($id, $lockMode = null, $lockVersion = null)
 * @method GithubRepo|null findOneBy(array $criteria, array $orderBy = null)
 * @method GithubRepo[]    findAll()
 * @method GithubRepo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GithubRepoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GithubRepo::class);
    }
}
