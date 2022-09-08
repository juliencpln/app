<?php
function getOnce($entityName, $id){
    $result = $entityName::findFirst('id ='.$id);
    if($entityName == 'Companies'){ 
        $result->balance = Companies::getBalance($result->id, $result->balance);
    }
    elseif($entityName == 'Products'){
        $result->stock  = Products::getStock($result->id, $result->stock);
    }

    return json_encode($result);
}
