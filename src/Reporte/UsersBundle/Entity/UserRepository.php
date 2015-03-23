<?php

namespace Reporte\UsersBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Parameter;

/**
 * UserRepository
 */
class UserRepository extends EntityRepository
{
    /*
     * Search all users order by username, it excludes root user
     */
    public function findAllNotRoot($limit, $offset)
    {
        $q = $this->createQueryBuilder('u')
            ->where('u.username != :name')
            ->setParameter(':name', 'root')
            ->orderBy('u.username', 'ASC');
    
        $q->setMaxResults($limit);
        $q->setFirstResult($offset);
        return $q->getEntities()->getResult();
    }
    
    /*
     * Search all users by username, user area or user role, it excludes root user
     */
    public function findLike($fields)
    {
        $q = $this->createQueryBuilder('u')
                    ->where('u.username LIKE :name')
                    ->andWhere('u.username != :root')
                    ->setParameters(new ArrayCollection(array( new Parameter(':name', '%'.$fields["username"].'%'),
                                                               new Parameter(':root', 'root') )));
        
        if(isset($fields["area"]))
        {
            $q->andWhere('u.area = :area');
            $q->setParameter(':area', $fields["area"]);
        }
        
        /*if(isset($fields["roles"]))
        {
            $q->andWhere('u.roles = :roles');
            $q->setParameter(':roles', $fields["roles"]);
        }*/

        return $q->getEntities()->getResult();
    }
    
    /*
     * Helper function to retreive all users mark as tecnico, it excludes root
     */
    public function findTecnicos()
    {
        $q = $this->createQueryBuilder('u')
                  ->join('u.myRoles', 'r')
                  ->where('u.username != :root')
                  ->andWhere('r.name = :role_admin OR r.name = :role_tec')
                  ->setParameters(new ArrayCollection(array( new Parameter(':root', 'root'),
                                                             new Parameter(':role_admin', 'Administrador'),
                                                             new Parameter(':role_tec', 'Tecnico'))))
                  ->getEntities();
        
        return $q->getResult();
    }
    
    /**
     * Counts all records in the Services table 
     */
    public function getCounter()
    {
        
        $qb = $this->createQueryBuilder('u')
                ->select('count(u.id)')
                ->where('u.username != :name')
                ->setParameter(':name', 'root')
                ->orderBy('u.username', 'ASC');
        
        return $qb->getEntities()->getSingleScalarResult();                
    }
    
}
