<?php
declare(strict_types=1);

use Phalcon\Mvc\Controller;
use Phalcon\Session\Manager;

class ControllerBase extends Controller
{

	function initialize() {
		
		$session = new Manager();

        if(!isset(($this->session->user_session)) &&  $_SERVER['REQUEST_URI'] != '/login')
        {
            $this->response->redirect('/login');
        }
        
        $this->view->setLayout("index");

		function dateToFrench($date, $format) 
      	{
          $english_days = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
          $french_days = array('lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche');
          $english_months = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
          $french_months = array('janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre');
          return str_replace($english_months, $french_months, str_replace($english_days, $french_days, date($format, strtotime($date) ) ) );
      	}
	}

	public function disconnectAction()
    {
        $this->session->destroy();
        $this->response->redirect('/login');
    }



}
