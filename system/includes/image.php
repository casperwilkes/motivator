<?php

/**
 * @author Casper Wilkes <casper@casperwilkes.net>
 * 
 * This class controls all of the information for images uploaded to the 
 * application. The user supplies the title, message, and the file. The class
 * also moves the file and reports errors while uploading the file.
 */
class Image {

    /**
     * The title text.
     * @var string
     */
    private $title;

    /**
     * The message text.
     * @var string
     */
    private $message;

    /**
     * The name of the image file.
     * @var string
     */
    private $name;

    /**
     * The type of image uploaded.
     * @var string
     */
    private $type;

    /**
     * The temp path of the image.
     * @var string
     */
    private $temp;

    /**
     * The size of the upload provided by the $_FILES array.
     * @var int
     */
    private $size;

    /**
     * The final path to the image.
     * @var string
     */
    private $image_path;

    /**
     * The directory where images are stored
     * @var string
     */
    private $image_dir = '';

    /**
     * The error code.
     * Default to zero / no error.
     * @var int
     */
    private $error = 0;

    /**
     * Maps the error codes to corresponding message. Used for logging 
     * upload errors.
     * @var array
     */
    private $error_map = array(
        'No errors.',
        'Larger than upload_max_filesize.',
        'Larger than form MAX_FILE_SIZE.',
        'Partial upload file.',
        'No file uploaded.',
        'No temporary directory.',
        'Can\'t write to disk.',
        'File upload stopped by extension.',
    );

    /**
     * The log used for logging errors.
     * @var string
     */
    private $log = 'image';

    public function __construct() {
        $this->image_dir = IMAGE_DIR;
    }

    /**
     * Gets the title text.
     * @return string 
     */
    public function get_title() {
        return $this->title;
    }

    /**
     * Gets the message text.
     * @return string 
     */
    public function get_message() {
        return $this->message;
    }

    /**
     * Gets the image path.
     * @return string 
     */
    public function get_image_path() {
        return $this->image_path;
    }

    /**
     * Gets the image name. 
     * @return string
     */
    public function get_name() {
        return $this->name;
    }

    /**
     * Gets the error code.
     * @return int
     */
    public function get_error() {
        return $this->error;
    }

    /**
     * Gets the type of file.
     * @return string
     */
    public function get_type() {
        return $this->type;
    }

    /**
     * Sets the title text.
     * @param string $title
     * @return boolean
     */
    public function set_title($title) {
        if (strlen($title)) {
            $this->title = trim($title);
            return TRUE;
        } else {
            $this->title = '';
            return FALSE;
        }
    }

    /**
     * Sets the message text.
     * @param string $message
     * @return boolean 
     */
    public function set_message($message) {
        if (strlen($message)) {
            $this->message = trim($message);
            return TRUE;
        } else {
            $this->message = '';
            return FALSE;
        }
    }

    /**
     * Sets the image path and changes the name of the file.
     * @return string
     */
    public function set_image_path() {
        // Set processing / poster paths //
        $process_dir = $this->image_dir . DS . 'temp';
        $poster_dir = $this->image_dir . DS . 'posters';
        // Create processing directory //
        if (!is_dir($process_dir)) {
            if (!mkdir($process_dir, 0755)) {
                log_message($this->log, __METHOD__, 'Could not create directory: ' . $process_dir);
                die("Could not access the necessary directory.");
            }
        }
        // Create poster directory //
        if (!is_dir($poster_dir)) {
            if (!mkdir($poster_dir, 0755)) {
                log_message($this->log, __METHOD__, 'Could not create directory: ' . $poster_dir);
                die('Could not access the necessary directory.');
            }
        }
        if (!$this->error) {
            $this->image_path = $process_dir . DS . $this->new_name();
            return TRUE;
        }
        return FALSE;
    }

    /**
     * This method transfers all of the information from the
     * $_FILES array into this class.
     * @param array $array The $_FILES array
     * @return boolean
     */
    public function file_info($array) {
        if (!$array || empty($array) || !is_array($array) || !$this->has_error($array)) {
            return FALSE;
        } else {
            $this->name = $array['name'];
            $this->type = $array['type'];
            $this->temp = $array['tmp_name'];
            $this->size = $array['size'];
            $this->set_image_path();
            return TRUE;
        }
    }

    /**
     * Moves the file from temporary location to the final location. 
     * @return boolean
     */
    public function move_file() {
        $move = move_uploaded_file($this->temp, $this->image_path);
        if (!$move) {
            log_message($this->log, __METHOD__, 'Could not move file: ' . $this->temp);
        }
        return $move;
    }

    /**
     * Renames the file to a random to keep from over writing files.
     * @return string 
     */
    private function new_name() {
        $new_base = substr(md5(uniqid()), 0, 15); // new hash
        $file_ext = substr($this->name, strripos($this->name, '.')); // strip extension
        $new_name = $new_base . $file_ext;  // combine both for new name
        return $new_name;
    }

    /**
     * Returns whether an error has occured while uploading the image and sets
     * the $error property if so.
     * @param array The $_FILES array
     * @return boolean 
     */
    private function has_error($files) {
        if ($files['error'] == 0) {
            return TRUE;
        } else {
            $this->error = $files['error'];
            log_message($this->log, __METHOD__, 'Error uploading image: ' . $this->error_map[$this->error]);
            return FALSE;
        }
    }

}

?>