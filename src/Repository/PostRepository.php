<?php

namespace App\Repository;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Post>
 *
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function findTopPosts(?string $interval = 'month'): array
    {
        $qb = $this->createQueryBuilder('p');
        $qb->leftJoin('p.likes', 'l')
            ->groupBy('p.id');
    
        switch ($interval) {
            case 'week':
                $qb->where('WEEK(p.date_creation) = WEEK(CURRENT_DATE())');
                break;
            case 'year':
                $qb->where('YEAR(p.date_creation) = YEAR(CURRENT_DATE())');
                break;
            case 'month':
            default:
                $qb->where('MONTH(p.date_creation) = MONTH(CURRENT_DATE())');
                break;
        }
    
        $qb->orderBy('COUNT(l.id)', 'DESC')
           ->setMaxResults(10);
    
        return $qb->getQuery()->getResult();
    }

    public function findPostsByUser(User $user)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

}
