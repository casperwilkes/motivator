<?php

class Upload_Controller extends Base_Controller {

    /**
     * Template to use for uploading form.
     * @var string
     */
    private $template = 'upload';

    /**
     * The upload model.
     * @var Upload_Model
     */
    private $model;

    /**
     * The template model.
     * @var View_Model
     */
    private $view;

    /**
     * Used to set and retrieve session data.
     * @var Session
     */
    private $session;

    public function __construct() {
        // Initialize session //
        $this->session = new Session();
        // Initialize model //
        $this->model = new Upload_Model();
        // Initialize view //
        $this->view = new View($this->template);
        // Set page title //
        $this->view->set_title('Upload');
    }

    public function load(array $get_vars) {
        // Assign template variables //
        $this->view->assign('max_file_size', $this->model->get_max_file_size());
        // Check if post data is empty //
        if (!empty($get_vars['post'])) {
            $this->model->gather_file_info($get_vars['files']['file_upload']);
            // Assign errors if any //
            $this->view->assign('js_error', $this->model->gather_poster_data($get_vars['post'], $this->session));
        }
        // Show the guide //
        $this->view->assign('guide_array', $this->model->get_guide_array());
        // Render template //
        $this->view->render();
    }

}

?>