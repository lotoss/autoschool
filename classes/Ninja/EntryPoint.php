<?php

namespace Ninja;

class EntryPoint
{
  private $route;
  private $method;
  private $routes;
  private $rootPath;

  public function __construct(string $route, string $method, \Ninja\Routes $routes)
  {
    $this->route = $route;
    $this->routes = $routes;
    $this->method = $method;
    $this->CheckUrl();
  }

  private function CheckUrl()
  {
    if ($this->route !== strtolower($this->route)) {
      http_response_code(301);
      header('location: ' . strtolower($this->route));
    }
  }

  private function loadTemplate($templateFileName, $variables = [])
  {
    extract($variables);

    ob_start();

    include __DIR__ . '/../../templates/' . $templateFileName;

    return ob_get_clean();
  }

  public function run()
  {
    $routes = $this->routes->getRoutes();
    $authentication = $this->routes->getAuthentication();

    if (!empty($routes[$this->route])) {
      if (isset($routes[$this->route]['login']) && !$authentication->isLoggedIn()) {
        header('location: /login');
      } else if (isset($routes[$this->route]['permissions']) && !$this->routes->checkPermission(($routes[$this->route]['permissions']))) {
        header('location: /login');
      }

      $controller = $routes[$this->route][$this->method]['controller'];
      $action = $routes[$this->route][$this->method]['action'];
      if (method_exists($controller, $action)) {
        $page = $controller->{$action}();
        if (isset($page['template'])) {
          $title = $page['title'];

          if ((isset($routes[$this->route]['layout_free']) && !$routes[$this->route]['layout_free'] || !isset($routes[$this->route]['layout_free'])) && in_array('layout.html.php', scandir(__DIR__ . '/../../templates/' . pathinfo($page['template'])['dirname']))) {
            if (isset($page['variables'])) {
              $output = $this->loadTemplate($page['template'], $page['variables']);
            } else {
              $output = $this->loadTemplate($page['template']);
            }

            $layout = pathinfo($page['template'])['dirname'] . '/layout.html.php';

            $vars = array_merge($page['layoutVariables'] ?? [], ['output' => $output, 'title' => $title, 'autoscuola' => $authentication->getUser()]);

            echo $this->loadTemplate($layout, $vars);
          } else {
            echo $this->loadTemplate($page['template'], $page['variables'] ?? []);
          }
        }
      } else {
        http_response_code(404);
      }
    } else {
      http_response_code(404);
    }
  }
}
