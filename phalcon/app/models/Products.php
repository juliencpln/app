<?php

use Phalcon\Mvc\Model;

class Products extends Model
{
    public $id;
    public $name;
    public $price;
    public $tax;
    public $stock;

    public static function getStock($id, $stock = NULL)
    {

        // Dans le cas où le stock n'est pas connu, on le récupère
        if(!$stock){
            $product = Products::findFirst(['conditions' => 'id = '.$id]);
            $stock = $product->stock;
        }

        // Parcourir toutes les transactions pour ce produit
        $transactions = Transactions::find(['conditions' => 'id_product = '.$id] );

        foreach ($transactions as $value) {

            // Dans le cas d'un client c'est une vente donc on soustrait le stock de produits
            if($value->id_client){
                $stock -= $value->quantity_product;
            }

            // Dans le cas d'un fournisseur c'est un achat donc on additionne le stock de produits
            if($value->id_provider){
                $stock += $value->quantity_product;
            }
        }
        
        return $stock;
    }
}
