<?php

namespace ScuolaGuida\Controllers;

class Admin
{
    public function setWorkMode()
    {
        if (isset($_SESSION['work_mode'])) {
            unset($_SESSION['work_mode']);
        } else {
            $_SESSION['work_mode'] = true;
        }
        //echo json_encode(array('work_mode' => !empty($_SESSION['work_mode'])));
        header('location: ' . $_SERVER['HTTP_REFERER']);
    }

    public function setPatente()
    {
        $_SESSION['patente'] = $_GET['patente'];
        //echo json_encode(array('work_mode' => !empty($_SESSION['work_mode'])));
        header('location: ' . $_SERVER['HTTP_REFERER']);
    }
}
