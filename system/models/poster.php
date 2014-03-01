<?php

class Poster_Model {

    /**
     * Image object
     * @var Image
     */
    private $image;

    /**
     * Whether poster creation was a success.
     * @var boolean
     */
    private $result = FALSE;

    /**
     * The error log to use.
     * @var string
     */
    private $log = 'poster';

    /**
     * Transfers the poster data from the handling object to the poster model.
     * @param Image $poster_data
     * @return boolean
     */
    public function set_poster($poster_data) {
        if (isset($poster_data)) {
            $this->image = $poster_data;
            return TRUE;
        } else {
            $this->image = null;
            return FALSE;
        }
    }

    /**
     * Return the name of the image.
     * @return string
     */
    public function get_name() {
        return basename($this->image->get_image_path());
    }

    /**
     * Return the final destination of the poster that was created.
     * @return string
     */
    public function get_destination() {
        return 'images' . DS . 'posters' . DS . basename($this->image->get_image_path());
    }

    /**
     * Return whether poster was created or not.
     * @return boolean
     */
    public function get_result() {
        return $this->result;
    }

    /**
     * Generates the poster and sets the result of creation. If creation failed
     * a log entry will be created. 
     */
    public function generate_poster() {
        if (!is_null($this->image)) {
            $gen = new Generator();
            if ($gen->establish_image($this->image)) {
                $gen->generate_poster();
                $this->result = $gen->get_result();
            } else {
                log_message($this->log, __METHOD__, 'Data was not valid');
                $this->result = FALSE;
            }
        } else {
            log_message($this->log, __METHOD__, 'No data to generate poster.');
        }
    }

}

?>