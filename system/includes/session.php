<?php

/**
 * @author Casper Wilkes <casper@casperwilkes.net>
 * 
 * This class contains the session data used to transfer the Image data from
 * the upload page to the generator page.
 */
class Session {

    /**
     * Used to transfer and maintain image data for poster creation.
     * @var Image
     */
    private $image;

    /**
     * The error log
     * @var string
     */
    private $log = 'session';

    public function __construct() {
        session_start();
        $this->check_image();
    }

    /**
     * Puts the image data into a session variable.
     * @param Image $image
     * @return boolean
     */
    public function set_image($image) {
        // Checks to make sure it's an Image type object. //
        if (is_a($image, 'Image')) {
            $this->image = $_SESSION['image'] = $image;
            return TRUE;
        }
        log_message($this->log, __METHOD__, 'Tried to pass a non image object: ' . $image);
        return FALSE;
    }

    /**
     * Returns the Image object, or null if empty.
     * If Image is set in session, it unsets it because it's no longer 
     * needed.
     * @return Image|null
     */
    public function get_image() {
        if (isset($_SESSION['image'])) {
            unset($_SESSION['image']);
        }
        return $this->image;
    }

    /**
     * Checks to ensure image object is still set in session variable.
     * @return Image|null
     */
    public function check_image() {
        return $this->image = isset($_SESSION['image']) ? $_SESSION['image'] : null;
    }

}

?>