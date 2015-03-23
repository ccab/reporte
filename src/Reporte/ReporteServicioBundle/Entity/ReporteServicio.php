<?php

namespace Reporte\ReporteServicioBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ReporteServicio
 *
 * @ORM\Table(name="reporte_servicio")
 * @ORM\Entity(repositoryClass="Reporte\ReporteServicioBundle\Entity\ReporteServicioRepository")
 */
class ReporteServicio
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="pc_inv", type="string", length=25)
     */
    protected $pc_inv;

    /**
     * @var string
     *
     * @ORM\Column(name="pc_area_nombre", type="string", length=50)
     */
    protected $pc_area_nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="pc_marca_nombre", type="string", length=50)
     */
    private $pc_marca_nombre;

    /**
     * @var boolean
     *
     * @ORM\Column(name="pc_es_laptop", type="boolean")
     */
    private $es_laptop;

    /**
     * @var string
     *
     * @ORM\Column(name="autor_username", type="string", length=50)
     */
    private $autor_username;

    /**
     * @var string
     *
     * @ORM\Column(name="autor_area_nombre", type="string", length=50)
     */
    private $autor_area_nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="tecnico_username", type="string", length=50)
     */
    private $tecnico_username;

    /**
     * @var string
     *
     * @ORM\Column(name="tecnico_area_nombre", type="string", length=50)
     */
    private $tecnico_area_nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="usuario", type="string", length=50)
     */
    protected $usuario;

    /**
     * @var string
     *
     * @ORM\Column(name="problema", type="text")
     */
    protected $problema;

    /**
     * @var string
     *
     * @ORM\Column(name="labor_realizada", type="text")
     */
    protected $labor_realizada;

    /**
     * @var string
     *
     * @ORM\Column(name="piezas", type="text", nullable=true)
     */
    private $piezas;

    /**
     * @var boolean
     *
     * @ORM\Column(name="piezas_recuperadas", type="boolean", nullable=true)
     */
    private $piezas_recuperadas;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_reporte", type="date")
     */
    protected $fecha_reporte;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_solucion", type="date")
     */
    protected $fecha_solucion;

    /**
     * @var string
     *
     * @ORM\Column(name="observacion", type="text", nullable=true)
     */
    protected $observacion;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set pc_inv
     *
     * @param string $pcInv
     * @return ReporteServicio
     */
    public function setPcInv($pcInv)
    {
        $this->pc_inv = $pcInv;

        return $this;
    }

    /**
     * Get pc_inv
     *
     * @return string 
     */
    public function getPcInv()
    {
        return $this->pc_inv;
    }

    /**
     * Set pc_area_nombre
     *
     * @param string $pcAreaNombre
     * @return ReporteServicio
     */
    public function setPcAreaNombre($pcAreaNombre)
    {
        $this->pc_area_nombre = $pcAreaNombre;

        return $this;
    }

    /**
     * Get pc_area_nombre
     *
     * @return string 
     */
    public function getPcAreaNombre()
    {
        return $this->pc_area_nombre;
    }

    /**
     * Set pc_marca_nombre
     *
     * @param string $pcMarcaNombre
     * @return ReporteServicio
     */
    public function setPcMarcaNombre($pcMarcaNombre)
    {
        $this->pc_marca_nombre = $pcMarcaNombre;

        return $this;
    }

    /**
     * Get pc_marca_nombre
     *
     * @return string 
     */
    public function getPcMarcaNombre()
    {
        return $this->pc_marca_nombre;
    }

    /**
     * Set es_laptop
     *
     * @param boolean $esLaptop
     * @return ReporteServicio
     */
    public function setEsLaptop($esLaptop)
    {
        $this->es_laptop = $esLaptop;

        return $this;
    }

    /**
     * Get es_laptop
     *
     * @return boolean 
     */
    public function getEsLaptop()
    {
        return $this->es_laptop;
    }

    /**
     * Set autor_username
     *
     * @param string $autorUsername
     * @return ReporteServicio
     */
    public function setAutorUsername($autorUsername)
    {
        $this->autor_username = $autorUsername;

        return $this;
    }

    /**
     * Get autor_username
     *
     * @return string 
     */
    public function getAutorUsername()
    {
        return $this->autor_username;
    }

    /**
     * Set autor_area_nombre
     *
     * @param string $autorAreaNombre
     * @return ReporteServicio
     */
    public function setAutorAreaNombre($autorAreaNombre)
    {
        $this->autor_area_nombre = $autorAreaNombre;

        return $this;
    }

    /**
     * Get autor_area_nombre
     *
     * @return string 
     */
    public function getAutorAreaNombre()
    {
        return $this->autor_area_nombre;
    }

    /**
     * Set tecnico_username
     *
     * @param string $tecnicoUsername
     * @return ReporteServicio
     */
    public function setTecnicoUsername($tecnicoUsername)
    {
        $this->tecnico_username = $tecnicoUsername;

        return $this;
    }

    /**
     * Get tecnico_username
     *
     * @return string 
     */
    public function getTecnicoUsername()
    {
        return $this->tecnico_username;
    }

    /**
     * Set tecnico_area_nombre
     *
     * @param string $tecnicoAreaNombre
     * @return ReporteServicio
     */
    public function setTecnicoAreaNombre($tecnicoAreaNombre)
    {
        $this->tecnico_area_nombre = $tecnicoAreaNombre;

        return $this;
    }

    /**
     * Get tecnico_area_nombre
     *
     * @return string 
     */
    public function getTecnicoAreaNombre()
    {
        return $this->tecnico_area_nombre;
    }

    /**
     * Set usuario
     *
     * @param string $usuario
     * @return ReporteServicio
     */
    public function setUsuario($usuario)
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * Get usuario
     *
     * @return string 
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * Set problema
     *
     * @param string $problema
     * @return ReporteServicio
     */
    public function setProblema($problema)
    {
        $this->problema = $problema;

        return $this;
    }

    /**
     * Get problema
     *
     * @return string 
     */
    public function getProblema()
    {
        return $this->problema;
    }

    /**
     * Set labor_realizada
     *
     * @param string $laborRealizada
     * @return ReporteServicio
     */
    public function setLaborRealizada($laborRealizada)
    {
        $this->labor_realizada = $laborRealizada;

        return $this;
    }

    /**
     * Get labor_realizada
     *
     * @return string 
     */
    public function getLaborRealizada()
    {
        return $this->labor_realizada;
    }

    /**
     * Set piezas
     *
     * @param string $piezas
     * @return ReporteServicio
     */
    public function setPiezas($piezas)
    {
        $this->piezas = $piezas;

        return $this;
    }

    /**
     * Get piezas
     *
     * @return string 
     */
    public function getPiezas()
    {
        return $this->piezas;
    }

    /**
     * Set piezas_recuperadas
     *
     * @param boolean $piezasRecuperadas
     * @return ReporteServicio
     */
    public function setPiezasRecuperadas($piezasRecuperadas)
    {
        $this->piezas_recuperadas = $piezasRecuperadas;

        return $this;
    }

    /**
     * Get piezas_recuperadas
     *
     * @return boolean 
     */
    public function getPiezasRecuperadas()
    {
        return $this->piezas_recuperadas;
    }

    /**
     * Set fecha_reporte
     *
     * @param \DateTime $fechaReporte
     * @return ReporteServicio
     */
    public function setFechaReporte($fechaReporte)
    {
        $this->fecha_reporte = $fechaReporte;

        return $this;
    }

    /**
     * Get fecha_reporte
     *
     * @return \DateTime 
     */
    public function getFechaReporte()
    {
        return $this->fecha_reporte;
    }

    /**
     * Set fecha_solucion
     *
     * @param \DateTime $fechaSolucion
     * @return ReporteServicio
     */
    public function setFechaSolucion($fechaSolucion)
    {
        $this->fecha_solucion = $fechaSolucion;

        return $this;
    }

    /**
     * Get fecha_solucion
     *
     * @return \DateTime 
     */
    public function getFechaSolucion()
    {
        return $this->fecha_solucion;
    }

    /**
     * Set observacion
     *
     * @param string $observacion
     * @return ReporteServicio
     */
    public function setObservacion($observacion)
    {
        $this->observacion = $observacion;

        return $this;
    }

    /**
     * Get observacion
     *
     * @return string 
     */
    public function getObservacion()
    {
        return $this->observacion;
    }
}
