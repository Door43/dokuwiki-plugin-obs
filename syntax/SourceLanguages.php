<?php
/**
 * DokuWiki Plugin door43obs (Syntax Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Phil Hopper <phillip_hopper@wycliffeassociates.org>
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


