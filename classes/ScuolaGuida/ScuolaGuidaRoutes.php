<?php

namespace ScuolaGuida;

use Ninja\Authentication;
use Ninja\DatabaseTable;

class ScuolaGuidaRoutes implements \Ninja\Routes
{
    private $studentiTable;
    private $capitoliTable;
    private $gruppiTable;
    private $domandeTable;
    private $domandeContrapposteTable;
    private $commentiTable;
    private $immaginiGruppiTable;
    private $gradoDomandeTable;
    private $immaginiCartelloneTable;
    private $authentication;

    public function __construct()
    {
        include __DIR__ . '/../../includes/DatabaseConnection.php';

        $this->studentiTable = new DatabaseTable($pdo, 'studenti', 'id', '\ScuolaGuida\Entity\Studente', []);
        $this->autoscuoleTable = new DatabaseTable($pdo, 'autoscuole', 'id', '\ScuolaGuida\Entity\Autoscuola', [&$this->studentiTable]);
        $this->capitoliTable = new DatabaseTable($pdo, 'capitoli', 'id', '\ScuolaGuida\Entity\Capitolo', [&$this->gruppiTable]);
        $this->gruppiTable = new DatabaseTable($pdo, 'gruppi', 'id', '\ScuolaGuida\Entity\Gruppo', [&$this->domandeTable, &$this->capitoliTable, &$this->immaginiGruppiTable, &$this->authentication]);
        $this->domandeTable = new DatabaseTable($pdo, 'domande', 'id', '\ScuolaGuida\Entity\Domanda', [&$this->gradoDomandeTable, &$this->domandeContrapposteTable, &$this->commentiTable, &$this->gruppiTable, &$this->authentication]);
        $this->domandeContrapposteTable = new DatabaseTable($pdo, 'domande_contrapposte', 'id');
        $this->immaginiGruppiTable = new DatabaseTable($pdo, 'immagini_gruppi', 'id');
        $this->commentiTable = new DatabaseTable($pdo, 'commenti', 'id');
        $this->gradoDomandeTable = new DatabaseTable($pdo, 'grado_domande', 'id');
        $this->immaginiCartelloneTable = new DatabaseTable($pdo, 'immagini_cartellone', 'id');
        $this->authentication = new Authentication($this->autoscuoleTable, $this->studentiTable, 'email', 'password');
    }

    public function getRoutes(): array
    {
        $argomentiController = new Controllers\Argomenti($this->authentication, $this->capitoliTable, $this->gruppiTable, $this->commentiTable, $this->domandeTable, $this->domandeContrapposteTable, $this->immaginiGruppiTable, $this->gradoDomandeTable);
        $loginController = new Controllers\Login($this->authentication);
        $studentiController = new Controllers\Studenti($this->authentication, $this->studentiTable);
        $ricezioneRisposteController = new Controllers\RicezioneRisposte($this->authentication, $this->studentiTable, $this->domandeTable);
        $domandeController = new Controllers\Domande($this->authentication, $this->domandeTable);
        $lezioneSearchController = new Controllers\LezioneSearch($this->capitoliTable, $this->gruppiTable, $this->domandeTable);
        $cartelloneController = new Controllers\Cartellone($this->capitoliTable, $this->gruppiTable, $this->domandeTable, $this->immaginiCartelloneTable);
        $adminController = new Controllers\Admin();

        return [
            'admin/lezione' => [
                'GET' => [
                    'controller' => ${$_GET['controller'] ?? ''} ?? '',
                    'action' => $_GET['action'] ?? 'list'
                ],
                'POST' => [
                    'controller' => ${$_GET['controller'] ?? ''} ?? '',
                    'action' => $_GET['action'] ?? ''
                ],
                'login' => true,
                'permissions' => \ScuolaGuida\Entity\Utente::ADMIN_PERMISSIONS
            ],
            'admin/lezione/setgradodomanda' => [
                'GET' => [
                    'controller' => $argomentiController,
                    'action' => 'setGradoDomanda'
                ],
                'login' => true,
                'permissions' => \ScuolaGuida\Entity\Utente::ADMIN_PERMISSIONS
            ],
            'admin/remove-related-file' => [
                'GET' => [
                    'controller' => $argomentiController,
                    'action' => 'removeRelFile'
                ],
                'login' => true,
                'permissions' => \ScuolaGuida\Entity\Utente::ADMIN_PERMISSIONS
            ],
            'admin/ricezione-risposte' => [
                'GET' => [
                    'controller' => $ricezioneRisposteController,
                    'action' => 'showRicezione'
                ],
                'login' => true,
                'permissions' => \ScuolaGuida\Entity\Utente::ADMIN_PERMISSIONS
            ],
            'admin/setworkmode' => [
                'GET' => [
                    'controller' => $adminController,
                    'action' => 'setWorkMode'
                ],
                'login' => true,
                'permissions' => \ScuolaGuida\Entity\Utente::ADMIN_PERMISSIONS
            ],
            'admin/setpatente' => [
                'GET' => [
                    'controller' => $adminController,
                    'action' => 'setPatente'
                ],
                'login' => true,
                'permissions' => \ScuolaGuida\Entity\Utente::ADMIN_PERMISSIONS
            ],
            'admin/ricezione-risposte/getdomanda' => [
                'GET' => [
                    'controller' => $ricezioneRisposteController,
                    'action' => 'getDomanda'
                ],
                'login' => true,
                'permissions' => \ScuolaGuida\Entity\Utente::ADMIN_PERMISSIONS
            ],
            'admin/lezione/search' => [
                'GET' => [
                    'controller' => $lezioneSearchController,
                    'action' => 'search'
                ],
                'login' => true,
                'permissions' => \ScuolaGuida\Entity\Utente::ADMIN_PERMISSIONS
            ],
            'admin/set-domanda-contrapposta' => [
                'GET' => [
                    'controller' => $argomentiController,
                    'action' => 'setDomandaContrapposta'
                ],
                'login' => true,
                'permissions' => \ScuolaGuida\Entity\Utente::ADMIN_PERMISSIONS
            ],
            'admin/studenti' => [
                'GET' => [
                    'controller' => $studentiController,
                    'action' => $_GET['action'] ?? 'list'
                ],
                'POST' => [
                    'controller' => $studentiController,
                    'action' => 'saveEdit'
                ],
                'login' => true,
                'permissions' => \ScuolaGuida\Entity\Utente::ADMIN_PERMISSIONS
            ],
            'login' => [
                'GET' => [
                    'controller' => $loginController,
                    'action' => 'showLoginPage'
                ],
                'POST' => [
                    'controller' => $loginController,
                    'action' => 'processLogin'
                ]
            ],
            'domande' => [
                'GET' => [
                    'controller' => $domandeController,
                    'action' => 'showDomande'
                ],
                'login' => true
            ],
            'getdomanda' => [
                'GET' => [
                    'controller' => $domandeController,
                    'action' => 'getDomanda'
                ],
                'login' => true
            ],
            'getstudente' => [
                'GET' => [
                    'controller' => $ricezioneRisposteController,
                    'action' => 'getStudente'
                ],
                'login' => true,
                'permissions' => \ScuolaGuida\Entity\Utente::ADMIN_PERMISSIONS
            ],
            '' => [
                'GET' => [
                    'controller' => $loginController,
                    'action' => 'showLoginPage'
                ],
                'POST' => [
                    'controller' => $loginController,
                    'action' => 'processLogin'
                ]
            ],
            'register-success' => [
                'GET' => [
                    'controller' => $studentiController,
                    'action' => 'success'
                ],
            ],
            'authuser' => [
                'GET' => [
                    'controller' => $studentiController,
                    'action' => 'authuser'
                ]
            ],
            'send-auth-mail' => [
                'GET' => [
                    'controller' => $studentiController,
                    'action' => 'sendAuthMail'
                ]
            ]
        ];
    }

    public function getAuthentication(): Authentication
    {
        return $this->authentication;
    }

    public function checkPermission($permissions): bool
    {
        $user = $this->authentication->getUser();

        if ($user && $user->hasPermission($permissions)) {
            return true;
        }

        return false;
    }
}
