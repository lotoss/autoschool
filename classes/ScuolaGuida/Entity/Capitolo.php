<?php

namespace ScuolaGuida\Entity;

use Ninja\DatabaseTable;

class Capitolo
{
    public $id;
    public $id_patente;
    public $descrizione;

    public $gruppi;

    private $gruppiTable;

    public function __construct(DatabaseTable $gruppiTable)
    {
        $this->gruppiTable = $gruppiTable;
    }

    public function getGruppi($orderBy = null)
    {
        if (empty($this->gruppi)) {
            $this->gruppi = $this->gruppiTable->find(array('id_capitolo', 'id_patente'), array($this->id, $this->id_patente), $orderBy);
        }

        return $this->gruppi;
    }
}
