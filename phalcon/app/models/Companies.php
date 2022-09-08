<?php

use Phalcon\Mvc\Model;

class Companies extends Model
{
    public $id;
    public $name;
    public $balance;
    public $country;

    public static function getBalance($id, $balance = NULL)
    {

        if(!$balance){
            $company = Companies::findFirst(['conditions' => 'id = '.$id]);
            $balance = $company->balance;
        }

        $transactions = Transactions::find(['conditions' => 'id_company = '.$id] );

        foreach ($transactions as $value) {

            $product = Products::findFirst([ 'conditions' => 'id = '.$value->id_product]);

            // Récupération du prix total TTC en fonction de la quantité de produit
            $total_price = ($product->price+($product->price*$product->tax/100))*$value->quantity_product;

            // Dans le cas d'un client c'est une vente donc on additionne au solde
            if($value->id_client){
                $balance += $total_price;
            }

            // Dans le cas d'un fournisseur c'est un achat donc on soustrait au solde
            if($value->id_provider){
                $balance -= $total_price;
            }
        }

        return $balance;
    }

}
