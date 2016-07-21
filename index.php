<?php
/*
 * e107 website system
 *
 * Copyright (C) 2008-2013 e107 Inc (e107.org)
 * Released under the terms and conditions of the
 * GNU General Public License (http://www.gnu.org/licenses/gpl.txt)
 *
 * e107 APRS Plugin
 *
*/
require_once("../../class2.php");
if (!defined('e107_INIT')){
    exit;
}
e107::js('aprs', 'js/aprs.js', 'jquery'); // Load Plugin javascript and include jQuery framework
e107::css('aprs', 'css/aprs.css'); // load css file
e107::lan('aprs', 'front', true); // front language files
e107::lan('aprs', 'global', true); // front language files

// e107::lan( 'aprs' ); // load language file ie. e107_plugins/_aprs/languages/English.php
e107::meta('keywords', 'some words'); // add meta data to <HEAD>

require_once(HEADERF); // render the header (everything before the main content area)

require_once('includes/aprs_class.php');
require_once('templates/aprs_template.php');

$ns = e107::getRender(); // render in theme box.

if (!is_object($aprs)){
    $aprs = new aprs;
}
// $aprs->status();
if ($aprs->viewer){
    $aprs->aprsMain();
}else{
	$aprs->aprsNotPermitted();
}
require_once(FOOTERF); // render the footer (everything after the main content area)
exit;

?>