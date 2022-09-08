<?php
// Liste des employés par entreprises
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