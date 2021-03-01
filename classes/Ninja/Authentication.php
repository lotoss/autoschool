<?php

namespace Ninja;

class Authentication
{
  private $autoscuoleTable;
  private $studentiTable;
  private $usernameColumn;
  private $passwordColumn;

  private $user;

  public function __construct(Databasetable $autoscuoleTable, DatabaseTable $studentiTable, $usernameColumn, $passwordColumn)
  {
    if (!isset($_SESSION)) {
      session_start();
    }
    $this->autoscuoleTable = $autoscuoleTable;
    $this->studentiTable = $studentiTable;
    $this->usernameColumn = $usernameColumn;
    $this->passwordColumn = $passwordColumn;
  }

  public function login($username, $password)
  {
    $user = $this->studentiTable->find($this->usernameColumn, strtolower($username));

    if (!empty($user) && password_verify($password, $user[0]->{$this->passwordColumn})) {
      session_regenerate_id();
      $_SESSION['username'] = strtolower($username);
      $_SESSION['password'] = $user[0]->{$this->passwordColumn};
      $_SESSION['role'] = 'studente';
      return true;
    } else {
      $user = $this->autoscuoleTable->find($this->usernameColumn, strtolower($username));
      if (!empty($user) && password_verify($password, $user[0]->{$this->passwordColumn})) {
        session_regenerate_id();
        $_SESSION['username'] = strtolower($username);
        $_SESSION['password'] = $user[0]->{$this->passwordColumn};
        $_SESSION['role'] = 'autoscuola';
        return true;
      } else {
        return false;
      }
    }
  }

  public function isLoggedIn()
  {
    if (empty($_SESSION['username']) || empty($_SESSION['password'])) {
      return false;
    }

    if ($_SESSION['role'] == 'studente') {
      $user = $this->studentiTable->find($this->usernameColumn, $_SESSION['username']);
    } elseif ($_SESSION['role'] == 'autoscuola') {
      $user = $this->autoscuoleTable->find($this->usernameColumn, strtolower($_SESSION['username']));
    }

    $passwordColumn = $this->passwordColumn;
    if (!empty($user) && $user[0]->$passwordColumn === $_SESSION['password']) {
      return true;
    } else {
      return false;
    }
  }

  public function getUser()
  {
    if (empty($this->user)) {
      if ($this->isLoggedIn()) {
        if ($_SESSION['role'] == 'studente') {
          $user = $this->studentiTable->find($this->usernameColumn, strtolower($_SESSION['username']));
        } else if ($_SESSION['role'] == 'autoscuola') {
          $user = $this->autoscuoleTable->find($this->usernameColumn, strtolower($_SESSION['username']));
        }

        if (!empty($user)) {
          $this->user = $user[0];
        }
      }
    }

    return $this->user;
  }
}
