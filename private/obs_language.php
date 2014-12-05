<?php
/**
 * Name: obs_language.php
 * Description:
 *
 * Created by PhpStorm.
 *
 * Author: Phil Hopper
 * Date:   12/5/14
 * Time:   4:54 PM
 */ 
class ObsLanguage {

    /** @var string */
    public $isoCode;

    /** @var string */
    public $name;

    /** @var int */
    public $checkingLevel;

    function __construct($isoCode, $name, $level=1) {

        $this->isoCode = $isoCode;
        $this->name = $name;
        $this->checkingLevel = $level;
    }
}
