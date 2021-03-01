<?php

try {
  include __DIR__ . '/../includes/autoload.php';
  include __DIR__ . '/../vendor/autoload.php';

  $route = strtolower(ltrim(strtok($_SERVER['REQUEST_URI'], '?'), '/'));

  $entryPoint = new \Ninja\EntryPoint($route, $_SERVER['REQUEST_METHOD'], new \ScuolaGuida\ScuolaGuidaRoutes());
  $entryPoint->run();
} catch (Exception $e) {
  $output = $e->getMessage() . ' in ' . $e->getFile() . ': ' . $e->getLine();
  echo $output;
}
