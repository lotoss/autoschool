<?php

namespace ScuolaGuida\Controllers;

use Ninja\DatabaseTable;

class Cartellone
{
    private $capitoliTable;
    private $gruppiTable;
    private $domandeTable;
    private $immaginiCartelloneTable;

    public function __construct(DatabaseTable $capitoliTable, DatabaseTable $gruppiTable, DatabaseTable $domandeTable, DatabaseTable $immaginiCartelloneTable)
    {
        $this->capitoliTable = $capitoliTable;
        $this->gruppiTable = $gruppiTable;
        $this->domandeTable = $domandeTable;
        $this->immaginiCartelloneTable = $immaginiCartelloneTable;
        if (!isset($_SESSION['patente'])) {
            $_SESSION['patente'] = 'B';
        }
    }

    public function list()
    {
        $imgs = [];
        $capitoli = $this->capitoliTable->find('id_patente', $_SESSION['patente']);
        $mems = [];
        foreach ($capitoli as $key => $capitolo) {
            $mems[$key] = [];
            foreach ($capitolo->getGruppi('id_immagine ASC') as $gruppo) {
                if (!empty($gruppo->id_immagine) && !in_array($gruppo->id_immagine, $imgs)) {
                    $imgs[] = $gruppo->id_immagine;
                    $mems[$key][] = $gruppo->id_immagine;
                }

                foreach ($gruppo->getDomande() as $domanda) {
                    if (!empty($domanda->id_immagine) && !in_array($domanda->id_immagine, $imgs)) {
                        $imgs[] = $domanda->id_immagine;
                        $mems[$key][] = $domanda->id_immagine;
                    }
                }
            }
        }


        foreach ($mems as $key => $value) {
            if (empty($mems[$key])) {
                unset($mems[$key]);
            } else {
                sort($mems[$key], SORT_NUMERIC);
            }
        }

        return [
            'template' => 'admin/cartellone.html.php',
            'title' => 'Cartellone',
            'layoutVariables' => [
                'breadcrumbs' => ['Lezione'],
                'selectPatente' => true,
                'search' => true
            ],
            'variables' => [
                'mems' => $mems
            ]
        ];
    }

    public function viewImg()
    {
    }
}
