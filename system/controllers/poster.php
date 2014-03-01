<?php

class Poster_Controller extends Base_Controller {

    /**
     * The templates to use for poster page.
     * @var array
     */
    private $template = array('success' => 'poster', 'error' => 'poster_error');

    /**
     * Poster model object.
     * @var Poster_Model
     */
    private $model;

    /**
     * Template model for views.
     * @var View_Model
     */
    private $view;

    /**
     * Session object.
     * @var Session
     */
    private $session;

    /**
     * The log for catching errors.
     * @var string
     */
    private $log = 'poster';

    public function __construct() {
        $this->session = new Session();
        $this->model = new Poster_Model();
        $this->view = new View();
        $this->view->set_title('Download Your Poster');
    }

    public function load(array $get_vars) {
        // Poster already created, send back to main page //
        if (is_null($this->session->check_image())) {
            redirect('index.php');
        }
        // Transfer Image object data from session to poster generator //
        if ($this->model->set_poster($this->session->get_image())) {
            // create the poster //
            $this->create_poster();
        } else {
            $this->view->compose_template_late($this->template['error']);
        }

        // Render Display //
        $this->view->render();
    }

    /**
     * Generates the poster, sets template variables, outputs to user.
     * @return boolean
     */
    private function create_poster() {
        $this->model->generate_poster();
        // Poster was created successfully //
        if ($this->model->get_result()) {
            // Assign template variables //
            $download_location = 'index.php?imageserve&im=' . $this->model->get_name();
            $this->view->compose_template_late($this->template['success']);
            $this->view->assign('dest', $this->model->get_destination());
            $this->view->assign('name', $this->model->get_name());
            $this->view->assign('button_query', $download_location);
            return TRUE;
        }
        // Creation failed, log entry //
        log_message($this->log, __METHOD__, 'Could not create poster from source: ' . $this->model->get_name());
        // Poster did not get created, send to error page instead //
        $this->view->compose_template_late($this->template['error']);

        return FALSE;
    }

}

?>