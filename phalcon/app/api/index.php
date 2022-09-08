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

    // Ajout de donnée dans les entitées
    foreach ($entities as $key => $value) {

        $app->post(
        '/api/'.$value,
            function () {
                $isSolvent = true;
                $isStockSuffisant = true;

                $entityBody = file_get_contents('php://input');
                $result = json_decode($entityBody);

                $entityName = ucfirst(explode("/",$_SERVER['REQUEST_URI'])[2]);
                $entity = new $entityName();

                // Récupération de toutes les données
                foreach ($result as $key => $value) {
                   
                    if($key == 'balance' OR $key == 'price' OR $key == 'tax'){
                        $value = str_replace(',','.',$value);
                        if(is_numeric($value)){
                            $entity->$key = $value;
                        }
                    }
                    elseif(($key == 'birthday' OR $key == 'first_day') && $value != ""){

                        if (DateTime::createFromFormat('d/m/Y', $value) !== FALSE) {
                            $value = DateTime::createFromFormat('d/m/Y',$value)->format('Y-m-d');
                        }

                        if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$value)){
                            $entity->$key = $value;  
                        }
                    }
                    else
                    {
                        $entity->$key = $value;
                    }
                }   

                if($entityName == 'Transactions'){ 
                    // Dans le cas d'un achat on vérifie si l'entreprise est solvable
                    if($result->id_provider){ 
                        $isSolvent = false;

                        // Récupération du solde de l'entreprise
                        $companyBalance = Companies::getBalance($result->id_company);
                        // Cout du produit
                        $product = Products::findFirst([ 'conditions' => 'id = '.$entity->id_product]);
                        // Cout total de la transaction
                        $total_price = ($product->price+($product->price*$product->tax/100))*$entity->quantity_product;

                        if($companyBalance >= $total_price){
                            $isSolvent = true; 
                        }
                    }
                    elseif($result->id_client){
                        $isStockSuffisant = false;

                        // Récupération du stock de produit 
                        $productStock = Products::getStock($result->id_product);
                        
                        if($productStock >= $result->quantity_product){
                            $isStockSuffisant = true; 
                        }
                    }
                }

                if($isSolvent){ 
                    if($isStockSuffisant){ 
                        if ($entity->save() === false) {
                            $data['message'] = "Il y'a eu une erreur, vérifiez vos données";

                        } else {
                            $data['last_id'] = $entity->id;
                            $data['message'] = 'Votre nouvelle entrée à été ajoutée avec succès !';
                        }
                    }
                    else
                    {
                        $data['message'] = "Le stock de produit est insufissant pour cette transaction.";
                    }
                }
                else
                {
                    $data['message'] = "Cette entreprise n'a pas assez de ressources financières pour cette transaction.";
                }

                return json_encode($data);

            }
        );
    }

    // Modification des données
    // Reste à faire

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

    $app->handle(
        $_SERVER["REQUEST_URI"]
    );
}

