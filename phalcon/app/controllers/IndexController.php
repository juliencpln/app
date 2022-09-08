<?php
declare(strict_types=1);

class IndexController extends ControllerBase
{
    public function indexAction()
    {
         if(isset(($this->session->user_session)))
        {
            $this->response->redirect('/companies');
        }
    }
}

