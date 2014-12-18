<?php
/**
 * Name: CreateNow.php
 * Description: A Dokuwiki syntax plugin to display a button the user can click to initialize OBS in another language.
 *
 * Author: Phil Hopper
 * Date:   2014-12-10
 */

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

$root = dirname(dirname(__FILE__));
require_once $root . '/private/plugin_base.php';

/**
 * Class to display a button the user can click to initialize OBS in another language
 */
class syntax_plugin_door43obs_CreateNow extends Door43obs_Plugin_Base {

    function __construct() {
        parent::__construct('CreateNow', 'obscreatenow', 'button_obs_create.html', 'createButtonText');
    }
}
