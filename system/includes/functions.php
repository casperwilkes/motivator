<?php

/**
 * @author Casper Wilkes <casper@casperwilkes.net>
 * 
 * Collection of functions useful around the site.
 */

/**
 * Lazy Loader.
 * @param string $class_name
 */
function __autoload($class_name) {
    // Splits class name by '_' //
    $loader = array();
    @list($loader['file_name'], $loader['suffix']) = explode('_', $class_name, 2);
    switch (strtolower($loader['suffix'])) {
        case(''):
            $dir = 'includes';
            break;
        case('controller'):
            $dir = 'controllers';
            break;
        default :
            $dir = 'models';
            break;
    }

    $file_path = SYSTEM . DS . $dir . DS . strtolower($loader['file_name']) . '.php';

    if (file_exists($file_path)) {
        include_once $file_path;
    } else {
        see_var(realpath($file_path), 'realpath');
        see_var($file_path, 'Requested Path');
        die('The requested path could not be loaded.');
    }
}

/**
 * Redirects browser.
 * @param string $location
 */
function redirect($location) {
    header("Location: $location");
    exit;
}

/**
 * checks and returns allowable extensions. 
 * Used for validating and serving images.
 * @param string $image
 * @return string|boolean Either type or false
 */
function check_ext($image) {
    // Array of allowable formats //
    $allowed_ext = array('gif', 'png', 'jpg', 'jpeg');
    // Last element count //
    $last_element = 0;
    // Lowercase the extension //
    $im = strtolower($image);
    if (!is_null($im) && strlen($im)) {
        // Turn image name into an array //
        if (strpos($im, '/')) {
            $name = explode('/', $im);
        } else {
            $name = explode('.', $im);
        }
        // Get the last element in array //
        $last_element = count($name) - 1;
        // Compares format against allowable formats //
        if (in_array($name[$last_element], $allowed_ext)) {
            return $name[$last_element];
        }
        return FALSE;
    }
    return FALSE;
}

/**
 * Creates a logfile for errors.
 * 
 * @param string $log Name of log file
 * @param string $action Method or cause of error
 * @param string $message Error message
 */
function log_message($log = '', $action = '', $message = '') {
    // Logging directory //
    $logs_dir = 'logs';
    // Path to directory //
    $log_path = SITE_ROOT . DS . $logs_dir;
    // filepath for log file //
    if (strlen($log)) {
        // If using __CLASS__ for log name //
        $log = strpos($log, '_') ? strstr($log, '_', TRUE) : $log;
        $logfile = $log_path . DS . strtolower($log) . '.txt';
    } else {
        // If log name was not specified //
        $logfile = $log_path . DS . 'General.txt';
    }
    // Set directory and log permissions //
    $permission = 0755;
    // Test file before writing //
    $new = file_exists($logfile) ? false : true;
    if (!is_dir($log_path)) {
        mkdir($log_path, $permission);
    }
    // If action or method wasn't specified //
    if (!strlen($action)) {
        $action = 'An unspecified error has occured';
    }
    // opens file if exists, if not creates giving appropriate information //
    // Open in Append mode //
    $handle = fopen($logfile, 'a');
    if ($handle) {
        // sets time //
        $timestamp = strftime("%m/%d/%Y %H:%M:%S", time());
        // Log line //
        $content = "{$timestamp} || {$action} || {$message}\n";
        // Write to file //
        fwrite($handle, $content);
        // Close file //
        fclose($handle);
        // change permissions on *nix //
        if ($new) {
            chmod($logfile, $permission);
        }
    } else {
        echo 'Could not open log file for writing.';
    }
}

?>