<?php

class Main_Model {

    /**
     * Used for relative links to images directory.
     * @var string
     */
    private $poster_loc = 'images/posters/';

    /**
     * Directory where images are stored.
     * @var string
     */
    private $images_dir;

    /**
     * Collected images from image directory.
     * @var array
     */
    private $images = array();

    public function __construct() {
        $this->images_dir = IMAGE_DIR . DS . 'posters';
    }

    /**
     * Returns an array of images if found in directory.
     * @return array
     */
    public function get_images() {
        // Check if images directory is empty, if not, collect the images.
        if ($this->check_images_dir()) {
            $this->collect_images();
        }
        return $this->images;
    }

    /**
     * Check whether image directory exists.
     * @return boolean
     */
    private function check_images_dir() {
        return is_dir($this->images_dir);
    }

    /**
     * Collects the images from the image directory and puts them into an array.
     * @return array
     */
    private function collect_images() {
        $images = new DirectoryIterator($this->images_dir);
        foreach ($images as $image) {
            if (!$image->isDot()) {
                $name = $image->getBasename();
                $this->images[] = array(
                    'path' => $this->poster_loc . DS . $name,
                    'name' => $name
                );
            }
        }
        return $this->images;
    }

}

?>