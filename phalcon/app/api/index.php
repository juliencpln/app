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
    $entities = array("companies", "products", "clients", "employees", "providers", "transactions");
    
    // Récupération des listes   
    foreach ($entities as $key => $value) {
        $app->get(
        '/api/'.$value,
            function () {

                $entityName = ucfirst(explode("/",$_SERVER['REQUEST_URI'])[2]);
                $entity = new $entityName();

                $metadata = $entity->getModelsMetaData();
                $result['columns'] = $metadata->getAttributes($entity);

                $result['data'] = $entityName::find([
                    'order' => 'id desc'
                 ])->toArray();

                if($entityName == 'Companies'){
                    foreach ($result['data'] as $key => $value) {
                        $result['data'][$key]['balance'] = Companies::getBalance($value['id'], $value['balance']);
                    }
                }
                elseif($entityName == 'Products'){
                    foreach ($result['data'] as $key => $value) {
                        $result['data'][$key]['stock'] = Products::getStock($value['id'], $value['stock']);
                    }
                }

                return json_encode($result);
            }
        );
    }

    // Récupération d'un résultat
    foreach ($entities as $key => $value) {
        $app->get(
        '/api/'.$value.'/{id:[0-9]+}',
            function ($id) {

                $entityName = ucfirst(explode("/",$_SERVER['REQUEST_URI'])[2]);
                $result = $entityName::findFirst('id ='.$id);

                if($entityName == 'Companies'){ 
                    $result->balance = Companies::getBalance($result->id, $result->balance);
                }
                elseif($entityName == 'Products'){
                    $result->stock  = Products::getStock($result->id, $result->stock);
                }

                return json_encode($result);
            }
        );
    }

     $app->get(
    '/api/employees/id_company={id:[0-9]+}',
        function ($id) {

            $employees = new Employees();

            $result['data'] = $employees::find([
                'id_company ='.$id,
                'order' => 'id desc'
             ]);

            return json_encode($result);
        }
    );

    // Gestion de l'ajout et de la modification
    include("script/createOrUpdate.php");

    // Ajout de données dans les entitées
    foreach ($entities as $key => $value) {
        $app->post(
        '/api/'.$value,
            function () {
                $entityName = ucfirst(explode("/",$_SERVER['REQUEST_URI'])[2]);
                $entity = new $entityName();
                return createOrUpdate($entity, $entityName);
            }
        );
    }

    // Modifications de données dans les entitées
    foreach ($entities as $key => $value) {
        $app->put(
        '/api/'.$value.'/{id:[0-9]+}',
            function ($id) {
                $entityName = ucfirst(explode("/",$_SERVER['REQUEST_URI'])[2]);
                $entity = $entityName::findFirst('id ='.$id);
                return createOrUpdate($entity, $entityName);
            }
        );
    };

    // Suppression d'un résultat
    foreach ($entities as $key => $value) {
        $app->delete(
        '/api/'.$value.'/{id:[0-9]+}',
            function ($id) {
                $entity = ucfirst(explode("/",$_SERVER['REQUEST_URI'])[2]);
                $request = $entity::findFirst('id ='.$id);
                $result  = $request->delete();
                return $result;
            }
        );
    }

    $app->handle($_SERVER["REQUEST_URI"]);
}

