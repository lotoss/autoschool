<?php

namespace ScuolaGuida\Controllers;

use Exception;
use \Ninja\DatabaseTable;
use \Ninja\Authentication;
use ScuolaGuida\Entity\Autoscuola;

class Argomenti
{
    private $authentication;
    private $capitoliTable;
    private $gruppiTable;
    private $commentiTable;
    private $domandeTable;
    private $domandeContrapposteTable;
    private $immaginiGruppiTable;
    private $gradoDomandeTable;

    public function __construct(Authentication $authentication, DatabaseTable $capitoliTable, DatabaseTable $gruppiTable, DatabaseTable $commentiTable, DatabaseTable $domandeTable, DatabaseTable $domandeContrapposteTable, Databasetable $immaginiGruppiTable, DatabaseTable $gradoDomandeTable)
    {
        $this->authentication = $authentication;
        $this->capitoliTable = $capitoliTable;
        $this->gruppiTable = $gruppiTable;
        $this->commentiTable = $commentiTable;
        $this->domandeTable = $domandeTable;
        $this->domandeContrapposteTable = $domandeContrapposteTable;
        $this->immaginiGruppiTable = $immaginiGruppiTable;
        $this->gradoDomandeTable = $gradoDomandeTable;
        if (!isset($_SESSION['patente'])) {
            $_SESSION['patente'] = 'B';
        }
    }

    public function list()
    {
        $capitoli = $this->capitoliTable->find('id_patente', $_SESSION['patente']);
        if ($_SESSION['role'] == 'autoscuola') {
            return [
                'template' => 'admin/argomenti.html.php',
                'title' => 'Argomenti - Argomenti',
                'layoutVariables' => [
                    'breadcrumbs' => ['Lezione', 'Argomenti'],
                    'selectPatente' => true,
                    'search' => true
                ],
                'variables' => [
                    'capitoli' => $capitoli
                ]
            ];
        } else {
            header('location: /login');
        }
    }

    public function viewChapter()
    {
        $capitolo = $this->capitoliTable->find(array('id', 'id_patente'), array($_GET['id'], $_SESSION['patente']));
        if ($_SESSION['role'] == 'autoscuola') {
            if (!empty($capitolo)) {
                return [
                    'template' => 'admin/gruppi.html.php',
                    'title' => $capitolo[0]->descrizione . ' - Argomenti',
                    'layoutVariables' => [
                        'breadcrumbs' => ['Lezione', 'Argomenti'],
                        'selectPatente' => true,
                        'search' => true
                    ],
                    'variables' => [
                        'capitolo' => $capitolo[0]
                    ]
                ];
            } else {
                http_response_code(404);
            }
        } else {
            header('location: /login');
        }
    }

    public function viewGroup()
    {
        $gruppo = $this->gruppiTable->find(array('id', 'id_patente'), array($_GET['id'], $_SESSION['patente']));
        if ($_SESSION['role'] == 'autoscuola') {
            if (!empty($gruppo)) {
                $gruppo = $gruppo[0];

                $questionImgs = [];
                $relatedFiles = [];
                $imageColumn = false;

                foreach ($gruppo->getDomande() as $domanda) {
                    if (!empty($domanda->id_immagine)) {
                        $imageColumn = true;
                        if ($domanda->id_immagine != $gruppo->id_immagine && !in_array($domanda->id_immagine, $questionImgs)) {
                            $questionImgs[] = $domanda->id_immagine;
                        }
                    }
                }

                foreach ($gruppo->getRelatedFiles() as $file) {
                    $relatedFiles[] = $file;
                }

                return [
                    'template' => 'admin/spiegazione.html.php',
                    'title' => $gruppo->descrizione . ' - Argomenti',
                    'layoutVariables' => [
                        'breadcrumbs' => ['Lezione', 'Argomenti'],
                        'selectPatente' => true,
                        'search' => true
                    ],
                    'variables' => [
                        'gruppo' => $gruppo,
                        'commenti' => $commenti ?? [],
                        'imageColumn' => $imageColumn,
                        'autoscuola' => $this->authentication->getUser(),
                        'relatedFiles' => $relatedFiles,
                        'questionImgs' => $questionImgs
                    ]
                ];
            } else {
                http_response_code(404);
            }
        } else {
            header('location: /login');
        }
    }

    public function uploadGrouprelatedFiles()
    {
        $gruppo = $this->gruppiTable->find(array('id', 'id_patente'), array($_POST['id'], $_SESSION['patente']));
        if (!empty($gruppo)) {
            $gruppo = $gruppo[0];
            $id_immagini = [];
            if (is_array($_FILES['files']['tmp_name'])) {
                for ($i = 0; $i < count($_FILES['files']['tmp_name']); $i++) {
                    if (file_exists($_FILES['files']['tmp_name'][$i])) {
                        // salvo il file che ha caricato l'utente
                        $file = new \Ninja\File($_FILES['files']['tmp_name'][$i]);
                        $file_name = (time() + ($i + 1)) . '.' . pathinfo($_FILES['files']['name'][$i], PATHINFO_EXTENSION);
                        $file->save(__DIR__ . '/../../../public/img/gruppi/immagini_correlate/' . $file_name, ['jpg', 'jpeg', 'png', 'gif']);
                        // salvo il percorso relativo l'immagine sul db
                        $id_immagini[] = $gruppo->saveRelatedImage($file_name)->id;
                    }
                }
            }
            echo json_encode(array('status' => 'OK', 'id_immagini' => $id_immagini));
        } else {
            http_response_code(404);
        }
    }

    public function saveRelFile()
    {
        $autoscuola = $this->authentication->getUser();
        if ($_SESSION['role'] == 'autoscuola') {
            $gruppo = $this->gruppiTable->find(array('id', 'id_patente'), array($_POST['id_gruppo'], $_SESSION['patente']));
            if (!empty($gruppo)) {
                $gruppo = $gruppo[0];

                if (!empty($_FILES['media-uploader-file']['tmp_name'])) {
                    $file = new \Ninja\File($_FILES['media-uploader-file']['tmp_name']);
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $mime = finfo_file($finfo, $_FILES['media-uploader-file']['tmp_name']);
                    $file_type = substr($mime, 0, strpos($mime, '/'));
                    $file_name = time() . '.' . strtolower(pathinfo($_FILES['media-uploader-file']['name'], PATHINFO_EXTENSION));
                    if ($file_type == 'image') {
                        $file->save(__DIR__ . '/../../../public/img/gruppi/correlate/' . $file_name, ['jpg', 'jpeg', 'png', 'gif']);
                    } else if ($file_type == 'video') {
                        $file->save(__DIR__ . '/../../../public/video/gruppi/correlati/' . $file_name, ['mov', 'mp4']);
                    }
                }

                $this->immaginiGruppiTable->save(array('id_gruppo' => $gruppo->id, 'id_autoscuola' => $autoscuola->id, 'file_name' => $file_name, 'file_type' => $file_type, 'id_gruppo_link' => $_POST['id_gruppo_link'] ?? null, 'id_domanda_link' => $_POST['id_domanda_link'] ?? null));
                header('location: ' . $_SERVER['HTTP_REFERER']);
            } else {
                http_response_code(404);
            }
        } else {
            header('location: /login');
        }
    }

    public function removeRelFile()
    {
        try {
            $file = $this->immaginiGruppiTable->findById($_GET['id']);
            if (!empty($file)) {
                if ($file->file_type == 'image') {
                    $file = new \Ninja\File(__DIR__ . '/../../../public/img/gruppi/correlate/' . $file->file_name);
                } else if ($file->file_type == 'video') {
                    $file = new \Ninja\File(__DIR__ . '/../../../public/video/gruppi/correlati/' . $file->file_name);
                } else {
                    throw new Exception("File type not supported");
                }
                $file->delete();
                $this->immaginiGruppiTable->delete($_GET['id']);
                echo json_encode(array("status" => "OK"));
            } else {
                throw new Exception("File not exists");
            }
        } catch (Exception $e) {
            echo json_encode(array('error' => $e->getMessage()));
        }
    }

    public function searchExpl()
    {
        if (!empty($_GET['q'])) {
            $gruppi = $this->gruppiTable->find(array('id_patente', 'descrizione'), array($_SESSION['patente'], '%' . $_GET['q'] . '%'), 'descrizione ASC', 100, null, 'LIKE');
            $domande = $this->domandeTable->find(array('id_patente', 'domanda'), array($_SESSION['patente'], '%' . $_GET['q'] . '%'), 'domanda ASC', 100, null, 'LIKE');
            echo json_encode(array('results' => array('gruppi' => $gruppi ?? [], 'domande' => $domande ?? [])));
        } else {
            echo json_encode(array('results' => []));
        }
    }

    public function viewQuestion()
    {
        $autoscuola = $this->authentication->getUser();
        $domanda = $this->domandeTable->find(array('id_gruppo', 'id'), array($_GET['id_gruppo'], $_GET['id_domanda']));
        if ($_SESSION['role'] == 'autoscuola') {
            if (!empty($domanda)) {
                $domanda = $domanda[0];
                if (!empty($domanda->getCommento())) {
                    if (!empty($domanda->getCommento()->id_domanda_link)) {
                        $d = $this->domandeTable->find(array('id', 'id_gruppo'), array($domanda->getCommento()->id_domanda_link, $domanda->getCommento()->id_gruppo_link));
                        if (!empty($d)) {
                            $mediaLinkedLabel = ucfirst($d[0]->domanda);
                        }
                    } elseif (!empty($domanda->getCommento()->id_gruppo_link)) {
                        $gruppo = $this->gruppiTable->findById($domanda->getCommento()->id_gruppo_link);
                        if (!empty($gruppo)) {
                            $mediaLinkedLabel = ucfirst($gruppo->descrizione);
                        }
                    }
                }
                return [
                    'template' => 'admin/risposta.html.php',
                    'title' => ucfirst($domanda->domanda) . ' - Argomenti competenze',
                    'layoutVariables' => [
                        'breadcrumbs' => ['Lezione', 'Argomenti'],
                        'selectPatente' => true,
                        'search' => true
                    ],
                    'variables' => [
                        'domanda' => $domanda,
                        'autoscuola' => $autoscuola,
                        'mediaLinkedLabel' => $mediaLinkedLabel ?? null
                    ]
                ];
            } else {
                http_response_code(404);
            }
        } else {
            header('location: /login');
        }
    }

    public function setGradoDomanda()
    {
        $autoscuola = $this->authentication->getUser();
        if ($_SESSION['role'] == 'autoscuola') {
            if (!empty($_GET['grado']) && (int) $_GET['grado'] > 0 && (int) $_GET['grado'] < 4) {
                if (!empty($_GET['id_grado'])) {
                    $domanda = $this->gradoDomandeTable->findById($_GET['id_grado']);
                    if ($domanda->grado == $_GET['grado']) {
                        $_GET['grado'] = 0;
                    }
                }
                $this->gradoDomandeTable->save(array('id' => $_GET['id_grado'] ?? null, 'id_autoscuola' => $autoscuola->id, 'id_gruppo' => $_GET['id_gruppo'], 'id_domanda' => $_GET['id_domanda'], 'grado' => $_GET['grado']));
                echo json_encode(array('status' => 'OK'));
            } else {
                http_response_code(404);
            }
        } else {
            header('location: /login');
        }
    }

    public function setDomandaContrapposta()
    {
        try {
            $domanda = $this->domandeContrapposteTable->find(array('id_domanda', 'id_gruppo', 'id_autoscuola'), array($_GET['id_domanda'], $_GET['id_gruppo'], $this->authentication->getUser()->id));
            if (!empty($domanda)) {
                $domanda = $domanda[0];
                $this->domandeContrapposteTable->delete($domanda->id);
            } else {
                $this->domandeContrapposteTable->save(array('id_domanda' => $_GET['id_domanda'], 'id_gruppo' => $_GET['id_gruppo'], 'id_autoscuola' => $this->authentication->getUser()->id));
            }
            echo json_encode(array('status' => 'OK', 'active' => empty($domanda)));
        } catch (\PDOException $e) {
            echo json_encode(array('error' => $e->getMessage()));
        }
    }

    public function saveComment()
    {
        if ($_SESSION['role'] == 'autoscuola') {
            if (!empty($_POST['id'])) {
                $commento = $this->commentiTable->findById($_POST['id']);
                $file_name = $commento->file_name;
                $file_type = $commento->file_type;
            }
            if (!empty($commento) && (isset($_POST['media_uploader_delete']) || (!empty($_FILES['media-uploader-file']['tmp_name']) && !empty($commento->file_name)))) {
                if ($commento->file_type == 'image') {
                    $file = new \Ninja\File(__DIR__ . '/../../../public/img/domande/commenti/' . $commento->file_name);
                } else if ($commento->file_type == 'video') {
                    $file = new \Ninja\File(__DIR__ . '/../../../public/video/domande/commenti/' . $commento->file_name);
                }
                $file->delete();
                $file_name = null;
                $file_type = null;
            }
            if (!empty($_FILES['media-uploader-file']['tmp_name'])) {
                $file = new \Ninja\File($_FILES['media-uploader-file']['tmp_name']);
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_file($finfo, $_FILES['media-uploader-file']['tmp_name']);
                $file_type = substr($mime, 0, strpos($mime, '/'));
                $file_name = time() . '.' . strtolower(pathinfo($_FILES['media-uploader-file']['name'], PATHINFO_EXTENSION));
                if ($file_type == 'image') {
                    $file->save(__DIR__ . '/../../../public/img/domande/commenti/' . $file_name, ['jpg', 'jpeg', 'png', 'gif']);
                } else if ($file_type == 'video') {
                    $file->save(__DIR__ . '/../../../public/video/domande/commenti/' . $file_name, ['mov', 'mp4']);
                }
            }
            if (empty(trim($_POST['comment'])) && empty($file_name) && empty($_POST['']) && empty($_POST['id_gruppo_link']) && isset($commento->id)) {
                $this->commentiTable->delete($commento->id);
            } elseif (!empty(trim($_POST['comment'])) || !empty($file_name) || !empty($_POST['id_gruppo_link'])) {
                $this->commentiTable->save(array(
                    'id' => $_POST['id'] ?? null,
                    'id_domanda' => $_POST['id_domanda'],
                    'id_gruppo' => $_POST['id_gruppo'],
                    'id_autoscuola' => $this->authentication->getUser()->id,
                    'commento' => $_POST['comment'] ?? null,
                    'file_name' => $file_name ?? null,
                    'file_type' => $file_type ?? null,
                    'id_gruppo_link' => $_POST['id_gruppo_link'] ?? null,
                    'id_domanda_link' => $_POST['id_domanda_link'] ?? null
                ));
            }
            header('location: ' . $_SERVER['HTTP_REFERER']);
        } else {
            header('location: /login');
        }
    }
}
