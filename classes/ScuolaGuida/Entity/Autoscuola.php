<?php

namespace ScuolaGuida\Entity;

use Ninja\DatabaseTable;

class Autoscuola extends Utente
{
    public $id;
    public $nome;
    public $email;
    public $password;
    public $citta;
    public $provincia;
    public $data_creazione;

    protected $permissions = Utente::ADMIN_PERMISSIONS;

    private $studentiTable;

    public $studenti = [];

    public function __construct(DatabaseTable $studentiTable)
    {
        $this->studentiTable = $studentiTable;
    }

    public function getStudenti($orderBy = null, $limit = null, $offset = null)
    {
        if (empty($this->studenti)) {
            $studenti = $this->studentiTable->find('id_autoscuola', $this->id, $orderBy, $limit, $offset);
            if (!empty($studenti)) {
                $this->studenti = $studenti;
            }
        }

        return $this->studenti;
    }

    public function searchStudenti($q, $orderBy = null, $limit = null, $offset = null)
    {
        if (empty($this->studenti)) {
            $studenti = $this->studentiTable->find('id_autoscuola', $this->id, $orderBy);
            if (!empty($studenti)) {
                $this->studenti = $studenti;
            }
        }

        $result = [];

        $studenti = array_slice($this->studenti, $offset);

        foreach ($studenti as $studente) {
            if (stripos($studente->nome, $q) !== false || stripos($studente->cognome, $q) !== false || stripos($studente->email, $q)) {
                $result[] = $studente;
                if (count($result) == $limit) {
                    break;
                }
            }
        }

        return $result;
    }
}
