<?php
/**
 * Name: DestinationLanguages.php
 * Description: A Dokuwiki syntax plugin to display a text box that allows the user to look up and select a language. The
 * list of languages comes from here: http://door43.org:9096/?q=
 *
 * Author: Phil Hopper
 * Date:   2014-12-10
 */

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

$root = dirname(dirname(__FILE__));
require_once $root . '/private/plugin_base.php';

/**
 * Class to retrieve Destination languages and display them in a text box element
 */
class syntax_plugin_door43obs_DestinationLanguages extends Door43obs_Plugin_Base {

    function __construct() {
        parent::__construct('DestinationLanguages', 'obsdestinationlang', 'destination_language.html', 'destinationLabel');
    }
}
