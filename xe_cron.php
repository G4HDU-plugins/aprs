<?php
/*
 * e107 website system
 *
 * Copyright (C) 2001-2009 e107 Inc (e107.org)
 * Released under the terms and conditions of the
 * GNU General Public License (http://www.gnu.org/licenses/gpl.txt)
 *
 * Plugin configuration module - gsitemap
 *
 * $Source: /cvs_backup/e107_0.8/e107_plugins/pm/e_cron.php,v $
 * $Revision$
 * $Date$
 * $Author$
 *
*/
// require_once(e_BASE. "class2.php" );
// error_reporting(E_ALL);
if (!defined('e107_INIT')){
    // exit;
}

require(e_PLUGIN . "aprs/includes/aprs_class.php");
require(e_PLUGIN . "aprs/includes/aprs_cron_class.php");
// include_lan(e_PLUGIN.'/aprs/languages/English_aprs.php');
// require( e_PLUGIN."aprs/includes/aprs_coord_class.php" );
class aprs_cron extends aprs{ // include plugin-folder in the name.
    private $logRequirement = 1; // Flag to determine logging level
    private $debugLevel = 0; // Used for internal debugging
    private $logHandle = null;

    private $e107;
    private $mailManager;
    private $sql1;
    private $sql2;
    public function __construct(){
        $this->e107 = e107::getInstance();
        // $this->aprs = new aprs;
        $this->sql1 = new db;
        $this->sql2 = new db;
        $this->debugLevel = 2;
        $this->logLine('Called ', true, true);
    }

    /**
    * Cron configuration
    *
    * Defines one or more cron tasks to be performed
    *
    * @return array of task arrays
    */
    public function config(){
        $cron = array();
        $cron[] = array(
            'name' => 'APRS Beacon',
            'category' => 'plugin',
            'function' => 'Beacon',
            'description' => "Sends regular beacon transmissions"
            );
        $cron[] = array(
            'name' => 'APRS Clean Up',
            'category' => 'plugin',
            'function' => 'cleanUp',
            'description' => "Clean Up old and orphaned entries"
            );
        return $cron;
    }
    function Beacon(){


    }
	function cleanUp(){


	}
    /**
    * Logging routine - writes lines to a text file
    *
    * Auto-opens log file (if necessary) on first call
    *
    * @param string $logText - body of text to write
    * @param boolean $closeAfter - if TRUE, log file closed before exit; otherwise left open
    * @return none
    */
    function logLine($logText, $closeAfter = false, $addTimeDate = false){
        if ($this->logRequirement == 0) return;

        $logFilename = e_LOG . 'aprslog.txt';
        if ($this->logHandle == null){
            if (!($this->logHandle = fopen($logFilename, "a"))){ // Problem creating file?
                echo "File open failed!<br />";
                $this->logRequirement = 0;
                return;
            }
        }

        if (fwrite($this->logHandle, ($addTimeDate ? date('D j M Y G:i:s') . ': ' : '') . $logText . "\r\n") == false){
            $this->logRequirement = 0;
            echo 'File write failed!<br />';
        }

        if ($closeAfter){
            fclose($this->logHandle);
            $this->logHandle = null;
        }
    }
}

?>