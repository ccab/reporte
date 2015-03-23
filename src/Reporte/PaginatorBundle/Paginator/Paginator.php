<?php
namespace Reporte\PaginatorBundle\Paginator;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Paginator
{
    //how many links to pages show in the paginator
    private $pages_show;
    //how many items show in a page
    private $items_per_page;
    private $em;
    //paginator elements
    private $total;
    private $first;
    private $previous;
    private $current;
    private $range;
    private $next;
    private $last;
    private $url;
    
    public function __construct($pages_show, $items_per_page, EntityManager $em) 
    {
        $this->pages_show = $pages_show;
        $this->items_per_page = $items_per_page;
        $this->em = $em;
    }
    
    /**
     * Set the paginator elements, call this method before fetching the resulset
     * to get the exception handling or any error before actually worry about the records.
     * If your records doesn't require more than one page the paginator doesn't show up at all
     */
    public function paginate($page, $counter)
    {      
        $max = ceil($counter/$this->items_per_page);
        $range = array();
             
        $start = $this->getStart($page, $max);
        $end = $start + ($this->pages_show - 1) > $max ? $max  : $start + ($this->pages_show - 1);
            
        for ($i = $start; $i <= $end; $i++) 
            $range[] = $i == $page ? array('number' => $i, 'current' => true) : array('number' => $i, 'current' => false);
        
        if ($max > 1)
        {
            $this->setRange($range);
            $this->setTotal($counter);
            $this->setFirst($page == 1 ? null : 1);
            $this->setPrevious($page-1 <= 0 ? null : $page-1);
            $this->setCurrent($page);
            $this->setNext($page+1 > $max ? null : $page+1);
            $this->setLast($page >= $max ? null : $max); 
        }                      
    }
    
    /**
     * Helper function to know the first number in the paginator links
     */
    private function getStart($page, $max)
    {
        if ($max == 0)
            return;
        if ($page > $max || $page < 1)
            throw new NotFoundHttpException("No se encuentra la pagina");
            
        $offset = floor(($this->pages_show -1)/2);
        
        $start = ($page - $offset <= 0) || ($this->pages_show >= $max) ? 1 : $page - $offset;
        
        if (($max >= $this->pages_show) && ($start + ($this->pages_show - 1) > $max))
            $start -= ($start + ($this->pages_show -1)) - $max;
        
        return $start;
    }

    public function getPagesShow() {
        return $this->pages_show;
    }

    public function setPagesShow($pages_show) {
        $this->pages_show = $pages_show;
    }

    public function getTotal() {
        return $this->total;
    }

    public function setTotal($total) {
        $this->total = $total;
    }

    public function getFirst() {
        return $this->first;
    }

    public function setFirst($first) {
        $this->first = $first;
    }

    public function getPrevious() {
        return $this->previous;
    }

    public function setPrevious($previous) {
        $this->previous = $previous;
    }

    public function getCurrent() {
        return $this->current;
    }

    public function setCurrent($current) {
        $this->current = $current;
    }

    public function getNext() {
        return $this->next;
    }

    public function setNext($next) {
        $this->next = $next;
    }

    public function getLast() {
        return $this->last;
    }

    public function setLast($last) {
        $this->last = $last;
    }

    public function getUrl() {
        return $this->url;
    }

    public function setUrl($url) {
        $this->url = $url;
    }

    public function getRange() {
        return $this->range;
    }

    public function setRange($range) {
        $this->range = $range;
    }


}
?>
