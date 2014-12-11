<?php
/**
 * Name: CreateNow.php
 * Description:
 *
 * Created by PhpStorm.
 *
 * Author: Phil Hopper
 * Date:   12/10/14
 * Time:   8:29 AM
 */

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

$root = dirname(dirname(__FILE__));
require_once $root . '/private/plugin_base.php';

/**
 * Class to retrieve Destination languages and display them in a select element
 */
class syntax_plugin_door43obs_CreateNow extends Door43obs_Plugin_Base {

    function __construct() {
        parent::__construct('CreateNow', 'obscreatenow', 'button_obs_create.html', 'createButtonText');
    }
}
