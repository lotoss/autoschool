<?php

namespace ScuolaGuida\Controllers;

use Ninja\Authentication;

class Login
{
    private $authentication;

    public function __construct(Authentication $authentication)
    {
        $this->authentication = $authentication;
    }

    public function showLoginPage()
    {
        return [
            'template' => 'public/login.html.php',
            'title' => 'Log In'
        ];
    }

    public function processLogin()
    {
        if ($this->authentication->login($_POST['email'], $_POST['password'])) {
            $user = $this->authentication->getUser();
            if ($user instanceof \ScuolaGuida\Entity\Autoscuola) {
                header('location: /admin/lezione?controller=argomentiController');
            } else {
                header('location: /domande');
            }
        } else {
            return [
                'template' => 'public/login.html.php',
                'title' => 'Log In',
                'variables' => [
                    'error' => 'Email o password non validi'
                ]
            ];
        }
    }
}
