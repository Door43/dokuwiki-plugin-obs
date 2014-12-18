<?php
/**
 * Name: SourceLanguages.php
 * Description: A Dokuwiki syntax plugin to display a dropdown box that allows the user to select a language. The source
 * languages comes from here: https://api.unfoldingword.org/obs/txt/1/obs-catalog.json
 *
 * Author: Phil Hopper
 * Date:   2014-12-10
 */

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

$root = dirname(dirname(__FILE__));
require_once $root . '/private/plugin_base.php';

/**
 * Class to retrieve source languages and display them in a select element
 */
class syntax_plugin_door43obs_SourceLanguages extends Door43obs_Plugin_Base {

    function __construct() {
        parent::__construct('SourceLanguages', 'obssourcelang', 'source_language.html', 'sourceLabel');
    }
}


