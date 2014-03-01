<?php

class Main_Controller extends Base_Controller {

    /**
     * Templates to use for index page.
     * @var array
     */
    private $template = array('success' => 'main', 'empty' => 'main_empty');

    /**
     * The object used for generating the index page.
     * @var Main_Model
     */
    private $model;

    /**
     * The templating object
     * @var View_Model
     */
    private $view;

    public function __construct() {
        $this->model = new Main_Model();
        $this->view = new View();
        $this->view->set_title('Welcome to the Motivator');
    }

    public function load(array $get_vars) {
        // Collect the images //
        $get_images = $this->model->get_images();
        if (!empty($get_images)) {
            // Images found in directory //
            $template = $this->template['success'];
            $this->view->assign('images', $get_images);
        } else {
            $template = $this->template['empty'];
        }
        // Use the corresponding template //
        $this->view->compose_template_late($template);
        // Render the template //
        $this->view->render();
    }

}

?>