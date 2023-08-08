<?php

    namespace App\Repository;

    use App\Entity\Paragraph;
    use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
    use Doctrine\Persistence\ManagerRegistry;

    /**
     * @extends ServiceEntityRepository<Paragraph>
     *
     * @method Paragraph|null find($id, $lockMode = null, $lockVersion = null)
     * @method Paragraph|null findOneBy(array $criteria, array $orderBy = null)
     * @method Paragraph[]    findAll()
     * @method Paragraph[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
     */
    class ParagraphRepository extends ServiceEntityRepository
    {
        public function __construct(ManagerRegistry $registry)
        {
            parent::__construct($registry, Paragraph::class);
        }

        public function save(Paragraph $entity, bool $flush = false): void
        {
            $this->getEntityManager()->persist($entity);

            if ($flush) {
                $this->getEntityManager()->flush();
            }
        }

        public function remove(Paragraph $entity, bool $flush = false): void
        {
            $this->getEntityManager()->remove($entity);

            if ($flush) {
                $this->getEntityManager()->flush();
            }
        }

//    /**
//     * @return Paragraph[] Returns an array of Paragraph objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Paragraph
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    }
