<?php
/*
 * e107 website system
 *
 * Copyright (C) 2008-2009 e107 Inc (e107.org)
 * Released under the terms and conditions of the
 * GNU General Public License (http://www.gnu.org/licenses/gpl.txt)
 *
 *
 *
 * $Source: /cvs_backup/e107_0.8/e107_plugins/aprs/admin_aprs.php,v $
 * $Revision$
 * $Date$
 * $Author$
 */

$eplug_admin = true;
define( 'APRS_DEBUG', false );

require_once( "../../class2.php" );
error_reporting( E_ALL );
if ( !getperms( "P" ) || !e107::isInstalled( 'aprs' ) ) {
    header( "location:" . e_BASE . "index.php" );
    exit() ;
}
e107::js('aprs','js/aprs.js','jquery');	// Load Plugin javascript and include jQuery framework
e107::css('aprs','css/aprs.css');		// load css file

e107::lan( 'aprs', 'aprs' ); // e_PLUGIN.'aprs/languages/'.e_LANGUAGE.'/aprs.php'
e107::lan( 'aprs', 'admin_aprs' ); // e_PLUGIN.'aprs/languages/'.e_LANGUAGE.'/admin_aprs.php'


require_once( e_PLUGIN . 'aprs/includes/aprs_class.php' );
require_once( e_HANDLER . "form_handler.php" );

$e_sub_cat = 'aprs';

$aprs = new aprs();


$targetFields = array( 'gen_datestamp', 'gen_user_id', 'gen_ip', 'gen_intdata', 'gen_chardata' ); // Fields for aprs limits


require_once( "handlers/admin.php" );
new plugin_aprs_admin();

require_once( e_ADMIN . "auth.php" );;
// aprs/includes/admin.php is auto-loaded.
e107::getAdminUI()->runPage();
require_once( e_ADMIN . "footer.php" );
exit;


?>