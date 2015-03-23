<?php
/**
 * @author carlos
 * 
 */

namespace Reporte\PCBundle\Entity;

use Doctrine\ORM\EntityRepository;

class PCRepository extends EntityRepository{
    
    /**
     * Counts all records in the Services table 
     */
    public function getCounter()
    {
        
        $qb = $this->createQueryBuilder('pc')
                ->select('count(pc.id)');
        
        return $qb->getEntities()->getSingleScalarResult();                
    }
    
    
    /*
     * Search all pcs by number, pc area
     */
    public function findLike($fields)
    {
        $q = $this->createQueryBuilder('pc')
                    ->where('pc.inv LIKE :inv')
                    ->setParameter(':inv',  '%'.$fields["inv"].'%');
        
        if(isset($fields["area"]))
        {
            $q->andWhere('pc.area = :area');
            $q->setParameter(':area', $fields["area"]);
        }

        return $q->getEntities()->getResult();
    }
}

?>
