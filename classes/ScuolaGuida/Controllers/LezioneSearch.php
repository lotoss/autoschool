<?php

namespace ScuolaGuida\Controllers;

use Ninja\DatabaseTable;

class LezioneSearch
{
    private $capitoliTable;
    private $gruppiTable;
    private $domandeTable;

    public function __construct(DatabaseTable $capitoliTable, DatabaseTable $gruppiTable, DatabaseTable $domandeTable)
    {
        $this->capitoliTable = $capitoliTable;
        $this->gruppiTable = $gruppiTable;
        $this->domandeTable = $domandeTable;
        if (!isset($_SESSION['patente'])) {
            $_SESSION['patente'] = 'B';
        }
    }

    public function search()
    {
        if (!isset($_GET['controller']) || ($_GET['controller'] != 'argomentiController')) {
            http_response_code(404);
            exit();
        }

        $capitoli = $this->capitoliTable->find(array('descrizione', 'id_patente'), array('%' . trim($_GET['q']) . '%', $_SESSION['patente']), null, null, null, 'LIKE') ?? [];
        $gruppi = $this->gruppiTable->find(array('descrizione', 'id_patente'),  array('%' . trim($_GET['q']) . '%', $_SESSION['patente']), null, null, null, 'LIKE') ?? [];
        $domande = $this->domandeTable->find(array('domanda', 'id_patente'), array('%' . trim($_GET['q']) . '%', $_SESSION['patente']), null, null, null, 'LIKE') ?? [];

        $numItems = count($capitoli) + count($gruppi) + count($domande);

        return [
            'template' => 'admin/lezione-search.html.php',
            'title' => 'Ricerca',
            'layoutVariables' => [
                'breadcrumbs' => ['Lezione'],
                'selectPatente' => true,
                'search' => true
            ],
            'variables' => [
                'capitoli' => $capitoli,
                'gruppi' => $gruppi,
                'domande' => $domande,
                'numItems' => $numItems
            ]
        ];
    }
}
