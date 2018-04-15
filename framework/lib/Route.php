<?php
namespace framework\lib;
use Exception;
/**
 * @method static Route get(string $route, Callable $callback)
 * @method static Route post(string $route, Callable $callback)
 * @method static Route put(string $route, Callable $callback)
 * @method static Route delete(string $route, Callable $callback)
 * @method static Route options(string $route, Callable $callback)
 * @method static Route head(string $route, Callable $callback)
 */
class Route {
  public static $halts = false;
  public static $routes = array();
  public static $methods = array();
  public static $callbacks = array();
  public static $patterns = array(
      ':any' => '[^/]+',
      ':num' => '[0-9]+',
      ':all' => '.*'
  );
  public static $error_callback;

  /**
   * Defines a route w/ callback and method
   */
  public static function __callstatic($method, $params) {
    $uri = dirname($_SERVER['PHP_SELF']).'/'.$params[0];
    $callback = $params[1];


    array_push(self::$routes, $uri);
    array_push(self::$methods, strtoupper($method));
    array_push(self::$callbacks, $callback);

  }

  /**
   * Defines callback if route is not found
  */
  public static function error($callback) {
    self::$error_callback = $callback;
  }

  public static function haltOnMatch($flag = true) {
    self::$halts = $flag;
  }

  /**
   * Runs the callback for the given request
   */
  public static function dispatch(){

    $request = Request::instance();
    $configs = Application::$configs;

    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $method = $_SERVER['REQUEST_METHOD'];


    $searches = array_keys(static::$patterns);
    $replaces = array_values(static::$patterns);
    
    $found_route = false;

    self::$routes = preg_replace('/\/+/', '/', self::$routes);
    
    $suffix = isset($configs['suffix']) ? $configs['suffix'] : '';
    if ( !empty($suffix) && strstr( $uri , $suffix ) !== false ) {
        $uri = strstr( $uri , $suffix ,true);
    }

    // 如果没有定义正则表达式检查路由
    if (in_array($uri, self::$routes)) {
      $route_pos = array_keys(self::$routes, $uri);
      foreach ($route_pos as $route) {
        // Using an ANY option to match both GET and POST requests
        if (self::$methods[$route] == $method || self::$methods[$route] == 'ANY') {
          $found_route = true;

          // If route is not an object
          if (!is_object(self::$callbacks[$route])) {

            // Grab all parts based on a / separator
            $parts = explode('/',self::$callbacks[$route]);

            // Collect the last index of the array
            $last = end($parts);

            // Grab the controller name and method call
            $segments = explode('@',$last);


              $class = $segments[0];
              $action = $segments[1];
              if ( strstr( $action , '?') !== false ) {
                  $action = strstr( $segments[1] , '?' , true);
                  $parameters = substr( strstr( $segments[1] , '?'),1);
                  $values = array_values($matched);
                  $params = preg_replace_callback('/:(\d+)/', function($matched) use($values) { 
                    return $values[$matched[1] - 1];
                  }, $parameters);
                  parse_str ( $params ,  $vars );
                  $_GET = array_merge($vars,$_GET);               
              }

              $request_route = explode("\controller\\",$class);

              $request->action( $action ? $action : $configs['action'] );
              $request->controller( $request_route ? array_pop($request_route) : $configs['controller'] );
              $request->module( $request_route ? array_pop($request_route) : $configs['module'] );

              // Instanitate controller
              $controller = new $class();

              // Fix multi parameters
              if (!method_exists($controller, $action)) {
                throw new Exception('controller and action not found:' . $request->controller() . '->' . $request->action() );
              } else {
                 $controller->$action();
              }

            // // Instanitate controller
            // $controller = new $segments[0]();

            // // Call method
            // $controller->{$segments[1]}();

            if (self::$halts) return;
          } else {
            // Call closure
            call_user_func(self::$callbacks[$route]);

            if (self::$halts) return;
          }
        }
      }
    } else {
      // 检查定义的正则表达式
      $pos = 0;

      foreach (self::$routes as $route) {
        if (strpos($route, ':') !== false) {
          $route = str_replace($searches, $replaces, $route);
        }
        
        if (preg_match('#^' . $route . '$#', $uri, $matched)) {
          if (self::$methods[$pos] == $method || self::$methods[$pos] == 'ANY') {
            $found_route = true;

            // Remove $matched[0] as [1] is the first parameter.
            array_shift($matched);

            if (!is_object(self::$callbacks[$pos])) {

              // Grab all parts based on a / separator
              $parts = explode('/',self::$callbacks[$pos]);
                  
              // Collect the last index of the array
              $last = end($parts);
              
              // Grab the controller name and method call
              $segments = explode('@',$last);
               
              $class = $segments[0];
              $action = $segments[1];
              if ( strstr( $action , '?') !== false ) {
                  $action = strstr( $segments[1] , '?' , true);
                  $parameters = substr( strstr( $segments[1] , '?'),1);
                  $values = array_values($matched);
                  $params = preg_replace_callback('/:(\d+)/', function($matched) use($values) { 
                    return $values[$matched[1] - 1];
                  }, $parameters);
                  parse_str ( $params ,  $vars );
                  $_GET = array_merge($vars,$_GET);               
              }

              $request_route = explode("\controller\\",$class);

              $request->action( $action ? $action : $configs['action'] );
              $request->controller( $request_route ? array_pop($request_route) : $configs['controller'] );
              $request->module( $request_route ? array_pop($request_route) : $configs['module'] );

              // Instanitate controller
              $controller = new $class();

              // Fix multi parameters
              if (!method_exists($controller, $action)) {
                throw new Exception('controller and action not found:' . $request->controller() . '->' . $request->action() );
              } else {
                call_user_func_array(array($controller, $action), $matched);
              }

              if (self::$halts) return;
            } else {
              call_user_func_array(self::$callbacks[$pos], $matched);

              if (self::$halts) return;
            }
          }
        }
        $pos++;
      }
    }


    // Run the error callback if the route was not found
    if ($found_route == false) {
      if (!self::$error_callback) {
        self::$error_callback = function() {
          header($_SERVER['SERVER_PROTOCOL']." 404 Not Found");
          echo '404';
        };
      } else {
        if (is_string(self::$error_callback)) {
          self::get($_SERVER['REQUEST_URI'], self::$error_callback);
          self::$error_callback = null;
          self::dispatch();
          return ;
        }
      }
      call_user_func(self::$error_callback);
    }
  }
}
