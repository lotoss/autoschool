<?php

namespace ScuolaGuida\Entity;

use Ninja\Authentication;
use Ninja\DatabaseTable;

class Domanda
{
    public $id;
    public $id_gruppo;
    public $id_patente;
    public $id_domanda;
    public $risposta;
    public $id_immagine;

    private $gradoDomandeTable;
    private $domandeContrapposteTable;
    private $commentiTable;
    private $gruppiTable;
    private $authentication;

    public $grado;
    public $gruppo;
    public $is_contrapposta;

    public function __construct(DatabaseTable $gradoDomandeTable, DatabaseTable $domandeContrapposteTable, DatabaseTable $commentiTable, DatabaseTable $gruppiTable, Authentication $authentication)
    {
        $this->gradoDomandeTable = $gradoDomandeTable;
        $this->domandeContrapposteTable = $domandeContrapposteTable;
        $this->commentiTable = $commentiTable;
        $this->gruppiTable = $gruppiTable;
        $this->authentication = $authentication;
    }

    public function getGrado()
    {
        if (empty($this->grado)) {
            $grado = $this->gradoDomandeTable->find(array('id_domanda', 'id_gruppo', 'id_autoscuola'), array($this->id, $this->id_gruppo, $this->authentication->getUser()->id));
            if (!empty($grado)) {
                $this->grado = $grado[0];
            }
        }

        return $this->grado;
    }

    public function getGruppo()
    {
        if (empty($this->gruppo)) {
            $gruppo = $this->gruppiTable->findById($this->id_gruppo);
            if (!empty($gruppo)) {
                $this->gruppo = $gruppo;
            }
        }

        return $this->gruppo;
    }

    public function isContrapposta()
    {
        if (empty($this->is_contrapposta)) {
            $this->is_contrapposta = !empty($this->domandeContrapposteTable->find(array('id_domanda', 'id_gruppo', 'id_autoscuola'), array($this->id, $this->id_gruppo, $this->authentication->getUser()->id)));
        }

        return $this->is_contrapposta;
    }

    public function getCommento()
    {
        if (empty($this->commento)) {
            $commento = $this->commentiTable->find(array('id_domanda', 'id_gruppo', 'id_autoscuola'), array($this->id, $this->id_gruppo, $this->authentication->getUser()->id));
            if (!empty($commento)) {
                $this->commento = $commento[0];
            }
        }

        return $this->commento;
    }
}
