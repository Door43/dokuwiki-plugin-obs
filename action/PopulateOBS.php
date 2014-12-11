<?php
/**
 * DokuWiki Plugin door43obs (Action Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Phil Hopper <phillip_hopper@wycliffeassociates.org>
 */

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

class action_plugin_door43obs_PopulateOBS extends DokuWiki_Action_Plugin {

    /**
     * Registers a callback function for a given event
     *
     * @param Doku_Event_Handler $controller DokuWiki's event controller object
     * @return void
     */
    public function register(Doku_Event_Handler $controller) {
        $controller->register_hook('AJAX_CALL_UNKNOWN', 'BEFORE', $this, 'handle_ajax_call_unknown');
    }

    /**
     * [Custom event handler which performs action]
     *
     * @param Doku_Event $event  event object by reference
     * @param mixed      $param  [the parameters passed as fifth argument to register_hook() when this
     *                           handler was registered]
     * @return void
     */

    public function handle_ajax_call_unknown(Doku_Event &$event, $param) {

        if ($event->data !== 'create_obs_now') return;

        //no other ajax call handlers needed
        $event->stopPropagation();
        $event->preventDefault();

        global $INPUT;

        header('Content-Type: text/plain');
        echo($INPUT->str('sourceLang') . "\n");
        echo($INPUT->str('destinationLang') . "\n");
    }

}

//$old_path = getcwd();
//chdir('/var/www/vhosts/door43.org/tools/obs/dokuwiki/');
//$output = shell_exec('./obs-creator.sh -l isoCode --src isoCode [--notes]');
//chdir($old_path);
