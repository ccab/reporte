<?php

namespace Reporte\ReporteServicioBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Reporte\ReporteServicioBundle\Form\ReporteServicioType;
use Symfony\Component\HttpFoundation\Request;

class ReporteServicioController extends Controller {
    /*
      public function importAction()
      {
      $em = $this->getDoctrine()->getManager();
      $entities = $em->getRepository('ReporteReporteBundle:Reporte')->findAll();

      if (!$entities)
      {
      $this->get('session')->getFlashBag()->add('warning','No existen Reportes Solucionados');
      return $this->redirect($this->generateUrl('reporte_custom_default_index'));
      }

      foreach ($entities as $reporte)
      $this->createReporteServicio($em, $reporte);

      $em->flush();

      $this->get('session')->getFlashBag()->add('success',"Reportes Importados");
      return $this->redirect($this->generateUrl('reporte_custom_default_index'));

      }
     */

    /**
     * Lista todos los reportes ordenados por fecha
     * 
     * @Route("/mostrar/rs/{page}", defaults={"page" = 1})
     * @Template()
     */
    public function indexAction(Request $request, $page) {
        $em = $this->getDoctrine()->getManager();
        //paginator settings
        $paginator = $this->get('reporte_paginator');
        $paginator->setUrl('reporte_reporteservicio_reporteservicio_index');
        $limit = $this->container->getParameter('items_per_page');
        $offset = ($page - 1) * $this->container->getParameter('items_per_page');

        if (true === $this->get('security.context')->isGranted('ROLE_ADMIN')) {
            $paginator->paginate($page, $em->getRepository('ReporteReporteServicioBundle:ReporteServicio')->getCounter());
            $entities = $em->getRepository('ReporteReporteServicioBundle:ReporteServicio')->findBy($criteria = array(), $orderBy = array('fecha_reporte' => 'DESC'), $limit, $offset);
        } else {
            $paginator->paginate($page, $em->getRepository('ReporteReporteServicioBundle:ReporteServicio')->getCounterPorArea(array('area' => $this->getUser()->getArea())));
            $entities = $em->getRepository('ReporteReporteServicioBundle:ReporteServicio')->findBy($criteria = array('autor_area_nombre' => $this->getUser()->getArea()->getNombre()), $orderBy = array('fecha_reporte' => 'DESC'), $limit, $offset);
        }

        $form = $this->createSearchForm();
        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();

            if (true === $this->get('security.context')->isGranted('ROLE_ADMIN') || true === $this->get('security.context')->isGranted('ROLE_TEC')) {
                $entities = $em->getRepository('ReporteReporteServicioBundle:ReporteServicio')->findLike($data);
            } else {
                $entities = $em->getRepository('ReporteReporteServicioBundle:ReporteServicio')->findLike(array('area' => $this->getUser()->getArea(), 'usuario' => $data['usuario']));
            }


            if (!$entities) {
                $this->get('session')->getFlashBag()->add('warning', 'No existen Reportes que coincidan con su busqueda');
                return $this->redirect($this->generateUrl('reporte_reporteservicio_reporteservicio_index'));
            }

            $this->get('session')->getFlashBag()->add('info', 'Resultados de la busqueda');
            return array('entities' => $entities, 'form' => $form->createView());
        } 


        if (!$entities) {
            $this->get('session')->getFlashBag()->add('warning', 'No existen Reportes Solucionados');
            return array();
        }

        return array('entities' => $entities, 'form' => $form->createView(), 'paginator' => $paginator);
    }

    /**
     * Helper function to create the Bundle Index search form
     */
    private function createSearchForm() {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createFormBuilder(array(), array('attr' => array('class' => 'form-inline')))
                ->add('usuario', 'search', array('required' => false, 'attr' => array('placeholder' => 'Usuario', 'role' => 'form')))
                ->getForm();

        //the upper roles may filter by Area
        if (true === $this->get('security.context')->isGranted('ROLE_ADMIN') || true === $this->get('security.context')->isGranted('ROLE_TEC')) {
            $areas = $em->getRepository('ReporteAreaBundle:Area')->findAll();
            $form->add('area', 'entity', array('class' => 'ReporteAreaBundle:Area', 'choices' => $areas,
                'property' => 'nombre', 'required' => false, 'empty_value' => 'Seleccione el area'));
        }

        $form->add('Buscar', 'submit', array('attr' => array('class' => 'btn btn-primary')));

        return $form;
    }

    /**
     * Busca y muestra un Reporte dado su id
     * 
     * @Route("/admin/mostrar/rs/{id}", defaults={"id" = 0})
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ReporteReporteServicioBundle:ReporteServicio')->find($id);

        if (!$entity) {
            $this->get('session')->getFlashBag()->add('error', 'No existe un Reporte con ese id');
            return $this->redirect($this->generateUrl('reporte_reporteservicio_reporteservicio_index'));
        }

        return array('entity' => $entity);
    }

    /*
     * Funcion auxiliar que devuelve el dia inicial y final de un mes
     * Si el mes es 0(default) se devuelve el rango en base al mes actual
     */

    private function dateRange($month, $year) {
        $date = array();
        $date_month = ($month == 0) ? date('F') : date('F', mktime(0, 0, 0, $month));
        $date_year = ($year == 0) ? date('Y') : date('Y', mktime(0, 0, 0, 0, 0, ++$year));

        $date['start'] = new \DateTime("first day of " . $date_month . " " . $date_year);
        $date['end'] = new \DateTime("last day of " . $date_month . " " . $date_year);

        return $date;
    }

    /**
     * Helper function to get all months in Spanish
     * 
     * @return array key = month number / value = month name
     */
    private function getMonths() {
        $months = array();
        $fmt = new \IntlDateFormatter("es_ES", NULL, NULL, NULL, NULL, "LLLL");

        for ($i = 1; $i <= 12; $i++) {
            $name = $fmt->format(mktime(0, 0, 0, $i));
            $months[] = array("number" => $i, "name" => $name);
        }

        return $months;
    }

    /**
     * Helper function to get an array with a list of years beginning with 
     * (current_year - range) to (current_year + range)
     * 
     * @param int $range 
     * @return array The years range
     */
    private function getYears($range = 1) {
        $year_fmt = new \IntlDateFormatter("es_ES", NULL, NULL, NULL, NULL, "y");

        //php.net: IntlDateFormatter::format (PHP Version 5.3.4) Support for providing DateTime objects to the value parameter was added.
        //return range($fmt->format(new \DateTime("-$range years")), $fmt->format(new \DateTime("+$range years")));

        $start = new \DateTime("-$range years");
        $end = new \DateTime("+$range years");
        return range($year_fmt->format($start->getTimestamp()), $year_fmt->format($end->getTimestamp()));
    }

    /**
     * Muestra los reportes correspondientes a un mes, en un formato de impresion
     * 
     * @Route("/mostrar/reporte/mensual")
     * @Template()
     */
    public function mensualAction(Request $request) {
        if (false === $this->container->get('security.context')->isGranted('ROLE_TEC')) {
            throw new AccessDeniedException();
        } else {
            $date_range = $this->dateRange($request->request->get('month', 0), $request->request->get('year', 0));
            $em = $this->getDoctrine()->getManager();
            $entities = $em->getRepository('ReporteReporteServicioBundle:ReporteServicio')->findRSDateRange($date_range);

            return array('entities' => $entities, 'date' => $date_range['start'], 'months' => $this->getMonths(), 'years' => $this->getYears());
        }
    }

    /**
     * @Route("/admin/rs/modificar/{id}", defaults={"id" = 0})
     * @Template()
     */
    public function updateAction($id, Request $request) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ReporteReporteServicioBundle:ReporteServicio')->find($id);

        if (!$entity) {
            $this->get('session')->getFlashBag()->add('error', 'No existe un Reporte con ese id');
            return $this->redirect($this->generateUrl('reporte_reporteservicio_reporteservicio_index'));
        }

        $form = $this->createForm(new ReporteServicioType(), $entity, array('attr' => array('class' => 'form-horizontal')));
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', "Reporte modificado");
            return $this->redirect($this->generateUrl('reporte_reporteservicio_reporteservicio_index'));
        }

        return array('form' => $form->createView());
    }

}
