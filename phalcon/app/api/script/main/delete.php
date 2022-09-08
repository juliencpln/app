<?php
function delete($entityName, $id){
	$request = $entityName::findFirst('id ='.$id);
	return $request->delete();
}