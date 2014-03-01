<?php

/**
 * Base class that all controllers use.
 */
abstract class Base_Controller {
    // Properties that all controllers should use //

    /**
     * The current view object.
     * @var Object
     */
    private $view;

    /**
     * The current model to use.
     * @var object
     */
    private $model;

    /**
     * The default template to use.
     * @var object
     */
    private $template;

    /**
     * Default method that loads the object for viewing.
     * @param array $get_vars
     */
    abstract function load(array $get_vars);
}

?>