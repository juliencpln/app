<?php

declare(strict_types=1);

namespace Tests\Unit;

require './vendor/autoload.php';

class APIUnitTest extends AbstractUnitTest
{
    public function testAPIGetList(): void
    {
    	// Gestion de la récupération des listes
    	include("./app/api/script/main/getList.php");
   		$response = getList('Employees');
	    $response = json_decode($response, true);

	    $this->assertIsArray($response);

	    foreach($response['data'] as $key=>$value){
	    	$this->assertIsString($response['data'][$key]['name']);
	    	$this->assertIsNumeric($response['data'][$key]['id_company']);
	    	$this->assertIsString($response['data'][$key]['birthday']);
	    	$this->assertIsString($response['data'][$key]['country']);
	    	$this->assertIsString($response['data'][$key]['first_day']);
		}
    }
}




 