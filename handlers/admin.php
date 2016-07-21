<?php
/*
 *
 *
 * Copyright (C) 2008-2015 G4HDU)
 * Released under the terms and conditions of the
 * GNU General Public License (http://www.gnu.org/licenses/gpl.txt)
 *
 * aprs Plugin Administration UI
 *
 * $URL: https://e107.svn.sourceforge.net/svnroot/e107/trunk/e107_0.8/e107_plugins/release/includes/admin.php $
 * $Id: admin.php 12212 2011-05-11 22:25:02Z e107coders $
*/

if (!defined('e107_INIT')){
    exit;
}
// var_dump($_COOKIE);
class plugin_aprs_admin extends e_admin_dispatcher{
    /**
    * Format: 'MODE' => array('controller' =>'CONTROLLER_CLASS'[, 'index' => 'list', 'path' => 'CONTROLLER SCRIPT PATH', 'ui' => 'UI CLASS NAME child of e_admin_ui', 'uipath' => 'UI SCRIPT PATH']);
    * Note - default mode/action is autodetected in this order:
    * - $defaultMode/$defaultAction (owned by dispatcher - see below)
    * - $adminMenu (first key if admin menu array is not empty)
    * - $modes (first key == mode, corresponding 'index' key == action)
    *
    * @var array
    */
    protected $modes = array (
        'main' => array (
            'controller' => 'aprs_main_admin_ui',
            'path' => null,
            'ui' => 'aprcs_main_admin_form_ui',
            'uipath' => null
            ),

        );

    /* Both are optional
	protected $defaultMode = null;
	protected $defaultAction = null;
	*/

    /**
    * Format: 'MODE/ACTION' => array('caption' => 'Menu link title'[, 'url' => '{e_PLUGIN}release/admin_config.php', 'perm' => '0']);
    * Additionally, any valid e107::getNav()->admin() key-value pair could be added to the above array
    *
    * @var array
    */
    protected $adminMenu = array(
        'main/settings' => array('caption' => "Preferences", 'perm' => 'P'),
        'main/Maintenance' => array('caption' => "Maintenance", 'perm' => 'P'),

        );

    /**
    * Optional, mode/action aliases, related with 'selected' menu CSS class
    * Format: 'MODE/ACTION' => 'MODE ALIAS/ACTION ALIAS';
    * This will mark active main/list menu item, when current page is main/edit
    *
    * @var array
    */
    protected $adminMenuAliases = array(
        'main/edit' => 'main/list'
        );

    /**
    * Navigation menu title
    *
    * Dsiplays at top of admin menu
    *
    * @var string
    */
    protected $menuTitle = "APRS";
}

/**
* aprs_main_admin_ui
*
* @package
* @author barry
* @copyright Copyright (c) 2015
* @version $Id$
* @access public
*/
class aprs_main_admin_ui extends e_admin_ui{
    protected $pluginTitle = "APRS";
    protected $pluginName = 'aprs';
    protected $eventName = 'aprs';

    /**
    * DB Table, table alias is supported
    * Example: 'r.aprs'
    *
    * @var string
    */
    protected $table = "aprscalls"; // DB Table, table alias is supported. Example: 'r.release'
    /**
    * If present this array will be used to build your list query
    * You can link fileds from $field array with 'table' parameter, which should equal to a key (table) from this array
    * 'leftField', 'rightField' and 'fields' attributes here are required, the rest is optional
    * Table alias is supported
    * Note:
    * - 'leftTable' could contain only table alias
    * - 'leftField' and 'rightField' shouldn't contain table aliases, they will be auto-added
    * - 'whereJoin' and 'where' should contain table aliases e.g. 'whereJoin' => 'AND u.user_ban=0'
    *
    * @var array [optional] table_name => array join parameters
    */
    //protected $tableJoin = array(
        // 'u.user' => array('leftField' => 'comment_author_id', 'rightField' => 'user_id', 'fields' => '*'/*, 'leftTable' => '', 'joinType' => 'LEFT JOIN', 'whereJoin' => '', 'where' => ''*/)
    //    );

    /**
    * This is only needed if you need to JOIN tables AND don't wanna use $tableJoin
    * Write your list query without any Order or Limit.
    *
    * @var string [optional]
    */
   //protected $listQry = "";
    // optional - required only in case of e.g. tables JOIN. This also could be done with custom model (set it in init())
    // protected $editQry = "SELECT * FROM #aprs WHERE aprs_id = {ID}";
    // required - if no custom model is set in init() (primary id)
    protected $pid = "aprscalls_ID";
    // optional
    protected $perPage = 10;
    // default - true - TODO - move to displaySettings
    protected $batchDelete = false;
    // UNDER CONSTRUCTION
    protected $displaySettings = array();
    // UNDER CONSTRUCTION
    /**
    * (use this as starting point for wiki documentation)
    * $fields format  (string) $field_name => (array) $attributes
    *
    * $field_name format:
    * 	'table_alias_or_name.field_name.field_alias' (if JOIN support is needed) OR just 'field_name'
    * NOTE: Keep in mind the count of exploded data can be 1 or 3!!! This means if you wanna give alias
    * on main table field you can't omit the table (first key), alternative is just '.' e.g. '.field_name.field_alias'
    *
    * $attributes format:
    * 	- title (string) Human readable field title, constant name will be accpeted as well (multi-language support
    *
    *      - type (string) null (means system), number, text, dropdown, url, image, icon, datestamp, userclass, userclasses, user[_name|_loginname|_login|_customtitle|_email],
    *        boolean, method, ip
    *      	full/most recent reference list - e_form::renderTableRow(), e_form::renderElement(), e_admin_form_ui::renderBatchFilter()
    *      	for list of possible read/writeParms per type see below
    *
    *      - data (string) Data type, one of the following: int, integer, string, str, float, bool, boolean, model, null
    *        Default is 'str'
    *        Used only if $dataFields is not set
    *      	full/most recent reference list - e_admin_model::sanitize(), db::_getFieldValue()
    *      - dataPath (string) - xpath like path to the model/posted value. Example: 'dataPath' => 'prefix/mykey' will result in $_POST['prefix']['mykey']
    *      - primary (boolean) primary field (obsolete, $pid is now used)
    *
    *      - help (string) edit/create table - inline help, constant name will be accpeted as well, optional
    *      - note (string) edit/create table - text shown below the field title (left column), constant name will be accpeted as well, optional
    *
    *      - validate (boolean|string) any of accepted validation types (see e_validator::$_required_rules), true == 'required'
    *      - rule (string) condition for chosen above validation type (see e_validator::$_required_rules), not required for all types
    *      - error (string) Human readable error message (validation failure), constant name will be accepted as well, optional
    *
    *      - batch (boolean) list table - add current field to batch actions, in use only for boolean, dropdown, datestamp, userclass, method field types
    *        NOTE: batch may accept string values in the future...
    *      	full/most recent reference type list - e_admin_form_ui::renderBatchFilter()
    *
    *      - filter (boolean) list table - add current field to filter actions, rest is same as batch
    *
    *      - forced (boolean) list table - forced fields are always shown in list table
    *      - nolist (boolean) list table - don't show in column choice list
    *      - noedit (boolean) edit table - don't show in edit mode
    *
    *      - width (string) list table - width e.g '10%', 'auto'
    *      - thclass (string) list table header - th element class
    *      - class (string) list table body - td element additional class
    *
    *      - readParms (mixed) parameters used by core routine for showing values of current field. Structure on this attribute
    *        depends on the current field type (see below). readParams are used mainly by list page
    *
    *      - writeParms (mixed) parameters used by core routine for showing control element(s) of current field.
    *        Structure on this attribute depends on the current field type (see below).
    *        writeParams are used mainly by edit page, filter (list page), batch (list page)
    *
    * $attributes['type']->$attributes['read/writeParams'] pairs:
    *
    * - null -> read: n/a
    * 		  -> write: n/a
    *
    * - dropdown -> read: 'pre', 'post', array in format posted_html_name => value
    * 			  -> write: 'pre', 'post', array in format as required by e_form::selectbox()
    *
    * - user -> read: [optional] 'link' => true - create link to user profile, 'idField' => 'author_id' - tells to renderValue() where to search for user id (used when 'link' is true and current field is NOT ID field)
    * 				   'nameField' => 'comment_author_name' - tells to renderValue() where to search for user name (used when 'link' is true and current field is ID field)
    * 		  -> write: [optional] 'nameField' => 'comment_author_name' the name of a 'user_name' field; 'currentInit' - use currrent user if no data provided; 'current' - use always current user(editor); '__options' e_form::userpickup() options
    *
    * - number -> read: (array) [optional] 'point' => '.', [optional] 'sep' => ' ', [optional] 'decimals' => 2, [optional] 'pre' => '&euro; ', [optional] 'post' => 'LAN_CURRENCY'
    * 			-> write: (array) [optional] 'pre' => '&euro; ', [optional] 'post' => 'LAN_CURRENCY', [optional] 'maxlength' => 50, [optional] '__options' => array(...) see e_form class description for __options format
    *
    * - ip		-> read: n/a
    * 			-> write: [optional] element options array (see e_form class description for __options format)
    *
    * - text -> read: (array) [optional] 'htmltruncate' => 100, [optional] 'truncate' => 100, [optional] 'pre' => '', [optional] 'post' => ' px'
    * 		  -> write: (array) [optional] 'pre' => '', [optional] 'post' => ' px', [optional] 'maxlength' => 50 (default - 255), [optional] '__options' => array(...) see e_form class description for __options format
    *
    * - textarea 	-> read: (array) 'noparse' => '1' default 0 (disable toHTML text parsing), [optional] 'bb' => '1' (parse bbcode) default 0,
    * 								[optional] 'parse' => '' modifiers passed to e_parse::toHTML() e.g. 'BODY', [optional] 'htmltruncate' => 100,
    * 								[optional] 'truncate' => 100, [optional] 'expand' => '[more]' title for expand link, empty - no expand
    * 		  		-> write: (array) [optional] 'rows' => '' default 15, [optional] 'cols' => '' default 40, [optional] '__options' => array(...) see e_form class description for __options format
    * 								[optional] 'counter' => 0 number of max characters - has only visual effect, doesn't truncate the value (default - false)
    *
    * - bbarea -> read: same as textarea type
    * 		  	-> write: (array) [optional] 'pre' => '', [optional] 'post' => ' px', [optional] 'maxlength' => 50 (default - 0),
    * 				[optional] 'size' => [optional] - medium, small, large - default is medium,
    * 				[optional] 'counter' => 0 number of max characters - has only visual effect, doesn't truncate the value (default - false)
    *
    * - image -> read: [optional] 'title' => 'SOME_LAN' (default - LAN_PREVIEW), [optional] 'pre' => '{e_PLUGIN}myplug/images/',
    * 				'thumb' => 1 (true) or number width in pixels, 'thumb_urlraw' => 1|0 if true, it's a 'raw' url (no sc path constants),
    * 				'thumb_aw' => if 'thumb' is 1|true, this is used for Adaptive thumb width
    * 		   -> write: (array) [optional] 'label' => '', [optional] '__options' => array(...) see e_form::imagepicker() for allowed options
    *
    * - icon  -> read: [optional] 'class' => 'S16', [optional] 'pre' => '{e_PLUGIN}myplug/images/'
    * 		   -> write: (array) [optional] 'label' => '', [optional] 'ajax' => true/false , [optional] '__options' => array(...) see e_form::iconpicker() for allowed options
    *
    * - datestamp  -> read: [optional] 'mask' => 'long'|'short'|strftime() string, default is 'short'
    * 		   		-> write: (array) [optional] 'label' => '', [optional] 'ajax' => true/false , [optional] '__options' => array(...) see e_form::iconpicker() for allowed options
    *
    * - url	-> read: [optional] 'pre' => '{ePLUGIN}myplug/'|'http://somedomain.com/', 'truncate' => 50 default - no truncate, NOTE:
    * 			-> write:
    *
    * - method -> read: optional, passed to given method (the field name)
    * 			-> write: optional, passed to given method (the field name)
    *
    * - hidden -> read: 'show' => 1|0 - show hidden value, 'empty' => 'something' - what to be shown if value is empty (only id 'show' is 1)
    * 			-> write: same as readParms
    *
    * - upload -> read: n/a
    * 			-> write: Under construction
    *
    * Special attribute types:
    * - method (string) field name should be method from the current e_admin_form_ui class (or its extension).
    * 		Example call: field_name($value, $render_action, $parms) where $value is current value,
    * 		$render_action is on of the following: read|write|batch|filter, parms are currently used paramateres ( value of read/writeParms attribute).
    * 		Return type expected (by render action):
    * 			- read: list table - formatted value only
    * 			- write: edit table - form element (control)
    * 			- batch: either array('title1' => 'value1', 'title2' => 'value2', ..) or array('singleOption' => '<option value="somethig">Title</option>') or rendered option group (string '<optgroup><option>...</option></optgroup>'
    * 			- filter: same as batch
    *
    * @var array
    */
    protected $fields = array(
        'checkboxes' 			=> array('title' => '',			'type' => null,       	'data' => null, 	'width' => '5%', 						'thclass' => 'center', 'forced' => true, 	'class' => 'center', 'toggle' => 'e-multiselect'),
        'aprscalls_ID' 			=> array('title' => ID,			'type' => 'number',   	'data' => 'int', 	'width' => '5%', 	'inline' => false,	'thclass' => '', 		'batch' => false,						'forced' => true, 'noedit' => true,  'nolist'=>false, 'primary' => true/*, 'noedit'=>TRUE*/), // Primary ID is not editable
    	'aprscallsCallsign' 	=> array('title' => "Station",	'type' => 'text',     	'data' => 'str',	'width' => 'auto', 	'inline' => true,  	'thclass' => '',		'batch' => false,	'filter' => false,	'forced' => true, 'noedit' => false, 'nolist'=>false),
        'aprscallsComment' 		=> array('title' => "Comment",	'type' => 'text',     	'data' => 'str',	'width' => 'auto', 	'inline' => false,   'thclass' => '',		'batch' => false, 	'filter' => false,	'forced' => true, 'noedit' => false, 'nolist'=>false),
    	'aprscallsActive' 		=> array('title' => "Active",	'type' => 'boolean',  	'data' => 'bool',	'width' => '5%',	'inline' => false,   'thclass' => 'center', 	'batch' => true, 	'filter' => true,	'forced' => true, 'noedit' => false, 'nolist'=>false),
		'aprscallsWX' 			=> array('title' => "WX",  		'type' => 'boolean',  	'data' => 'bool',	'width' => '5%',	'inline' => false,   'thclass' => 'center', 	'batch' => true, 	'filter' => true,	'forced' => true, 'noedit' => false, 'nolist'=>false),
        'aprscallsWildcard' 	=> array('title' => "Wild Card",'type' => 'boolean',	'data' => 'bool', 	'width' => '5%', 	'inline' => false,	'thclass' => 'center', 	'batch' => true, 	'filter' => true,	'forced' => true, 'noedit' => false, 'nolist'=>false),
        'aprscallsMenu' 		=> array('title' => "Show" ,	'type' => 'boolean', 	'data' => 'bool', 	'width' => '5%', 	'inline' => false,	'thclass' => 'center', 	'batch' => true, 	'filter' => true,	'forced' => true, 'noedit' => false, 'nolist'=>false),
        'aprscallsLastReport' 	=> array('title' => "Last Report",'type' =>'datestamp',	'data' => 'datestamp','width' => 'auto',	'inline' => false, 	'thclass' => '',		'batch' => false, 	'filter' => false,	'forced' => true, 'noedit' => true,	'nolist'=>true,		'readParms' => array('mask' => 'dd mm yyyy - hh:ii:ss'), 'writeParms' => '', ),
        'aprscallsLastUpdate' 	=> array('title' => "Last Update",'type' =>'datestamp',	'data' => 'datestamp','width' => 'auto',	'inline' => false, 	'thclass' => '',		'batch' => false, 	'filter' => false,	'forced' => true, 'noedit' => true,	'nolist'=>true,		'readParms' => array('mask' => 'dd mm yyyy - hh:ii:ss'), 'writeParms' => '', ),
        'aprscallsLastEdit' 	=> array('title' => "Last Edit",'type' => 'datestamp', 	'data' => 'datestamp','width' => 'auto',	'inline' => false, 	'thclass' => '', 		'batch' => false, 	'filter' => false,	'forced' => true, 'noedit' => true, 'nolist'=>true,		'readParms' => array('mask' => 'dd mm yyyy - hh:ii:ss'), 'writeParms' => '', 'noedit' => true),
        'options' 				=> array('title' => LAN_OPTIONS,'type' => null, 		'data' => null, 	'width' => '10%', 						'thclass' => 'center last', 'class' => 'center last', 'forced' => true)
        );
    // required - default column user prefs
    protected $fieldpref = array(
    	'checkboxes',
    	'aprscalls_ID',
    	'aprscallsCallsign',
    	'aprscallsComment',
    	'aprscallsActive',
    	'aprscallsWX',
    	'aprscallsWildcard',
    	'aprscallsMenu',
    	'aprscallsLastReport',
    	'aprscallsLastUpdate',

    	'options');

    protected $action = array();
    protected $subAction = array();
    protected $id = "";
    // FORMAT field_name=>type - optional if fields 'data' attribute is set or if custom model is set in init()
    /*protected $dataFields = array();*/
    // optional, could be also set directly from $fields array with attributes 'validate' => true|'rule_name', 'rule' => 'condition_name', 'error' => 'Validation Error message'
    /*protected  $validationRules = array(
			'release_url' => array('required', '', 'Release URL', 'Help text', 'not valid error message')
		);*/
    // optional, if $pluginName == 'core', core prefs will be used, else e107::getPluginConfig($pluginName);
    /**
    * aprs_main_admin_ui::observe()
    *
    * Watch for this being triggered. If it is then do something
    *
    * @return
    */
    public function observe(){
        if (isset($_POST['updateaprsoptions'])){ // Save prefs.
            $this->save_prefs();
        }

        if (isset($_POST)){
            // e107::getCache()->clear( "download_cat" );
        }
    }
    // optional
    public function init(){
        $this->action = vartrue($_GET['mode']);
        $this->subAction = vartrue($_GET['action']);
        $this->id = vartrue($_GET['id']);
        $this->observe();
    }

    function settingsPage(){
        // global $adminDownload;
        $this->show_aprs_options();
    }

    function maintPage(){
        showMaint();
    }

    function save_prefs(){
        global $admin_log;
        e107::getPlugPref('aprs');
        $tp = e107::getParser();

        $temp = array();
        $temp['aprs_activity'] = $_POST['aprs_activity'];
        $temp['aprs_viewclass'] = $_POST['aprs_viewclass'];
        $temp['aprs_adminclass'] = $_POST['aprs_adminclass'];
        $temp['aprs_emai'] = $_POST['aprs_emai'];
        $temp['aprs_logging'] = $_POST['aprs_logging'];
        $temp['aprs_cronlogging'] = $_POST['aprs_cronlogging'];
        $temp['aprs_logdest'] = $_POST['aprs_logdest'];
        $temp['aprs_serverdb'] = $_POST['aprs_serverdb'];
        $temp['aprs_serverhost'] = $_POST['aprs_serverhost'];
        $temp['aprs_serverport'] = $tp->toDB($_POST['aprs_serverport']);
        $temp['aprs_serveruser'] = $tp->toDB($_POST['aprs_serveruser']);
        $temp['aprs_serverpass'] = $_POST['aprs_serverpass'];
        $temp['aprs_serverprefix'] = $_POST['aprs_serverprefix'];
        $temp['aprs_servertableprefix'] = $_POST['aprs_servertableprefix'];

        $temp['aprs_menuclass'] = $_POST['aprs_menuclass'];
        $temp['aprs_menuamber'] = $_POST['aprs_menuamber'];
        $temp['aprs_menured'] = $_POST['aprs_menured'];
        $temp['aprs_menuvisible'] = $_POST['aprs_menuvisible'];

        e107::getConfig('aprs')->setPref($temp)->save(false);
        // now generate text file
        /*
    	   my $database = "e107";
    	   my $host     = "localhost";
    	   my $user     = "root";
    	   my $pw       = "";
    	   my $port     = "3306";

    	   # APRS-IS config
    	   my $IShost   = "rotate.aprs2.net:14580";
    	   my $ISmycall = "G4HDU";
    	   */

        $fieldList[] = array('host', $temp['aprs_serverhost']);
        $fieldList[] = array('port', $temp['aprs_serverport']);
        $fieldList[] = array('user', $temp['aprs_serveruser']);
        $fieldList[] = array('pw', $temp['aprs_serverpass']);
        $fieldList[] = array('prefix', $temp['aprs_serverprefix']);

        $fieldList[] = array('ISmycall', $temp['aprs_call']);
        $fieldList[] = array('ISpasscode', $temp['aprs_passcode']);

        $errorfp = false;

        $fp = fopen('aprs2db/aprs.conf', 'w');
        if ($fp === false){
            $errorMsg = "Error opening output file. ";
            $errorfp = true;
        }else{
            foreach($fieldList as $field){
                $fpresult = fputcsv($fp, $field, ',', '"');
                if ($fpresult === false){
                    $writeError = "Error writing CSV. ";
                    $errorfp = true;
                }
            }
        }
        if ($errorfp){
            $outMessage = $errorMsg . $writeError;
            // $mes->addError($outMessage);
        }else{
            // $mes->addSuccess();
        }
        fclose($fp);
    }

    /**
    * aprs_main_admin_ui::show_aprs_options()
    *
    * @return
    */
    function show_aprs_options(){
        global $ns;
        $pref = e107::getPlugPref('aprs');
        require_once(e_HANDLER . "form_handler.php");
        $frm = new e_form(true); //enable inner tabindex counter
        $activity_options = array('sep' => "&nbsp;&nbsp;:", 'help' => "The type of installation");
        $logging_dest = array('0' => 'Database', '1' => 'e107 Logfiles');
        $aprs_serverdb = array('0' => "This site's settings", '1' => "Remote settings below");
        $tab1active = '';
        $tab1Class == '';
        $tab2active = '';
        $tab2Class == '';
        $tab3active = '';
        $tab3Class == '';
        $tab4active = '';
        $tab4Class == '';
        $activeTab = $_COOKIE['aprsLastTab'];
        $tabTime = $_COOKIE['aprsLastTabTime'];
        // print time()-$tabTime;
        // print"</br>";
        if (time() - $tabTime > 180){
            $activeTab = 1;
            $tabTime = time();
            setcookie("aprsLastTab", 1, 0, '/');
            setcookie("aprsLastTabTime", $tabTime, 0, '/');
        }

        switch ($activeTab){
            case 2:
                $tab2active = ' active' ;
                $tab2Class = " class='active' ";
                break;
            case 3:
                $tab3active = ' active' ;
                $tab3Class = " class='active' ";
                break;
            case 4:
                $tab4active = ' active' ;
                $tab4Class = " class='active' ";
                break;
            case 1:
            default :
                $tab1active = ' active' ;
                $tab1Class = " class='active' ";
                break;
        }
        $menu_colour = array(
            '15' => '15 mins',
            '30' => '30 mins',
            '45' => '45 mins',
            '60' => '1 hour',
            '120' => '2 hours',
            '360' => '6 hours');
    	$perpage=array( '10'=>'10','15'=>'15','20'=>'20','25'=>'25','30'=>'30','50'=>'50');
        $text = "
	<ul class='nav nav-tabs'>
		<li {$tab1Class} id='aprsTab1' ><a data-toggle='tab' href='#core-aprs-aprs1'>General</a></li>
		<li {$tab2Class} id='aprsTab2' ><a data-toggle='tab' href='#core-aprs-aprs2'>Database</a></li>
		<li {$tab3Class} id='aprsTab3' ><a data-toggle='tab' href='#core-aprs-aprs3'>Menu</a></li>
		<li {$tab4Class} id='aprsTab4' ><a data-toggle='tab' href='#core-aprs-aprs4'>Social</a></li>
	</ul>
	<form method='post' id='aprsPrefForm' action='" . e_SELF . "?" . e_QUERY . "'>\n
   		<div class='tab-content'>
			<div class='tab-pane {$tab1active}' id='core-aprs-aprs1'>
		    	<div>
		        	<table class='table adminform'>
		            	<colgroup>
		            		<col style='width:30%'/>
		            		<col style='width:70%'/>
		            	</colgroup>
		            	<tr>
		               		<td>View Class</td>
		                 	<td>" . $frm->userclass('aprs_viewclass', $pref['aprs_viewclass'], 'dropdown') . "</td>
		                </tr>
		            	<tr>
		               		<td>Admin Class</td>
		                 	<td>" . $frm->userclass('aprs_adminclass', $pref['aprs_adminclass'], 'dropdown') . "</td>
		                </tr>
		            	<tr>
		               		<td>Records per page</td>
		            		<td>" . $frm->select('aprs_perpage', $perpage, $pref['aprs_perpage']) . "</td>
		            	</tr>
		            	<tr>
		               		<td>Error Email</td>
		            		<td>" . $frm->email('aprs_emai', $pref['aprs_emai'], '50', array('size' => '50')) . "</td>
		                </tr>
		                <tr>
		                	<td>Error Logging</td>
		            		<td>" . $frm->select('aprs_logging', 'yesno', $pref['aprs_logging']) . "</td>
		            	</tr>
		            	<tr>
		            		<td>Cron Logging</td>
		            		<td>" . $frm->select('aprs_cronlogging',  'yesno', $pref['aprs_cronlogging']) . "</td>
		              	</tr>
		            	<tr>
		            		<td>Log Location</td>
		            		<td>" . $frm->select('aprs_logdest', $logging_dest, $pref['aprs_logdest']) . "</td>
		            	</tr>
		        	</table>
		        </div>
			</div>
		   	<div class='tab-pane {$tab2active}' id='core-aprs-aprs2'>
		    	<div>
		        	<table class='table adminform'>
		            	<colgroup>
		            		<col style='width:30%'/>
		            		<col style='width:70%'/>
		            	</colgroup>
		            	<tr>
		               		<td>Database</td>
		               		<td>" . $frm->select('aprs_serverdb', $aprs_serverdb, $pref['aprs_serverdb']) . "</td>
		                </tr>
		                <tr>
		            		<td>SQL Host</td>
		            		<td>" . $frm->text('aprs_serverhost', $pref['aprs_serverhost'], '25', array('size' => '25')) . "</td>
		            	</tr>
		               	<tr>
							<td>SQL port</td>
		            		<td>" . $frm->text('aprs_serverport', $pref['aprs_serverport'], '6', array('size' => '6')) . "</td>
		            	</tr>
		            	<tr>
		            		<td>SQL Username</td>
		            		<td>" . $frm->text('aprs_serveruser', $pref['aprs_serveruser'], '25', array('size' => '25')) . "</td>
		                </tr>
		            	<tr>
		            		<td>SQL Password</td>
		            		<td>" . $frm->text('aprs_serverpass', $pref['aprs_serverpass'], '25', array('size' => '25')) . "</td>
		            	</tr>
		            	<tr>
		            		<td>SQL Database</td>
		            		<td>" . $frm->text('aprs_serverprefix', $pref['aprs_serverprefix'], '15', array('size' => '15')) . "</td>
		            	</tr>
		            	<tr>
		            		<td>SQL Table Prefix</td>
		            		<td>" . $frm->text('aprs_servertableprefix', $pref['aprs_servertableprefix'], '15', array('size' => '15')) . "</td>
		            	</tr>
		            </table>
		        </div>
			</div>
		   	<div class='tab-pane {$tab3active}' id='core-aprs-aprs3'>
		    	<div>
		        	<table class='table adminform'>
		            	<colgroup>
		            		<col style='width:30%'/>
		            		<col style='width:70%'/>
		            	</colgroup>
		            	<tr>
		               		<td>Menu visible to</td>
		                 	<td>" . $frm->userclass('aprs_menuvisible', $pref['aprs_menuvisible'], 'dropdown') . "</td>
		                </tr>
		            	<tr>
		               		<td>Go Amber after</td>
		               		<td>" . $frm->select('aprs_menuamber', $menu_colour, $pref['aprs_menuamber']) . "</td>
		                </tr>
		                <tr>
		                	<td>Go Red after</td>
		               	   	<td>" . $frm->select('aprs_menured', $menu_colour, $pref['aprs_menured']) . "</td>
		            	</tr>
		            </table>
		        </div>
			</div>
		   	<div class='tab-pane {$tab4active}' id='core-aprs-aprs4'>
		    	<div>
		        	<table class='table adminform'>
		            	<colgroup>
		            		<col style='width:30%'/>
		            		<col style='width:70%'/>
		            	</colgroup>
		            	<tr>
		            		<td colspan='2' ><b>Twitter</b></td>
		            	</tr>
		            	<tr>
		            		<td>Post type</td>
		            		<td>//TODO</td>
		            	</tr>
		            	<tr>
		            		<td>Account</td>
		            		<td>//TODO</td>
		            	</tr>
		            	<tr>
		            		<td>Password</td>
		            		<td>//TODO</td>
		            	</tr>
		            </table>
		        </div>
			</div>
			<div class='buttons-bar center'>
		    	<input class='btn button' type='submit' name='updateaprsoptions' value='Update'/>
		    </div>
		</div>
	</form>";
        echo $text;
        return $text;
    }
}

class aprs_main_admin_form_ui extends e_admin_form_ui{
}