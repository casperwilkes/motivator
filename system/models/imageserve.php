<?php

class Imageserve_Model {

    /**
     * Type of file to serve.
     * @var string
     */
    private $type;

    /**
     * Name of the file to serve.
     * @var string
     */
    private $name;

    /**
     * File path to the image being served.
     * @var string
     */
    private $file_path;

    /**
     * The image being requested for download.
     * @param string $request
     */
    public function get_request($request) {
        // Checks that image request is set //
        if (!isset($request)) {
            log_message(__CLASS__, __METHOD__, 'Request was empty');
            redirect('index.php');
        }
        // Sets the class properties //
        $this->set_properties($request);
        // Make sure file exists before trying to serve //
        if (!is_file($this->file_path)) {
            die('File does not exist.');
        }
    }

    /**
     * Serves the image to the browser.
     */
    public function serve_image() {
        // Set headers //
        $this->set_headers();
        // Read the file from disk //
        readfile($this->file_path);
    }

    /**
     * Sets the class properties.
     * @param string $file
     */
    private function set_properties($file) {
        $this->file_path = IMAGE_DIR . DS . 'posters' . DS . $file;
        $this->name = basename($this->file_path);
        $this->type = $this->type_check();
    }

    /**
     * Checks the extension type for header info. Returns the proper extension
     * for mimetype.
     * @return string
     */
    private function type_check() {
        $ext = check_ext(basename($this->name));
        // If the extension is jpg, turn to jpeg for mimetype //
        if ($ext == 'jpg' || $ext == 'JPG') {
            $type = 'jpeg';
        } else {
            $type = $ext;
        }
        return $type;
    }

    /**
     * Sets all of the header() info for the browser.
     */
    private function set_headers() {
        header("Pragma: no-cache");
        header("Content-Length: " . filesize($this->file_path));
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename={$this->name}");
        header("Content-Type: image/{$this->type}");
        header("Content-Transfer-Encoding: binary");
    }

}

?>