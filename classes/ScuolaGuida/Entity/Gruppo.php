<?php

namespace ScuolaGuida\Entity;

use Ninja\DatabaseTable;
use Ninja\Authentication;

class Gruppo
{
    public $id;
    public $id_patente;
    public $id_capitolo;
    public $descrizione;
    public $spiegazione;
    public $id_immagine;

    public $domande = [];
    public $capitolo;
    public $immagini = [];

    private $domandeTable;
    private $capitoliTable;
    private $immaginiGruppi;
    private $authentication;

    public function __construct(DatabaseTable $domandeTable, DatabaseTable $capitoliTable, DatabaseTable $immaginiGruppi, Authentication $authentication)
    {
        $this->domandeTable = $domandeTable;
        $this->capitoliTable = $capitoliTable;
        $this->immaginiGruppi = $immaginiGruppi;
        $this->authentication = $authentication;
    }

    public function getDomande()
    {
        if (empty($this->domande)) {
            $domande = $this->domandeTable->find('id_gruppo', $this->id);
            if (!empty($domande)) {
                $this->domande = $domande;
            }
        }

        return $this->domande;
    }

    public function getCapitolo()
    {
        if (empty($this->capitolo)) {
            $this->capitolo = $this->capitoliTable->find(array('id', 'id_patente'), array($this->id_capitolo, $this->id_patente));
        }

        return $this->capitolo;
    }

    public function getRelatedFiles()
    {
        if (empty($this->immagini)) {
            $this->immagini = $this->immaginiGruppi->find(array('id_gruppo', 'id_autoscuola'), array($this->id, $this->authentication->getUser()->id));
        }

        return $this->immagini;
    }

    public function deleteImmagine($id_immagine)
    {
        $img = $this->immaginiGruppi->findById($id_immagine);
        if (!empty($img)) {
            $file = new \Ninja\File(__DIR__ . '/../../../public/img/gruppi/correlate/' . $img->file_immagine);
            $file->delete();
            $this->immaginiGruppi->delete($id_immagine);
            return true;
        }

        return false;
    }
}
