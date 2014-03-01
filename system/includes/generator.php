<?php

/**
 * @author Casper Wilkes <casper@casperwilkes.net>
 * 
 * This file processes and generates a poster based on specified params from 
 * the input page provided by the user. All settings are clearly marked if 
 * adjustments are needed. If the image is unable to process for some reason,
 * the reason will be logged and the output page will give the user a generic
 * error message.
 * 
 * NOTE: Requires Image Magick version 6 and higher and imagick extension.
 * Image Magick: http://imagemagick.com/script/index.php
 * Imagick: http://pecl.php.net/package/imagick
 */
class Generator {

    /**
     * The image data to create the motivational poster
     * @var Image
     */
    private $mot;

    /**
     * The path to the image.
     * @var string
     */
    private $image_path;

    /**
     * The destination path the image will be saved to.
     * @var string
     */
    private $destination;

    /**
     * The result of whether the image processed correctly.
     * @var boolean
     */
    private $result;

    /**
     * The text that the watermark will use.
     * @var string
     */
    private $watermark_text = 'The Motivator';

    /**
     * The name of the True Type Font that the title will use.
     * @var string
     */
    private $title_font = 'Times.TTF';

    /**
     * The name of the True Type Font that the message will use.
     * @var string
     */
    private $message_font = 'century gothic.TTF';

    /**
     * The error log.
     * @var string
     */
    private $log = 'generator';

    /**
     * Returns the result of the poster creation.
     * @return boolean
     */
    public function get_result() {
        return $this->result;
    }

    /**
     * Establishes the image data that will be used when creating the
     * poster.
     * @param Image $image
     */
    public function establish_image($image) {
        if (is_a($image, 'Image')) {
            $this->mot = $image;
            $this->set_props();
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Generates and saves the poster if all requirements are met.
     */
    public function generate_poster() {
        try {
            /**
             * Original image 
             * */
            $src_im = new Imagick($this->image_path);
            // Width & height //
            // Flatten images in case of gif so that geometry can be gotten accurately //
            $set_size = $src_im->flattenimages();
            $src_im_geo = $set_size->getimagegeometry();
            $src_im_width = $src_im_geo['width'] + 4;
            $src_im_height = $src_im_geo['height'] + 4;
            // Gets the delay on animated gifs //
            $delay = $src_im->getimagedelay();
            // Get image extension //
            $ext = $src_im->getImageFormat();

            /**
             * Motivational title text 
             * */
            $mt = new ImagickDraw();
            // Set font for title text //
            $mt->setFont(FONTS_DIR . DS . $this->title_font);
            $mt->setFillColor('white');
            // Text placement //
            $mt->setGravity(IMAGICK::GRAVITY_NORTH);
            // Letter spacing //
            $mt->setTextKerning(6);
            // capitalize text //
            $mt_text = strtoupper($this->mot->get_title());
            // set initial font size //
            $mt_text_font_size = 1;
            $mt->setFontSize($mt_text_font_size);
            // Get font metrics for textWidth and textHeight //
            $mt_text_met = $src_im->queryFontMetrics($mt, $mt_text);

            // Increase font to fit width of the image //
            while ($mt_text_met['textWidth'] < $src_im_width + 50) {
                ++$mt_text_font_size;
                $mt->setFontSize($mt_text_font_size);
                $mt_text_met = $src_im->queryFontMetrics($mt, $mt_text);
            }
            // Decrease font to under half the size of the image //
            while ($mt_text_met['textHeight'] > $src_im_height * .25) {
                --$mt_text_font_size;
                $mt->setFontSize($mt_text_font_size);
                $mt_text_met = $src_im->queryFontMetrics($mt, $mt_text);
            }

            /**
             * Motivational message text
             * */
            $mm = new ImagickDraw();
            $mm->setFont(FONTS_DIR . DS . $this->message_font);
            // Set initial font size to 3/4 the size of the title text //
            $mm_text_font_size = ($mt_text_font_size * .75);
            $mm->setFontSize($mm_text_font_size);
            $mm->setFillColor('white');
            // Text placement //
            $mm->setGravity(IMAGICK::GRAVITY_NORTH);
            // Letter spacing //
            $mm->setTextKerning(1);
            $mm_text = strtolower($this->mot->get_message());
            // Get font metrics for textWidth and textHeight //
            $mm_text_met = $src_im->queryFontMetrics($mm, $mm_text);

            /**
             * Checks to see if message statement is set
             * if not continues on, but leaves space
             * to keep balance and consistent symmetry. 
             * */
            if (strlen($mm_text)) {
                if ($mm_text_met['textWidth'] >= ($src_im_width * .75)) {
                    // Lowers font size if message length is longer than image width //
                    while ($mm_text_met['textWidth'] >= ($src_im_width * .75)) {
                        --$mm_text_font_size;
                        $mm->setFontSize($mm_text_font_size);
                        $mm_text_met = $src_im->queryFontMetrics($mm, $mm_text);
                    }
                } else {
                    // Raises font size if message width is shorter than image width //
                    while ($mm_text_met['textWidth'] <= ($src_im_width * .85)) {
                        ++$mm_text_font_size;
                        $mm->setFontSize($mm_text_font_size);
                        $mm_text_met = $src_im->queryFontMetrics($mm, $mm_text);
                    }
                }
                // Checks to make sure message character height is shorter than title text //
                if ($mm_text_met['characterHeight'] >= ($mt_text_met['characterHeight'] * .8)) {
                    while ($mm_text_met['characterHeight'] >= ($mt_text_met['characterHeight'] * .8)) {
                        // if it is, lower font size to appropriate level //
                        --$mm_text_font_size;
                        $mm->setFontSize($mm_text_font_size);
                        $mm_text_met = $src_im->queryFontMetrics($mm, $mm_text);
                    }
                }

                // Redundency size check //
                // Checks again to make sure that text width is still shorter than image, //
                // if not readjusts //
                while ($mm_text_met['textWidth'] >= ($src_im_width * .85)) {
                    --$mm_text_font_size;
                    $mm->setFontSize($mm_text_font_size);
                    $mm_text_met = $src_im->queryFontMetrics($mm, $mm_text);
                }
            }

            /**
             * Poster background
             * */
            $post = new Imagick();
            // Sets the image format //
            $post->setFormat($ext);
            $post_color = 'black';
            $post_width = $src_im_width + 100;
            // If message account for space, calculate size //
            if (strlen($mm_text)) {
                $post_height = $src_im_height + ((($mt_text_met['characterHeight'] * 2) + ($mm_text_met['characterHeight'] * 2)) / 2 + 15) + (($this->half($post_width) - $this->half($src_im_width)) * 2);
            } else {
                $post_height = $src_im_height + (($mt_text_met['characterHeight'] * 2) / 2 + 15) + (($this->half($post_width) - $this->half($src_im_width)) * 2);
            }

            /**
             * Watermark
             * */
            $wmark = new ImagickDraw();
            $wmark->setFont(FONTS_DIR . DS . $this->title_font);
            // Padding to put watermark in //
            $c_padd = $post_height * .03;
            // Sets initial font size //
            $wmark_font_size = 1;
            $wmark->setFontSize($wmark_font_size);
            $wmark->setFillColor('white');
            $wmark->setTextKerning(1);
            // Watermark placement //
            $wmark->setGravity(IMAGICK::GRAVITY_SOUTHEAST);
            // Font metrics //
            $wmark_font_met = $post->queryFontMetrics($wmark, $this->watermark_text);
            // Sets font size for dynamic watermark //
            while ($wmark_font_met['characterHeight'] <= ($c_padd - 5)) {
                ++$wmark_font_size;
                $wmark->setFontSize($wmark_font_size);
                $wmark_font_met = $post->queryFontMetrics($wmark, $this->watermark_text);
            }

            /**
             * X and Y cords for image alignments.
             */
            $src_im_x = ($this->half($post_width) - $this->half($src_im_width));
            $src_im_y = $src_im_x / 1.5;
            // x and y cords for title text //
            $mt_x = 0;
            $mt_y = $src_im_height + $src_im_y + 10;
            // x and y cords for statement text //
            $mm_x = 0;
            $mm_y = $src_im_height + $src_im_y + $mt_text_met['characterHeight'] + 10;

            /**
             * Combine all components to the poster together.
             * */
            // seperates gif images into multiple frames //
            foreach ($src_im->coalesceimages() as $frames) {
                // Border image here to prevent skipping //
                $frames->borderimage('white', 2, 2);
                // Create a new background //
                $post->newImage($post_width, $post_height, $post_color, $ext);
                // Add images to background //
                $post->compositeImage($frames, imagick::COMPOSITE_OVER, $src_im_x, $src_im_y);
                // Add title text //
                $post->annotateImage($mt, $mt_x, $mt_y, 0, $mt_text);
                // Add message text //
                $post->annotateImage($mm, $mm_x, $mm_y, 0, $mm_text);
                // Add watermark //
                $post->annotateImage($wmark, 4, 1, 0, $this->watermark_text);
                // Set the image display on animated gifs //
                $post->setimagedelay($delay);
            }

            // Write it to the dest folder//
            // If save not successful, original file can be found in temp dir //
            $result = $post->writeImages($this->destination, TRUE);

            $this->result = is_bool($result) ? TRUE : FALSE;

            /**
             * Destroy all of the resources and free up memory
             * */
            // clear all resources //
            $src_im->clear();
            $mt->clear();
            $mm->clear();
            $wmark->clear();
            $post->clear();
            // destroys all resources //
            $src_im->destroy();
            $mt->destroy();
            $mm->destroy();
            $wmark->destroy();
            $post->destroy();

            // Destroy the original image, no longer needed //
            if ($this->result) {
                unlink($this->mot->get_image_path());
            }
        } catch (Exception $e) {
            // log any errors //
            log_message($this->log, __METHOD__, 'LINE: ' . $e->getLine() . ' | ' . $e->getMessage());
        }
    }

    /**
     * Sets related properties that will be used for creating the Motivational
     * poster.
     */
    private function set_props() {
        $this->image_path = $this->mot->get_image_path();
        $this->destination = IMAGE_DIR . DS . 'posters' . DS . basename($this->image_path);
    }

    /**
     * Cleaner method of dividing digits. Used to center properties.
     * @param int $digit Number to calculate
     * @return int Calculated Number
     */
    private function half($digit) {
        return $digit / 2;
    }

}

?>