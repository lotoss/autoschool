<?php

namespace ScuolaGuida\Controllers;

use \SoUuid\SoUuid;
use Ninja\DatabaseTable;
use Ninja\Authentication;

class Studenti
{
    private $authentication;
    private $studentiTable;

    public function __construct(Authentication $authentication, DatabaseTable $studentiTable)
    {
        $this->authentication = $authentication;
        $this->studentiTable = $studentiTable;
        if (!isset($_SESSION['patente'])) {
            $_SESSION['patente'] = 'B';
        }
    }

    public function list()
    {
        $page = $_GET['page'] < 1 || empty($_GET['page']) ? 1 : $_GET['page'];
        $orderBy = $_GET['orderBy'] ?? 'data_creazione';
        $sortOrder = $_GET['sortOrder'] ?? 'DESC';
        $orderBy .= ' ' . $sortOrder;

        $offset = ($page - 1) * 20;

        $autoscuola = $this->authentication->getUser();
        $q = trim($_GET['q']);

        if (empty($q)) {
            $studenti = $autoscuola->getStudenti($orderBy, 20, $offset);
            $totaleStudenti = $this->studentiTable->total();
        } else {
            $studenti = $autoscuola->searchStudenti($q, $orderBy, 20, $offset);
            $totaleStudenti = $this->studentiTable->total(array('nome', 'cognome', 'email'), '%' . $q . '%', 'LIKE', 'OR');
        }


        return [
            'template' => 'admin/studenti.html.php',
            'title' => 'Studenti',
            'layoutVariables' => [
                'breadcrumbs' => ['Studenti']
            ],
            'variables' => [
                'studenti' => $studenti,
                'currentPage' => $page,
                'totaleStudenti' => $totaleStudenti
            ]
        ];
    }

    public function add()
    {
        $autoscuola = $this->authentication->getUser();
        return [
            'template' => 'admin/registra-studente.html.php',
            'title' => 'Nuovo studente',
            'layoutVariables' => [
                'breadcrumbs' => ['Studenti', 'Nuovo studente']
            ],
            'variables' => [
                'autoscuola' => $autoscuola
            ]
        ];
    }

    public function saveEdit()
    {
        $studente = &$_POST['studente'];

        $studente['nome'] = ucwords(preg_replace('!\s+!', ' ', trim($studente['nome'])));
        $studente['cognome'] = ucwords(preg_replace('!\s+!', ' ', trim($studente['cognome'])));
        $studente['email'] = strtolower(trim($studente['email']));

        if (empty(trim($studente['note']))) {
            unset($studente['note']);
        } else {
            $studente['note'] = ucfirst(trim($studente['note']));
        }

        if (empty($studente['data_esame_teoria'])) {
            $studente['data_esame_teoria'] = null;
        }

        if (empty($studente['nome'])) {
            $errors['nome'] = 'Campo obbligatorio';
        } elseif (!preg_match(\ScuolaGuida\Entity\Studente::NAME_PATTERN, $studente['nome'])) {
            $errors['nome'] = 'Questo campo contiene caratteri non supportati';
        } else if (strlen($studente['nome']) > 50) {
            $errors['nome'] = 'Campo non valido';
        }

        if (empty($studente['cognome'])) {
            $errors['cognome'] = 'Campo obbligatorio';
        } elseif (!preg_match(\ScuolaGuida\Entity\Studente::NAME_PATTERN, $studente['cognome'])) {
            $errors['cognome'] = 'Questo campo contiene caratteri non supportati';
        } else if (strlen($studente['cognome']) > 50) {
            $errors['cognome'] = 'Campo non valido';
        }

        if (empty($studente['data_nascita'])) {
            $errors['data_nascita'] = 'Campo obbligatorio';
        } else if (strtotime($studente['data_nascita']) > time()) {
            $errors['data_nascita'] = 'Data non valida';
        }

        if ($studente['sesso'] != 'M' && $studente['sesso'] != 'F') {
            $errors['sesso'] = 'Sesso non valido';
        }

        if ($studente['tipo_esame'] != 'B' && $studente['tipo_esame'] != 'AM') {
            $errors['tipo_esame'] = 'Tipo esame non valido';
        }

        if (empty($studente['email'])) {
            $errors['email'] = 'Campo obbligatorio';
        } else if (!filter_var($studente['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Indirizzo email non valido';
        } else {
            $found = $this->studentiTable->find('email', $studente['email']);
            if (!empty($found) && (empty($studente['id']) || $found[0]->id != $studente['id'])) {
                $errors['email'] = 'Indirizzo email già registrato';
            }
        }

        if (empty($errors)) {
            //$plaintext_password = \ScuolaGuida\Entity\Studente::genNewPassword();
            $studente['id_autoscuola'] = $this->authentication->getUser()->id;
            try {
                if (empty($this->studentiTable->findById($studente['id']))) {
                    $uuid = SoUuid::generate();
                    $studente['password'] = password_hash(str_replace(' ', '', strtolower($studente['cognome'])), PASSWORD_DEFAULT);
                    $studente['id'] = $uuid->getString();
                }
                $this->studentiTable->save($studente);
                header('location: /admin/studenti');
            } catch (\PDOException $e) {
                return [
                    'template' => 'admin/registra-studente.html.php',
                    'title' => 'Nuovo studente',
                    'layoutVariables' => [
                        'breadcrumbs' => ['Studenti', 'Nuovo studente']
                    ],
                    'variables' => [
                        'exception' => $e
                    ]
                ];
            } catch (\PHPMailer\PHPMailer\Exception $e) {
                return [
                    'template' => 'admin/registra-studente.html.php',
                    'title' => 'Nuovo studente',
                    'layoutVariables' => [
                        'breadcrumbs' => ['Studenti', 'Nuovo studente']
                    ],
                    'variables' => [
                        'mail_exception' => $e
                    ]
                ];
            }
        } else {
            return [
                'template' => 'admin/registra-studente.html.php',
                'title' => 'Nuovo studente',
                'layoutVariables' => [
                    'breadcrumbs' => ['Studenti', 'Nuovo studente']
                ],
                'variables' => [
                    'errors' => $errors
                ]
            ];
        }
    }

    public function success()
    {
        $studente = $this->studentiTable->findById(base64_decode($_GET['id']));
        if (!empty($studente)) {
            return [
                'template' => 'register-success.html.php',
                'title' => 'Account creato',
                'variables' => [
                    'studente' => $studente
                ]
            ];
        } else {
            http_response_code(404);
        }
    }

    public function delete()
    {
        if (!empty($_GET['id'])) {
            $studente = $this->studentiTable->findById($_GET['id']);
            if (!empty($studente)) {
                $this->studentiTable->delete($studente->id);
                $queryString = rtrim(($_GET['page'] ? 'page=' . $_GET['page'] . '&' : '') . ($_GET['orderBy'] ? 'orderBy=' . $_GET['orderBy'] . '&' : '') . ($_GET['sortOrder'] ? 'sortOrder=' . $_GET['sortOrder'] . '&' : '') . ($_GET['q'] ? 'q=' . $_GET['q'] . '&' : ''), '&');
                header('location: /admin/studenti?' . $queryString);
            } else {
                http_response_code(404);
            }
        } else {
            http_response_code(404);
        }
    }

    public function edit()
    {
        if (!empty($_GET['id'])) {
            $studente = $this->studentiTable->findById($_GET['id']);
            if (!empty($studente)) {
                return [
                    'template' => 'admin/registra-studente.html.php',
                    'title' => 'Modifica studente',
                    'layoutVariables' => [
                        'breadcrumbs' => ['Studenti', 'Nuovo studente']
                    ],
                    'variables' => [
                        'studente' => $studente
                    ]
                ];
            } else {
                http_response_code(404);
            }
        } else {
            http_response_code(404);
        }
    }

    private function test()
    {
        $autoscuola = $this->authentication->getUser();
        $frst = array('Giorgio', 'Aurora', 'Gabriele', 'Nicola', 'David', 'Marcello', 'Silvia', 'Giada', 'Leonardo', 'Luca', 'Francesca', 'Francesco');
        $last = array('Cesari', 'Marinelli', 'Alunni', 'Rossi', 'Verdi', 'Neri', 'Esposito', 'Gambino', 'Cagnoni', 'Lamberti', 'Russo', 'Nero');
        $i = 0;
        for ($i = 0; $i < 100; $i++) {
            $rand_frst = array_rand($frst); // no idea why you wanted to select 4 by random,
            $rand_last = array_rand($last); // when you're only using 1. so i've removed

            $uuid = SoUuid::generate();
            $user = array(
                'id' => $uuid->getString(),
                'nome' => $frst[$rand_frst],
                'cognome'  => $last[$rand_last],
                'email'      => strtolower($frst[$rand_frst] . $last[$rand_last]) . '@gmail.com',
                'password'   => password_hash($last[$rand_last], PASSWORD_DEFAULT),
                'data_nascita' => date("Y-m-d", mt_rand(1262055681, time())),
                'sesso' => array('M', 'F')[mt_rand(0, 1)],
                'id_autoscuola' => $autoscuola->id,
                'tipo_esame' => array('AM', 'B')[mt_rand(0, 1)],
                'data_esame_teoria' => date("Y-m-d", mt_rand(time(), time() + 7884000000))
            );
            $this->studentiTable->save($user);
        }
    }
}
