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
 * Class to retrieve source languages and display them in a select element
 */
class syntax_plugin_door43obs_SourceLanguages extends DokuWiki_Syntax_Plugin {

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
        return 902;
    }

    /**
     * Connect lookup pattern to lexer.
     *
     * @param string $mode Parser mode
     */
    public function connectTo($mode) {
        $this->Lexer->addSpecialPattern('\[obssourcelang\]', $mode, 'plugin_door43obs_SourceLanguages');
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
        $url      = 'https://api.unfoldingword.org/obs/txt/1/obs-catalog.json';
        $response = $http->get($url);
        if($response !== false) {

            // Convert to ObsLanguage.
            // Example of data received:
            // {
            //   "date_modified": "20141205",
            //   "direction": "ltr",
            //   "language": "en",
            //   "status": {
            //              ...
            //              "checking_level": "3",
            //              ...
            //             },
            //   "string": "English"
            // }
            $languages = json_decode($response);
            foreach($languages as $lang) {

                if($lang->status->checking_level == '3') {
                    $data[] = new ObsLanguage($lang->language, $lang->string, 3);
                }
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
     * @param ObsLanguage[] $data     The data from the handler() function
     * @return bool If rendering was successful.
     */
    public function render($mode, Doku_Renderer &$renderer, $data) {

        if($mode != 'xhtml') return false;

        $elem = "<label for=\"selectObsSource\">{$this->getLang('sourceLabel')}</label>&nbsp;<select id=\"selectObsSource\">\n";

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
