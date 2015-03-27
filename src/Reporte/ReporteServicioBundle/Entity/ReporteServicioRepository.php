<?php

namespace Reporte\ReporteServicioBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * ReporteServicioRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ReporteServicioRepository extends EntityRepository
{
    /**
     * Search all reportes in a date range
     */
    public function findRSDateRange($date)
    {
        $q = $this->createQueryBuilder('r')
                    ->where('r.fecha_reporte > :start AND r.fecha_reporte < :end')
                    ->orderBy('r.fecha_reporte','DESC')
                    ->setParameter(':start', $date['start'])
                    ->setParameter(':end', $date['end'])
                    ->getEntities();

        return $q->getResult();
    }
    
    /**
     * Counts all records in the Reportes table 
     */
    public function getCounter()
    {
        
        $qb = $this->createQueryBuilder('s')
                ->select('count(s.id)');
        
        return $qb->getEntities()->getSingleScalarResult();                
    }
    
    
    /**
     * Counts all records in the Reportes table 
     */
    public function getCounterPorArea($criteria)
    {
        
        $qb = $this->createQueryBuilder('s')
                ->select('count(s.id)')
                ->where('s.autor_area_nombre = :area')
                ->setParameter('area', $criteria["area"]);
        
        return $qb->getEntities()->getSingleScalarResult();                
    }
    
    
    /*
     * Search all users by username, user area or user role, it excludes root user
     */
    public function findLike($fields)
    {
        $q = $this->createQueryBuilder('rs')
                    ->where('rs.usuario LIKE :name')
                    ->setParameter(':name', '%'.$fields["usuario"].'%');
        
        if(isset($fields["area"]))
        {
            $q->andWhere('rs.autor_area_nombre = :area');
            $q->setParameter(':area', $fields["area"]->getNombre());
        }

        return $q->getEntities()->getResult();
    }
}
