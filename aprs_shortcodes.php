<?php
if (!defined('e107_INIT')){
    exit;
}

/**
*
* @package wab
* @subpackage wab
* @version 1.0.1
* @author baz
*
* 	wab shortcodes
*/

/**
* aprs_shortcodes
*
* @package
* @author barry
* @copyright Copyright (c) 2015
* @version $Id$
* @access public
*/
class aprs_shortcodes extends e_shortcode{
    public $dataRow;
    public $perPage;
    public $rec;

    function __construct(){
        parent::__construct();
    }

    protected function makeToolTip($placement = 'bottom', $toolTip = 'Default'){
        return " data-toggle='wabToolTip' data-placement='{$placement}' data-original-title='{$toolTip}'";
    }
    function sc_aprs_selcomment(){
        return $this->rec['selcomment'];
    }
    function sc_aprs_selectcall(){
        return $this->rec['selectcall'];
    }
    function sc_aprs_action(){
        return $this->rec['selectop'];
    }

    function sc_aprs_arrived(){
        if ($this->rec['aprsdata_arrived'] > 0){
            return date('d/m/y H:i:s', $this->rec['aprsdata_arrived']);
        }else{
            return '';
        }
    }
    function sc_aprs_last(){
        if ($this->rec['unixReport'] > 0){
            return date('jS M Y H:i', $this->rec['unixReport']);
        }else{
            return '';
        }
    }
    function sc_aprs_perpage(){
        return $this->rec['perpage'];
    }
    function sc_aprs_wab(){
        return $this->rec['wab'];
    }
    function sc_aprs_qra(){
        return $this->rec['iaru'];
    }
    function sc_aprs_lat(){
        return $this->rec['Latitude'];
    }
    function sc_aprs_long(){
        return $this->rec['Longitude'];
    }
    function sc_aprs_comment(){
        return $this->rec['comment'];
    }
    function sc_aprs_icon(){
        return $this->rec['icon'];
    }
    function sc_aprs_callto(){
        return $this->rec['CallsignTo'];
    }
    function sc_aprs_callfrom(){
        return $this->rec['CallsignSSID'];
    }
    function sc_aprs_callmessage(){
        return $this->rec['Message'];
    }
    function sc_aprs_callaction(){
        return $this->rec['action'];
    }
    function sc_aprs_id(){
        return $this->rec['aprscalls_ID'];
    }
    function sc_aprs_call(){
        return $this->rec['aprscallsCallsign'];
    }
    function sc_aprs_callcomment(){
        return $this->rec['aprscallsComment'];
    }
    function sc_aprs_active(){
        return $this->rec['aprscallsActive'];
    }
    function sc_aprs_wx(){
        return $this->rec['aprscallsWX'];
    }
    function sc_aprs_show(){
        return $this->rec['aprscallsMenu'];
    }
    function sc_aprs_buddy(){
        if ($this->admin){
            return "<a href='" . e_SELF . "?aprsAction=calls' ><i class='fa fa-cogs'></i></a>";
        }else{
            return '';
        }
    }
	function sc_aprs_delid(){
		return $this->rec['aprscalls_ID'];
	}
	function sc_aprs_delcall(){
		return $this->rec['aprscallsCallsign'];
	}
    function sc_aprs_edid(){
        return $this->rec['aprscalls_ID'];
    }
    function sc_aprs_edcall(){
        return $this->rec['edcall'];
    }
    function sc_aprs_edactive(){
        return $this->rec['edactive'];
    }
    function sc_aprs_edcomment(){
        return $this->rec['edcomment'];
    }
    function sc_aprs_edmenu(){
        return $this->rec['edmenu'];
    }
    function sc_aprs_edwx(){
        return $this->rec['edwx'];
    }
    function sc_aprs_edcancel(){
        return $this->rec['edcancel'];
    }
    function sc_aprs_edupdate(){
        return $this->rec['edupdate'];
    }
	function sc_aprs_delcancel(){
		return $this->rec['delcancel'];
	}
	function sc_aprs_deldo(){
		return $this->rec['deldo'];
	}
    function sc_aprs_create(){
        if ($this->admin){
            return "<a href='" . e_SELF . "?aprsAction=create' title='Create new record'><i class='fa fa-plus-square faMid'></i></a>";
        }else{
            return '';
        }
    }
    function sc_aprs_settings(){
        if ($this->admin){
            return "<a href='" . e_SELF . "?aprsAction=settings' title='APRS Settings'><i class='fa fa-cog faMid'></i></a> | ";
        }else{
            return '';
        }
    }
    function sc_aprs_home(){
        return "<a href='" . e_SELF . "?aprsAction=list' title='APRS Home'><i class='fa fa-home faMid'></i></a>";
    }
    function sc_aprs_back(){
        return "<a href='#' title='APRS Home'><i class='fa fa-arrow-circle-left faMid'></i></a>";
    }
	function sc_aprs_tabs(){
		return $this->rec['tabs'];
	}
	function sc_aprs_tab1(){
		 return $this->rec['tab1'];
	}
	function sc_aprs_tab2(){
		 return $this->rec['tab2'];
	}
    function sc_aprs_bread(){
        return $this->rec['bread'];
    }
    function sc_aprs_host(){
        return $this->rec['aprsprefs_host'];
    }
	function sc_aprs_port(){
		return $this->rec['aprsprefs_port'];
	}
    function sc_aprs_logincall(){
        return $this->rec['aprsprefs_mycall'];
    }
    function sc_aprs_passcode(){
        return $this->rec['aprsprefs_pass'];
    }
	function sc_aprs_filter(){
		return $this->rec['aprsprefs_filter'];
	}
	function sc_aprs_logging(){
		return $this->rec['aprsprefs_logging'];
	}
	function sc_aprs_debug(){
		return $this->rec['aprsprefs_debug'];
	}
    function sc_aprs_client(){
        return $this->rec['aprsprefs_client'];
    }
    function sc_aprs_beaconactive(){
        return $this->rec['aprsprefs_beaconactive'];
    }
    function sc_aprs_beacontext(){
        return $this->rec['aprsprefs_beacontext'];
    }
    function sc_aprs_beaconcallsign(){
        return $this->rec['aprsprefs_beaconcallsign'];
    }
    function sc_aprs_beaconpasscode(){
        return $this->rec['aprsprefs_beaconpass'];
    }
    function sc_aprs_beaconport(){
        return $this->rec['aprsprefs_beaconport'];
    }
    function sc_aprs_beaconlat(){
        return $this->rec['aprsprefs_beaconlat'];
    }
    function sc_aprs_beaconlon(){
        return $this->rec['aprsprefs_beaconlon'];
    }
    function sc_aprs_beaconalt(){
        return $this->rec['aprsprefs_beaconalt'];
    }
    function sc_aprs_beaconint(){
        return $this->rec['aprsprefs_beaconinterval'];
    }
    function sc_aprs_prefscancel(){
        return $this->rec['prefcancel'];
    }
    function sc_aprs_prefsupdate(){
        return $this->rec['prefupdate'];
    }
    function sc_aprs_passgen(){
        if ($this->admin){
            return "<a  href='" . e_SELF . "?aprsAction=passcode' title='APRS Home'> <i class='fa fa-key faMid'></i></a> | ";
        }else{
            return '';
        }
    }
    function sc_aprs_passcall(){
        return $this->rec['passcall'];
    }
    function sc_aprs_passmake(){
        return $this->rec['passmake'];
    }
    function sc_aprs_passmade(){
        return $this->rec['passmade'];
    }
}