<?php
declare(strict_types=1);

use Phalcon\Forms\Form;
use Phalcon\Session\Manager;
use Phalcon\Forms\Element\Text;

class LoginController extends ControllerBase
{

    public function indexAction(){
    	
    	$session = new Manager();
    	$this->view->setLayout("index");

        if(isset(($this->session->user_session))){
            $this->response->redirect('/');
        }
    	// Affichage flash du message d'erreur
    	if(isset(($this->session->error_login))){
    		$this->view->error_login = $this->session->error_login;
    		$this->session->destroy();
    	}
    }

    public function connectAction()
    {
    
    	// Récupération des champs du formulaire de login
        $username = $this->request->getPost("username");
        $password = $this->request->getPost("password");
        $password = sha1(md5($password));

        // Vérifie si l'utilisateur existe en base
 		$user = Users::findFirst('username = "'.$username.'" AND password = "'.$password.'"');

    	if(isset($user)){

    		// L'utilisateur existe on crée la session
    		$this->session->set('user_session', $user);

    		// En fonction de ses droits on lui attribue des permissions

    		// On le redirige vers l'application 
           	$this->response->redirect('/');

    	}
    	else
    	{
    		// On redirige vers le formulaire avec un message d'erreur 
    		$this->session->set('error_login', "Vos identifiants sont incorrects");
           	$this->response->redirect('/login');


    	}
    }
}

