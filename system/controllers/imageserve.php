<?php

class Imageserve_Controller extends Base_Controller {

    /**
     * THe model used for serving images.
     * @var Imageserve_Model
     */
    public $model;

    public function __construct() {
        $this->model = new Imageserve_Model();
    }

    public function load(array $get_vars) {
        $this->model->get_request($get_vars['im']);
        $this->model->serve_image();
    }

}

?>
