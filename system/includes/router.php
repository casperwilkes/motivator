<?php

/**
 * @author Casper Wilkes <casper@casperwilkes.net>
 * 
 * Controlls the routing of the application.
 */
class Router {

    /**
     * The query string pulled form $_SERVER['QUERY_STRING].
     * @var string
     */
    private $request;

    /**
     * Page being requested.
     * @var string
     */
    private $page;

    /**
     * The target file to be included in the request.
     * @var string
     */
    private $target;

    /**
     * Array of variables used throughout the application.
     * @var array
     */
    public $get_vars = array();

    /**
     * Set the request property and seperate the incoming variables.
     */
    public function __construct() {
        $this->request = $_SERVER['QUERY_STRING'];
        $this->seperate_request();
        $this->load_extras();
    }

    /*
     * Loads the page, corresponding to the classes and files.
     */

    public function load() {
        // Include the file //
        include_once $this->target;
        // Modify page to naming convention //
        $class = ucfirst($this->page) . '_Controller';
        // instatiate appropriate class for request //
        if (class_exists($class)) {
            $controller = new $class();
            $controller->load($this->get_vars);
        } else {
            log_message(__CLASS__, __METHOD__, 'Could not locate: ' . $class);
            die("Could not locate page.");
        }
    }

    /**
     * Splits the incoming request and sets properties.
     */
    private function seperate_request() {
        // Parse out the incoming request or set it to empty //
        $parsed = (strlen($this->request)) ? explode('&', $this->request) : '';
        // Parse the page request //
        $this->page = (is_array($parsed)) ? array_shift($parsed) : 'main';
        // Set the target file based on page //
        $this->target = SYSTEM . DS . 'controllers' . DS . $this->page . '.php';
        // Make sure that target file exists //
        if (!file_exists($this->target)) {
            // If doesn't exist, force inventory as page, and force inventory to target //
            $this->page = 'main';
            $this->target = SYSTEM . DS . 'controllers' . DS . 'main.php';
        }
        // Set the variables for get_vars //
        if (is_array($parsed)) {
            foreach ($parsed as $argument) {
                // Split get requests into variables and values //
                list($variable, $value) = explode('=', $argument);
                $this->get_vars[$variable] = urldecode($value);
            }
        }
    }

    /**
     * Adds the $_POST and $_FILES globals to the get_vars variable.
     */
    private function load_extras() {
        $this->get_vars['post'] = (isset($_POST) && !empty($_POST)) ? $_POST : null;
        $this->get_vars['files'] = (isset($_FILES) && !empty($_FILES)) ? $_FILES : null;
    }

}

?>