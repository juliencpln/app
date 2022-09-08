<?php
function postOrPut($entityName, $id = null){

    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        // Création
        $entity = new $entityName();
    }
    else
    {
        // Modification
        $entity = $entityName::findFirst('id ='.$id);
    }

    // La companie est solvable
    $isSolvent = true;
    // Le stock de produit est suffisant
    $isStockSuffisant = true;

    $entityBody = file_get_contents('php://input');
    $result = json_decode($entityBody);

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
            }
            else 
            {
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
?>
