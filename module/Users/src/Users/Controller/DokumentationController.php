<?php
namespace Users\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class DokumentationController extends AbstractActionController
{
    public function indexAction()
    {
    	$this->layout('layout/default-layout');
		$this->layout()->setVariable('x_active', 'active');
        $view = new ViewModel();
        return $view;
    }

}