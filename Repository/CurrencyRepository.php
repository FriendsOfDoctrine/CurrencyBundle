<?php

namespace Fod\Repository;

use Doctrine\ORM\EntityRepository;
use Fod\Entity\Currency;

/**
 * Class CurrencyRepository
 */
class CurrencyRepository extends EntityRepository
{
    /**
     * @param string $code
     *
     * @return Currency|null
     */
    public function getTodayCurrency(string $code)
    {
        $qb = $this->createQueryBuilder('c')
            ->andWhere('c.code = :code')
            ->setParameter('code', $code)
            ->andWhere('c.createdAt >= :createdAt')
            ->setParameter('createdAt', new \DateTime('today'))
            ->orderBy('c.createdAt', 'desc')
            ->setMaxResults(1);

        return $qb
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param string $code
     *
     * @return Currency|null
     */
    public function getYesterdayCurrency(string $code)
    {
        $qb = $this->createQueryBuilder('c')
            ->andWhere('c.code = :code')
            ->setParameter('code', $code)
            ->andWhere('c.createdAt >= :yesterday')
            ->setParameter('yesterday', new \DateTime('yesterday'))
            ->andWhere('c.createdAt < :today')
            ->setParameter('today', new \DateTime('today'))
            ->orderBy('c.createdAt', 'desc')
            ->setMaxResults(1);

        return $qb
            ->getQuery()
            ->getOneOrNullResult();
    }
}