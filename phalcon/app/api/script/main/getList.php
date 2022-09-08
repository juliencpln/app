<?php

function getList($entityName){

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
