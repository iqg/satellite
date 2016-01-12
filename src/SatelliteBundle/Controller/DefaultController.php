<?php

namespace SatelliteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('SatelliteBundle:Default:index.html.twig');
    }
}
