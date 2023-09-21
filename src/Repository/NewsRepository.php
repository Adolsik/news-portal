<?php

namespace App\Repository;

use App\Entity\News;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<News>
 *
 * @method News|null find($id, $lockMode = null, $lockVersion = null)
 * @method News|null findOneBy(array $criteria, array $orderBy = null)
 * @method News[]    findAll()
 * @method News[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NewsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, News::class);
    }

    public function FindByCategory(int $id){

        $qb = $this->createQueryBuilder('n');
        $qb->select('n.title')
        ->addSelect('n.content')
        ->addSelect('n.create_date as createdate')
        ->addSelect('n.thumbnail')
        ->addSelect('c.id as cat_id')
        ->addSelect('c.name as category')
        ->addSelect('n.id as id')
        ->innerJoin('n.category', 'c')
        ->where('c.id = :id')
        ->setParameter('id', $id);

        return $qb->getQuery()->getResult();
    }

    public function FindLatestNews(){
        $qb = $this->createQueryBuilder('n');
        $qb->select('n.title')
        ->addSelect('n.content')
        ->addSelect('n.thumbnail')
        ->addSelect('n.id')
        ->orderBy('n.id','DESC')
        ->setMaxResults(3);
        
        return $qb->getQuery()->getResult();
    }

    public function DeleteNews(int $id){
        $qb = $this->createQueryBuilder('n');
        $qb->delete()
        ->where('n.id = :id')
        ->setParameter('id', $id);

        return $qb->getQuery()->getResult();
    }

//    
}
