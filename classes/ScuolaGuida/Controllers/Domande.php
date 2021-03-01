<?php

namespace ScuolaGuida\Controllers;

use Ninja\Authentication;
use Ninja\DatabaseTable;

class Domande
{
    private $domandeTable;
    private $authentication;

    public function __construct(Authentication $authentication, DatabaseTable $domandeTable)
    {
        $this->authentication = $authentication;
        $this->domandeTable = $domandeTable;
    }

    public function showDomande()
    {
        $studente = $this->authentication->getUser();
        if ($studente instanceof \ScuolaGuida\Entity\Studente) {
            return [
                'template' => 'public/domande.html.php',
                'title' => 'Domande',
                'variables' => [
                    'studente' => $studente
                ]
            ];
        } else {
            header('location: /login');
        }
    }

    public function getDomanda()
    {
        $domanda = $this->domandeTable->find(array('id', 'id_gruppo'), array($_GET['id_domanda'], $_GET['id_gruppo']));
        if (!empty($domanda)) {
            $domanda = $domanda[0];
            unset($domanda->risposta);
            unset($domanda->id_patente);
            echo json_encode(array('domanda' => $domanda));
        } else {
            http_response_code(404);
        }
    }
}
