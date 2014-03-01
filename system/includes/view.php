<?php

/**
 * @author Casper Wilkes <casper@casperwilkes.net>
 * 
 * This model controlls the templating system.
 */
class View {

    /**
     * The location of the templates directory.
     * @var string
     */
    private $templates_dir;

    /**
     * The data passed to the templing system from the controllers.
     * @var array
     */
    private $data = array();

    /**
     * The template to load.
     * @var string
     */
    private $template;

    /**
     * Sets whether to render the page or show an error.
     * @var boolean
     */
    private $render = false;

    /**
     * The title of the page.
     * @var string
     */
    private $title = '[M]otivator';

    public function __construct($template = '') {
        $this->set_template_dir();
        $this->compose_template($template);
    }

    /**
     * Compose the template after initialization.
     * @param string $template
     */
    public function compose_template_late($template) {
        $this->compose_template($template);
    }

    /**
     * Build and display the requested page.
     */
    public function render() {
        // Header of page //
        include $this->templates_dir . DS . 'tpl.header.php';
        // Content of page // c
        if (is_bool($this->render) && $this->render == true) {
            include $this->template;
        } else {
            log_message(__CLASS__, __METHOD__, 'Could not load template: ' . basename($this->template));
            include $this->templates_dir . DS . 'tpl.error.php';
        }
        // Footer of page //
        include $this->templates_dir . DS . 'tpl.footer.php';
    }

    /**
     * Sets the title of the page.
     * @param string $title
     */
    public function set_title($title = null) {
        if (strlen($title)) {
            $this->title.= " | {$title}";
        }
    }

    /**
     * Assigns variables and elements to the data array.
     * @param string $variable
     * @param mixed $value
     */
    public function assign($variable, $value) {
        $this->data[$variable] = $value;
    }

    /**
     * Sets the destination to the templates directory.
     */
    private function set_template_dir() {
        $this->templates_dir = SYSTEM . DS . 'views';
    }

    /**
     * Fetches the template to use for the requested page.
     * @param string $template
     */
    private function compose_template($template) {
        // Brings template together //
        $this->template = $this->templates_dir . DS . 'tpl.' . strtolower($template) . '.php';

        if (file_exists($this->template)) {
            $this->render = true;
        }
    }

}

?>