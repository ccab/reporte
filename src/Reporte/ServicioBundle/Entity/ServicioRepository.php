<?php

namespace Reporte\ServicioBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * ServicioRepository
 */
class ServicioRepository extends EntityRepository
{
    /*
     * Search all Services by author area and by user, sort by date.
     * Limit and offset params available
     */
    public function findOTArea($criteria, $limit, $offset)
    {
        $q = $this->createQueryBuilder('s')
                    ->join('s.autor', 'a')
                    ->where('a.area = :area')
                    ->orderBy('s.fecha_reporte', 'DESC')
                    ->setParameter('area', $criteria["area"]);

        if(isset($criteria["user"]))
        {
            $q->andWhere('s.usuario LIKE :user');
            $q->setParameter(':user', '%'.$criteria["user"].'%');
        }
        
        $q->setMaxResults($limit);
        $q->setFirstResult($offset);
        return $q->getEntities()->getResult();
    }
    
    /*
     * Search all Services by user and by author area, sort by date
     */
    public function findOTLike($fields)
    {
        $q = $this->createQueryBuilder('s')
                    ->join('s.autor', 'a')
                    ->where('s.usuario LIKE :user')
                    ->orderBy('s.fecha_reporte', 'DESC')
                    ->setParameter(':user', '%'.$fields["usuario"].'%');
                    

        if(isset($fields["area"]))
        {
            $q->andWhere('a.area = :area');
            $q->setParameter(':area', $fields["area"]);
        }
        
        return $q->getEntities()->getResult();
    }
    
    /**
     * Counts all records in the Services table 
     */
    public function getCounter()
    {
        
        $qb = $this->createQueryBuilder('s')
                ->select('count(s.id)');
        
        return $qb->getEntities()->getSingleScalarResult();                
    }
    
    /**
     * Counts all records in the Services table of a given Area
     */
    public function getCounterOTArea($criteria)
    {
        $qb = $this->createQueryBuilder('s')
                ->select('count(s.id)')
                ->join('s.autor', 'a')
                ->where('a.area = :area')
                ->setParameter('area', $criteria["area"]);
        
        return $qb->getEntities()->getSingleScalarResult();
    }
}
