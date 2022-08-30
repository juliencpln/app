<?php 

use Phalcon\Mvc\Micro;

$app = new Micro();

$isAdmin = 1;

if($_SERVER['REQUEST_METHOD'] != 'GET' && isAdmin == 0 ){

    echo "non pas le droit";
}
else
{ 
    $app->get(
        '/api/companies',
        function () {
             return json_encode(Companies::find());
        }
    );

    $app->get(
        '/api/companies/search/{name}',
        function ($name) {
        }
    );

    $app->get(
        '/api/companies/{id:[0-9]+}
    ',
        function ($id) {
        }
    );

    $app->post(
        '/api/companies',
        function () {

            $company = new Companies();

            $company->type = 'mechanical';
            $company->name = 'Astro TEST';
            $company->year = 1952;

            if ($company->save() === false) {
                echo "Umh, We can't store company right now: \n";

                $messages = $company->getMessages();

                foreach ($messages as $message) {
                    echo $message, "\n";
                }

            } else {
                echo 'Great, a new robot was saved successfully!';
            }

        }
    );

    $app->put(
        '/api/companies/{id:[0-9]+}',
        function ($id) {
        }
    );

    $app->delete(
        '/api/companies/{id:[0-9]+}',
        function ($id) {
        }
    );

    $app->handle(
        $_SERVER["REQUEST_URI"]
    );

}

