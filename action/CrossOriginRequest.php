<?php
/**
 * Name: CrossOriginRequest.php
 * Description: A Dokuwiki action plugin to perform cross-origin requests for the browser.
 *
 * Author: Phil Hopper
 * Date:   2014-12-10
 */

// must be run within Dokuwiki
if(!defined('DOKU_INC')) die();

class action_plugin_door43obs_CrossOriginRequest extends DokuWiki_Action_Plugin {

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
     * Retrieves the requested url from another server and sends it back to the caller.
     *
     * @param Doku_Event $event  event object by reference
     * @param mixed      $param  [the parameters passed as fifth argument to register_hook() when this
     *                           handler was registered]
     * @return void
     */
    public function handle_ajax_call_unknown(Doku_Event &$event, $param) {

        if ($event->data !== 'obs_cross_origin_json_request') return;

        //no other ajax call handlers needed
        $event->stopPropagation();
        $event->preventDefault();

        global $INPUT;

        // if no contentType was passed, use application/json as the default
        $contentType = $INPUT->str('contentType');
        if (empty($contentType)) $contentType = 'application/json';

        header('Content-Type: ' . $contentType);

        $http = new DokuHTTPClient();

        // Get the list of source languages that are level 3.
        $url = $INPUT->str('requestUrl');
        echo $http->get($url);
    }

}
