<?php

namespace ScuolaGuida\Entity;

class Studente extends Utente
{
    public $email;
    public $nome;
    public $cognome;
    public $data_nascita;
    public $sesso;
    public $password;
    public $id_autoscuola;
    public $tipo_esame;
    public $note;
    public $data_esame_teoria;
    public $data_creazione;

    protected $permissions = Utente::STUDENTE_PERMISSIONS;

    const NAME_PATTERN = "/^[A-Za-z\x{00C0}-\x{00FF}][A-Za-z\x{00C0}-\x{00FF}\'\-]+([\ A-Za-z\x{00C0}-\x{00FF}][A-Za-z\x{00C0}-\x{00FF}\'\-]+)*/u";

    public static function genNewPassword()
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $count = mb_strlen($chars);

        for ($i = 0, $result = ''; $i < 8; $i++) {
            $index = rand(0, $count - 1);
            $result .= mb_substr($chars, $index, 1);
        }

        return $result;
    }
}
