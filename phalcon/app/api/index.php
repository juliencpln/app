<?php 
use Phalcon\Mvc\Micro; 
use Phalcon\Mvc\Model\MetaData;
use Phalcon\Mvc\Model\Query;

// Récupération de la session pour les droits
$session = Phalcon\Di::getDefault()->get('session');
$user_session = $session->get('user_session');

$app = new Micro();

if($_SERVER['REQUEST_METHOD'] != 'GET' && $user_session->is_admin == 0 ){

    echo "Vous n'avez pas accès à cette ressource";
}
else
{ 
    $entitiesList = array("companies", "products", "clients", "employees", "providers", "transactions");
    
    // Gestion de la récupération des listes
    include("script/main/getList.php");

    // Récupération des listes   
    foreach ($entitiesList as $key => $value) {
        $app->get(
        '/api/'.$value,
            function () {
                $entityName = ucfirst(explode("/",$_SERVER['REQUEST_URI'])[2]);
                return getList($entityName);
            }
        );
    }

    // Gestion de la récupération d'un résultat
    include("script/main/getOnce.php");

    // Récupération d'un résultat
    foreach ($entitiesList as $key => $value) {
        $app->get(
        '/api/'.$value.'/{id:[0-9]+}',
            function ($id) {
                $entityName = ucfirst(explode("/",$_SERVER['REQUEST_URI'])[2]);
                return getOnce($entityName, $id);
            }
        );
    }

    // Gestion de l'ajout et de la modification
    include("script/main/postOrPut.php");

    // Ajout de données dans les entitées
    foreach ($entitiesList as $key => $value) {
        $app->post(
        '/api/'.$value,
            function () {
                $entityName = ucfirst(explode("/",$_SERVER['REQUEST_URI'])[2]);
                return postOrPut($entityName);
            }
        );
    }

    // Modifications de données dans les entitées
    foreach ($entitiesList as $key => $value) {
        $app->put(
        '/api/'.$value.'/{id:[0-9]+}',
            function ($id) {
                $entityName = ucfirst(explode("/",$_SERVER['REQUEST_URI'])[2]);
                return postOrPut($entityName, $id);
            }
        );
    };

    // Gestion de la suppression
    include("script/main/delete.php");

    // Suppression d'un résultat
    foreach ($entitiesList as $key => $value) {
        $app->delete(
        '/api/'.$value.'/{id:[0-9]+}',
            function ($id) {
                $entityName = ucfirst(explode("/",$_SERVER['REQUEST_URI'])[2]);
                return delete($entityName, $id);
            }
        );
    }

    include("script/employeesByCompanies.php");

    $app->handle($_SERVER["REQUEST_URI"]);
}

