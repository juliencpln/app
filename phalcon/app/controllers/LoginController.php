<?php
declare(strict_types=1);


use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Select;
class LoginController extends ControllerBase
{

    public function indexAction()
    {

    }

    public function loginAction()
    {

        $username = $this->request->getPost("username");
        $password = $this->request->getPost("password");
        $password = sha1(md5($password));
	    $user = Users::query()
	    ->where('username = "'.$username.'"')
	    ->where('password = "'.$password.'"')
	    ->execute();

    	if(count($user) != 0 ){
    		echo "il existe";
    		// On crée la session
    	}
    	else
    	{
    		echo "il n'existe pas";
    		// On redirige vers /login avec un message d'erreur 
    	}

    }


}

