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

require_once $root . '/private/obs_language.php';

/**
 * Class to retrieve Destination languages and display them in a select element
 */
class syntax_plugin_door43obs_DestinationLanguages extends DokuWiki_Syntax_Plugin {

    /**
     * @return string Syntax mode type
     */
    public function getType() {
        return 'substition';
    }

    /**
     * @return string Paragraph type
     */
    public function getPType() {
        return 'normal';
    }

    /**
     * @return int Sort order - Low numbers go before high numbers
     */
    public function getSort() {
        return 901;
    }

    /**
     * Connect lookup pattern to lexer.
     *
     * @param string $mode Parser mode
     */
    public function connectTo($mode) {
        $this->Lexer->addSpecialPattern('\[obsdestinationlang\]', $mode, 'plugin_door43obs_DestinationLanguages');
    }

    /**
     * Handle matches of the door43obs syntax
     *
     * @param string       $match   The match of the syntax
     * @param int          $state   The state of the handler
     * @param int          $pos     The position in the document
     * @param Doku_Handler $handler The handler
     * @return array Data for the renderer
     */
    public function handle($match, $state, $pos, Doku_Handler &$handler) {

        $data = array();

        if ($state != DOKU_LEXER_SPECIAL)
            return $data;

        $http = new DokuHTTPClient();

        // Get the list of source languages that are level 3.
        $url      = 'https://api.unfoldingword.org/obs/txt/1/langnames.json';
        $response = $http->get($url);
        if ($response !== false) {

            // Convert to ObsLanguage.
            // Example of data received:
            // {
            //  "cc": ["GB"],
            //  "lc": "en",
            //  "ln": "English",
            //  "lr": "Europe"
            // }
            $languages = json_decode($response);
            foreach($languages as $lang) {
                $data[] = new ObsLanguage($lang->lc, $lang->ln);
            }
        }

        ObsLanguage::sort($data);

        return $data;
    }

    /**
     * Render xhtml output or metadata
     *
     * @param string        $mode     Renderer mode (supported modes: xhtml)
     * @param Doku_Renderer $renderer The renderer
     * @param array         $data     The data from the handler() function
     * @return bool If rendering was successful.
     */
    public function render($mode, Doku_Renderer &$renderer, $data) {

        if($mode != 'xhtml') return false;

        $elem = "<label for=\"selectObsDestination\">{$this->getLang('destinationLabel')}</label>&nbsp;<select id=\"selectObsDestination\">\n";

        $elem .= "<option>{$this->getLang('selectOne')}</option>\n";

        /* @var ObsLanguage $lang */
        foreach($data as $lang) {
            $elem .= "<option value=\"{$lang->isoCode}\">{$lang->name}</option>\n";
        }

        $elem .= "</select>\n";

        $renderer->doc .= $elem;

        return true;
    }
}

// vim:ts=4:sw=4:et:
