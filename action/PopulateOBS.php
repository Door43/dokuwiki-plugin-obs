<?php
/**
 * DokuWiki Plugin door43obs (Action Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Phil Hopper <phillip_hopper@wycliffeassociates.org>
 */

// must be run within Dokuwiki
if (!defined('DOKU_INC')) die();
if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

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
    public function handle_ajax_call_unknown(Doku_Event &$event,
        /** @noinspection PhpUnusedParameterInspection */ $param) {

        if ($event->data !== 'create_obs_now') return;

        //no other ajax call handlers needed
        $event->stopPropagation();
        $event->preventDefault();


        $this->initialize_obs_content();
    }

    private function initialize_obs_content() {

        global $conf;
        global $INPUT;

        header('Content-Type: text/plain');

        // Get the iso codes for the source and destination languages.
        $srcIso = $INPUT->str('sourceLang');
        $dstIso = $this->get_iso_from_language_name_string($INPUT->str('destinationLang'));

        // Check if the destination namespace exists.
        // If not, create it.
        $pagesDir = $conf['datadir'];
        $dstNamespaceDir = $pagesDir . DS . $dstIso;
        if (!$this->check_namespace($dstNamespaceDir, $dstIso)) {
            echo sprintf($this->get_error_message('obsNamespaceNotFound'), $dstIso);
            return;
        }

        // Check if the source obs directory exists.
        // If not, report an error.
        $srcDir = $pagesDir . DS . $srcIso . DS . 'obs';
        if (!is_dir($srcDir)) {
            echo sprintf($this->get_error_message('obsSourceDirNotFound'), $srcIso);
            return;
        }

        // Check if the destination obs directory already exists.
        $dstDir = $dstNamespaceDir . DS . 'obs';
        if (is_dir($dstDir)) {

            // If the directory exists, are there txt files in it?
            // If there are, report an error.
            $files = glob($dstDir . DS . '*.txt', GLOB_NOSORT);
            if (!empty($files) && (count($files) > 5)) {
                echo sprintf($this->get_error_message('obsDestinationDirExists'), $dstIso);
                return;
            }
        }

        // Now copy the obs files from $srcDir to $dstDir
        $this->copy_obs_files($srcDir, $dstDir, $srcIso, $dstIso);

        // TODO: Should we copy the notes also?
    }

    private function get_error_message($langStringKey) {
        return '<span style="color: #990000;">' . $this->getLang($langStringKey) . '</span><br>';
    }

    private function get_success_message($langStringKey) {
        return '<span style="color: #009900;">' . $this->getLang($langStringKey) . '</span><br>';
    }

    private function get_iso_from_language_name_string($languageName) {

        // extract iso code from the destination language field, i.e.: "English (en)"
        $pattern = '/\([^\(\)]+\)$/';
        $matches = array();
        if (preg_match($pattern, $languageName, $matches) === 1)
            return preg_replace('/\(|\)/', '', $matches[0]);

        // if no matches, hopefully $languageName is the iso
        return $languageName;
    }

    /**
     * Check if a namespace exists, and creates it if it does not.
     * @param $namespaceDir
     * @param $langIso
     * @return bool
     */
    private function check_namespace($namespaceDir, $langIso) {

        if (is_dir($namespaceDir)) return true;

        // create the directory
        mkdir($namespaceDir, 0777);

        // create default files
        $files = array('home.txt', 'sidebar.txt');
        $fileDir = dirname(dirname(__FILE__)) . '/private/namespace/';

        foreach($files as $file) {

            $txt = file_get_contents($fileDir . $file);
            $fileName = $namespaceDir . DS . $file;
            file_put_contents($fileName, str_replace('LANGCODE', $langIso, $txt));
            chmod($fileName, 0777);
        }

        // initialize a github repo for this language
        if (strpos($namespaceDir, '/var/www/vhosts/door43.org/') !== false) {

            $gitInit = '/var/www/vhosts/door43.org/tools/obs/dokuwiki/d43-git-init.py';
            if (is_file($gitInit)) {
                shell_exec($gitInit . ' ' . $langIso);
            }
        }

        echo sprintf($this->get_success_message('obsCreatedNamespace'), $langIso);
        return true;
    }

    private function copy_obs_files($srcDir, $dstDir, $srcIso, $dstIso) {

        if (!is_dir($dstDir))
            mkdir($dstDir, 0777);

        // Copy the 01.txt through 50.txt source files.
        // Do nothing besides copying.
        for ($i = 1; $i < 51; $i++) {

            $file = sprintf('%02d', $i) . '.txt';
            $srcFile = $srcDir . DS . $file;

            if (!is_file($srcFile)) continue;

            $outFile = $dstDir . DS . $file;
            copy($srcFile, $outFile);
            chmod($outFile, 0777);
        }

        // Copy the 01-something.txt through 50-something.txt source files.
        // Replace the $srcIso with $dstIso.
        for ($i = 1; $i < 51; $i++) {

            $pattern = $srcDir . DS . sprintf('%02d', $i) . '-*.txt';
            foreach (glob($pattern) as $file) {

                if (!is_file($file)) continue;

                $text = file_get_contents($file);
                $outFile = $dstDir . DS . basename($file);
                file_put_contents($outFile, $this->replace_iso_in_text($text, $srcIso, $dstIso));
                chmod($outFile, 0777);
            }
        }

        // Copy remaining files.
        // Replace the $srcIso with $dstIso.
        $files = array('app_words.txt', 'back-matter.txt', 'front-matter.txt', 'sidebar.txt', 'stories.txt');
        foreach($files as $file) {

            $srcFile = $srcDir . DS . $file;
            if (!is_file($srcFile)) continue;

            $text = file_get_contents($srcFile);
            $outFile = $dstDir . DS . $file;
            file_put_contents($outFile, $this->replace_iso_in_text($text, $srcIso, $dstIso));
            chmod($outFile, 0777);
        }

        // Create the obs.txt home page.
        $obsFile = dirname(dirname(__FILE__)) . '/private/namespace/obs.txt';
        $outFile = dirname($dstDir) . DS . 'obs.txt';

        $text = file_get_contents($obsFile);
        file_put_contents($outFile, str_replace('LANGCODE', $dstIso, $text));
        chmod($outFile, 0777);

        echo sprintf($this->get_success_message('obsCreatedSuccess'), $dstIso, "/$dstIso/obs");
    }

    private function replace_iso_in_text($text, $srcIso, $dstIso) {

        $pattern = '/(?<=[\[:>\s])' . $srcIso . '(?=:obs)/';
        return preg_replace($pattern, $dstIso, $text);
    }
}


