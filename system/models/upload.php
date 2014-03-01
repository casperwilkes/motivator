<?php

class Upload_Model {

    /**
     * Sets the max file size to 2mb for the form, php's default.
     * @var int
     */
    private $max_file_size = 2097152;

    /**
     * Used to make the guide list items an array so we can modify as needed.
     * @var array
     */
    private $guide_array = array(
        'You must upload a file',
        'The Motivator only accepts images',
        'File must be less than 2MB',
        'Title text must be included',
        'Message text is not needed, but should still be included for a good poster',
        'Unlike most poster creators, The Motivator builds a poster around your image, 
            so choose a decent image size',
        'Once the poster has been created, it is advised to save it using the 
            download button to prevent losing changes',
        'Animated images will take longer to process please be patient'
    );

    /**
     * Image image data.
     * @var Image
     */
    private $image;

    /**
     * The file information from the file upload. $_FILES.
     * @var array
     */
    private $file_info;

    /**
     * Any errors that may occur from the upload process.
     * @var array
     */
    private $error = array();

    /**
     * The log file used for error catching.
     * @var string
     */
    private $log = 'upload';

    /**
     * Returns the max file size.
     * @return int
     */
    public function get_max_file_size() {
        return $this->max_file_size;
    }

    /**
     * Returns the guide array.
     * @return array
     */
    public function get_guide_array() {
        return $this->guide_array;
    }

    /**
     * Collects the $_FILES info.
     * @param array $file_info
     * @return array
     */
    public function gather_file_info($file_info) {
        if (is_array($file_info)) {
            $this->file_info = $file_info;
            return $this->file_info;
        }
        // Parse the array into a string for logging //
        $string_convert = str_replace(array('&', '%2F', '%3A', '%5C'), array(', ', '/', ':', '\\'), http_build_query($file_info));
        // Log the file info //
        log_message($this->log, __METHOD__, 'could not gather file information: ' . $string_convert);
        $this->file_info = null;
    }

    /**
     * Gathers all of the poster data from the file upload and transfers 
     * the user to the poster generator portion of the application.
     * @param array $post_data $_POST data from file submission.
     * @param Session $session $_SESSION data from Session class
     * @return array JSON encoded error array
     */
    public function gather_poster_data($post_data, $session) {
        // If form was submitted //
        if (isset($post_data)) {
            // Initialize Image class //
            $this->image = new Image();
            // Set message text //
            $this->image->set_message($post_data['message_text']);
            // Set file info for image //
            $this->image->file_info($this->file_info);
            // Set the title text for the image //
            $title = $this->image->set_title($post_data['title_text']);
            // Use ternary because function returns type if valid //
            $check_ext = (check_ext($this->image->get_type())) ? TRUE : FALSE;
            // Check that file is of correct type //
            if (!$check_ext) {
                $this->error[] = $this->guide_array[1];
            }
            // Checks that title was set //
            if (!$title) {
                $this->error[] = $this->guide_array[3];
            }
            // Checks for other image errors //
            $this->assign_errors();
            // If no error uploading and poster was able to be moved //
            if (($this->image->get_error() == 0) && $check_ext && $title) {
                if ($this->image->move_file()) {
                    // Put object into session //
                    $session->set_image($this->image);
                    // Redirect users //
                    redirect('index.php?poster');
                } else {
                    // The file could not be moved //
                    log_message($this->log, __METHOD__, 'Failed to move file:' . $this->image->get_name());
                    $this->error[] = $this->guide_array[] = 'Could not move file.';
                }
            } else {
                $this->error[] = $this->guide_array[] = 'There was a problem processing your submission. Please try again or use another image.';
            }
            // Encode the errors to send them to the JS at bottom of upload.php for use //
            return json_encode($this->error);
        }
    }

    /**
     * Assigns any image errors to guide array.
     */
    private function assign_errors() {
        // Check that file has no errors //
        if ($this->image->get_error() != 0) {
            switch ($this->image->get_error()) {
                case(1): // UPLOAD_ERR_INI_SIZE //
                    $this->error[] = $this->guide_array[2];
                    break;
                case(2): // UPLOAD_ERR_FORM_SIZE //
                    $this->error[] = $this->guide_array[2];
                    break;
                case(4); // UPLOAD_ERR_NO_FILE //
                    $this->error[] = $this->guide_array[0];
                    break;
                default:
                    // Send unknown error, add string to guide array so it outputs //
                    $this->error[] = $this->guide_array[] = 'An unknown error has occured.';
            }
        }
    }

}

?>