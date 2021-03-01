<?php

namespace ScuolaGuida\Controllers;

use Ninja\DatabaseTable;
use Ninja\Authentication;

class RicezioneRisposte
{
    private $studentiTable;
    private $authentication;
    private $domandeTable;

    public function __construct(Authentication $authentication, DatabaseTable $studentiTable, DatabaseTable $domandeTable)
    {
        $this->studentiTable = $studentiTable;
        $this->authentication = $authentication;
        $this->domandeTable = $domandeTable;
    }

    public function showRicezione()
    {
        $autoscuola = $this->authentication->getUser();
        if ($_SESSION['role'] == 'autoscuola') {
            return [
                'template' => 'admin/ricezione-risposte.html.php',
                'title' => 'Ricezione risposte',
                'layoutVariables' => [
                    'breadcrumbs' => ['Lezione', 'Ricezione risposte']
                ],
                'variables' => [
                    'autoscuola' => $autoscuola
                ]
            ];
        } else {
            header('location: /login');
        }
    }

    public function getStudente()
    {
        $studente = $this->studentiTable->findById($_GET['email']);
        if (!empty($studente)) {
            echo json_encode(array('studente' => $studente));
        } else {
            http_response_code(404);
        }
    }

    public function getDomanda()
    {
        $domanda = $this->domandeTable->find(array('id', 'id_gruppo'), array($_GET['id_domanda'], $_GET['id_gruppo']));
        if (!empty($domanda)) {
            echo json_encode(array('domanda' => $domanda[0]));
        } else {
            http_response_code(404);
        }
    }
}
