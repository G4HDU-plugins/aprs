<?php
if (!defined('e107_INIT')){
    exit;
}
/**
* AprsTemplate
*
* @package
* @author barry
* @copyright Copyright (c) 2015
* @version $Id$
* @access public
*/
class aprsTemplate{
    /**
    * AprsTemplate::__construct()
    */
    function __construct(){
    }
    function aprsMainHeader(){
        $retval = "
<div id='aprsMainPage'>
	{APRS_BREAD}
		";
        return $retval;
    }
    function aprsSelect(){
        $retval = "
	<div id='aprsMainCaption'>
		<div class='aprsHeaderLeft' class='' >Action {APRS_ACTION}</div>
		<div class='aprsHeaderLeft' >Callsign {APRS_SELECTCALL}</div>
		<div class='aprsHeaderRight' >{APRS_SELCOMMENT}&nbsp;</div>
		<div style='clear:both;'></div>
	</div>	";
        return $retval;
    }

    function aprsTableHeader(){
        if ($this->order == 'asc'){
            $orderClass = 'fa-chevron-up';
        }else{
            $orderClass = 'fa-chevron-down';
        }
        $retval = "
	<table id='aprsTable' >
		<thead>
			<tr>
				<th id='aprsSorter' class='aprsSortCalls aprsTableDate aprsBottom' >
					<div style='vertical-align:middle;display:inline-block;height:22px;' >Report Time</div>
					<i id='aprsSortIcon' class='fa faDateColour fa-w fa-2 $orderClass'></i>
				</th>
				<th class='aprsTableWAB aprsBottom aprsLeft' >WAB</th>
				<th class='aprsTableIARU aprsBottom aprsLeft' >IARU</th>
				<th class='aprsTableIcon aprsBottom aprsLeft' >Icon</th>
				<th class='aprsTableLatLon aprsBottom aprsLeft' >Lat</th>
				<th class='aprsTableLatLon aprsBottom aprsLeft' >Long</th>
				<th class='aprsTableComment aprsBottom aprsLeft' >Comment</th>
			</tr>
		</thead>
		<tbody>
		";
        return $retval;
    }
    function aprsTableDetail(){
        $retval = "
			<tr>
				<td class='aprsTableDate' >{APRS_LAST}</td>
				<td class='aprsTableWAB' >{APRS_WAB}</td>
				<td class='aprsTableIARU' >{APRS_QRA}</td>
				<td class='aprsTableIcon' >{APRS_ICON}</td>
				<td class='aprsTableLatLon' >{APRS_LAT}</td>
				<td class='aprsTableLatLon' >{APRS_LONG}</td>
				<td class='aprsTableComment' >{APRS_COMMENT}</td>

			</tr>
		";
        return $retval;
    }
    function aprsTableNoDetail(){
        $retval = "
			<tr>
				<td colspan='7' >No Records</td>
			</tr>
		";
        return $retval;
    }
    function aprsTableNoSelect(){
        $retval = "
			<div>Select activity</div>
		";
        return $retval;
    }
    function aprsTableFooter(){
        $retval = "
		<tbody>
	</table>
		";
        return $retval;
    }
    function aprsMainFooter(){
        $retval .= "
	<div id='aprsFooterContainer' >
		<div id='aprsFooterLeft' >{APRS_PERPAGE}&nbsp;</div>
		<div id='aprsFooterRight' >{APRS_BUDDY}</div>
	</div> <!-- end footer container -->
</div>";
        return $retval;
    }
    function aprsMsgHeader(){
        $retval = "
	<table id='aprsTable' >
		<thead>
			<tr>
				<th class='aprsTableDate aprsBottom ' >Report Time <i class=' fa fa-fw fa-sort'></i></th>
				<th class='aprsTableCall aprsBottom aprsLeft' >Call to</th>
				<th class='aprsTableCall aprsBottom aprsLeft' >Call from</th>
				<th class='aprsTableIcon aprsBottom aprsLeft' >Icon</th>
				<th class='aprsTableMessage aprsBottom aprsLeft' >Message</th>

			</tr>
		</thead>
		<tbody>
		";
        return $retval;
    }
    function aprsMsgDetail(){
        $retval = "
			<tr>
				<td class='aprsTableDate' >{APRS_LAST}</td>
				<td class='aprsTableCall' >{APRS_CALLTO}</td>
				<td class='aprsTableCall' >{APRS_CALLFROM}</td>
				<td class='aprsTableIcon' >{APRS_ICON}</td>
				<td class='aprsTableMessage' >{APRS_MESSAGE}</td>

			</tr>
		";
        return $retval;
    }
    function aprsMsgNoDetail(){
        $retval = "
			<tr>
				<td colspan='5' >No Messages</td>
			</tr>
		";
        return $retval;
    }
    function aprsMsgFooter(){
        $retval = "
		<tbody>
	</table>
		";
        return $retval;
    }
    function aprsWxHeader(){
        $retval = "
	<table id='aprsTable' >
		<thead>
			<tr>
				<th class='aprsTableDate aprsBottom' colspan='1'>&nbsp;</th>
				<th class='aprsTableWxMid aprsTableWxTitle aprsBottom aprsLeft' colspan='3'>Wind</th>
				<th class='aprsTableWxMid aprsTableWxTitle aprsBottom aprsLeft' colspan='3'>Atmosphere</th>
				<th class='aprsTableWxMid aprsTableWxTitle aprsBottom aprsLeft' colspan='3'>Rainfall mm</th>
			</tr>
			<tr>

				<th class='aprsTableWxDate aprsBottom' >Report Time <i class=' fa fa-fw fa-sort'></i></th>
				<th class='aprsTableWxH aprsBottom aprsLeft' >Dirn &deg;</th>
				<th class='aprsTableWxH aprsBottom aprsLeft' >Speed</th>
				<th class='aprsTableWxH aprsBottom aprsLeft' >Gust</th>
				<th class='aprsTableWxH aprsBottom aprsLeft' >Temp &#8451;</th>
				<th class='aprsTableWxH aprsBottom aprsLeft' >Hum %</th>
				<th class='aprsTableWxH aprsBottom aprsLeft' >Baro HPa</th>
				<th class='aprsTableWxH aprsBottom aprsLeft' >Last Hr</th>
				<th class='aprsTableWxH aprsBottom aprsLeft' >Last 24</th>
				<th class='aprsTableWxH aprsBottom aprsLeft' >Today</th>

			</tr>
		</thead>
		<tbody>
		";
        return $retval;
    }
    function aprsWxDetail(){
        $retval = "
			<tr>
				<td class='aprsTableWxDate' >{APRS_LAST}</td>
				<td class='aprsTableWx' >{APRS_DIRECTION}</td>
				<td class='aprsTableWx' >{APRS_SPEED}</td>
				<td class='aprsTableWx' >{APRS_GUST}</td>
				<td class='aprsTableWx' >{APRS_TEMP}</td>
				<td class='aprsTableWx' >{APRS_HUM}</td>
				<td class='aprsTableWx' >{APRS_BARO}</td>
				<td class='aprsTableWx' >{APRS_RAINHOUR}</td>
				<td class='aprsTableWx' >{APRS_RAINDAY}</td>
				<td class='aprsTableWx' >{APRS_RAINMIDNIGHT}</td>

			</tr>
		";
        return $retval;
    }
    function aprsWxNoDetail(){
        $retval = "
			<tr>
				<td colspan='5' >No Messages</td>
			</tr>
		";
        return $retval;
    }
    function aprsWxFooter(){
        $retval = "
		<tbody>
	</table>
		";
        return $retval;
    }

	function aprsCallsHeader(){
		if ($this->order == 'asc'){
			$orderClass = 'fa-chevron-up';
		}else{
			$orderClass = 'fa-chevron-down';
		}
		$retval = "
<div  id='aprsMainPage' >
{APRS_BREAD}
	<table id='aprsTable' >
		<thead>
			<tr>
				<th class='aprsID aprsBottom aprsLeft' >ID</th>
				<th id='aprsCallsSorter' class='aprsSortHeader aprsCall aprsBottom' >
				<div style='vertical-align:middle;display:inline-block;height:22px;' >Callsign</div>
					<i id='aprsSortIcon' class='fa faDateColour fa-w fa-2 $orderClass'></i>
				</th>
				<th class='aprsComment aprsBottom aprsLeft' >Comment</th>
				<th class='aprsCheck aprsBottom aprsLeft' >Active</th>
				<th class='aprsCheck aprsBottom aprsLeft' >Show</th>
				<th class='aprsCheck aprsBottom aprsLeft' >WX</th>
				<th class='aprsCheck2 aprsBottom aprsLeft' >Action</th>
			</tr>
		</thead>
		<tbody>
		";
		return $retval;

	}
	function aprsCallsDetail(){
		$retval = "
			<tr>
				<td class='aprsID' >{APRS_ID}</td>
				<td class='aprsCall' >{APRS_CALL}</td>
				<td class='aprsComment' >{APRS_CALLCOMMENT}</td>
				<td class='aprsCheck' >{APRS_ACTIVE}</td>
				<td class='aprsCheck' >{APRS_SHOw}</td>
				<td class='aprsCheck' >{APRS_WX}</td>
				<td class='aprsAction' >{APRS_CALLACTION}</td>
			</tr>
		";
		return $retval;
	}
	function aprsCallsNoDetail(){
		$retval = "
			<tr>
				<td colspan='7' >No Calls</td>
			</tr>
		";
		return $retval;
	}
	function aprsCallsFooter(){
		$retval .= "
			</tbody>
		</table>
	<div id='aprsFooterContainer' >
		<div id='aprsFooterLeft' >{APRS_PERPAGE}&nbsp;</div>
		<div id='aprsFooterRight' >{APRS_PASSGEN}{APRS_SETTINGS}{APRS_CREATE}</div>
	</div> <!-- end footer container -->
</div>";
		return $retval;
	}
function aprsCallsDelete(){
	$retval="
<div  id='aprsEditPage' >
	{APRS_BREAD}
	<table id='aprsDeleteTable' >
		<thead>
			<tr>
				<th class='aprsEditCaption' >Delete Record</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class='aprsEditCaption' >You are about to delete the following record<br />
				ID <b>{APRS_DELID}</b> :: Callsign <b>{APRS_DELCALL}</b></td>
			</tr>
			<tr>
				<td class='aprsEditCaption' >{APRS_DELCANCEL}&nbsp;&nbsp;{APRS_DELDO}</td>
			</tr>
		</tbody>
	</table>
</div>
	";
	return $retval;
}
	function aprsCallsEdit(){
				$retval .= "
<div  id='aprsEditPage' >
{APRS_BREAD}
	<table id='aprsEditTable' >
		<thead>
			<tr>
				<th class='aprsEditCaption' colspan='2' >Edit Record</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class='aprsEditCaptionL' >ID</td>
				<td class='aprsEditCaptionR' >{APRS_EDID}</td>
			</tr>
			<tr>
				<td class='aprsEditCaptionL' >Callsign</td>
				<td class='aprsEditCaptionR' >{APRS_EDCALL}</td>
			</tr>
			<tr>
				<td class='aprsEditCaptionL' >Comment</td>
				<td class='aprsEditCaptionR' >{APRS_EDCOMMENT}</td>
			</tr>
			<tr>
				<td class='aprsEditCaptionL' >Active</td>
				<td class='aprsEditCaptionR aprsRadio' ><div>{APRS_EDACTIVE}</div></td>
			</tr>
			<tr>
				<td class='aprsEditCaptionL' >Show in Menu</td>
				<td class='aprsEditCaptionR aprsRadio' >{APRS_EDMENU}</td>
			</tr>
			<tr>
				<td class='aprsEditCaptionL' >Weather Station</td>
				<td class='aprsEditCaptionR aprsRadio' >{APRS_EDWX}</td>
			</tr>

			<tr>
				<td class='aprsEditCaption' colspan='2'>{APRS_EDCANCEL}&nbsp;&nbsp;{APRS_EDUPDATE}</td>
			</tr>
		</tbody>
	</table>
</div>
		";
		return $retval;
	}
	function aprsPrefsEdit(){
		$retval .= "
<div  id='aprsEditPage' >
	{APRS_BREAD}
	{APRS_TABS}
	<div class='aprsEditCaption'>{APRS_PREFSCANCEL}&nbsp;&nbsp;{APRS_PREFSUPDATE}</div>
</div>
		";
		return $retval;
	}
	function prefsTab1(){
		$retval="
<table id='aprsEditTable1' >
	<thead>
		<tr>
			<th class='aprsEditCaption' colspan='2' >Edit General Settings</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class='aprsEditCaptionL' >Host</td>
			<td class='aprsEditCaptionR' >{APRS_HOST}</td>
		</tr>
		<tr>
			<td class='aprsEditCaptionL' >Port</td>
			<td class='aprsEditCaptionR' >{APRS_PORT}</td>
		</tr>
		<tr>
			<td class='aprsEditCaptionL' >Callsign</td>
			<td class='aprsEditCaptionR' >{APRS_LOGINCALL}</td>
		</tr>
		<tr>
			<td class='aprsEditCaptionL' >Passcode</td>
			<td class='aprsEditCaptionR' >{APRS_PASSCODE}</td>
		</tr>
		<tr>
			<td class='aprsEditCaptionL' >Client</td>
			<td class='aprsEditCaptionR' >{APRS_CLIENT}</td>
		</tr>
		<tr>
			<td class='aprsEditCaptionL' >Filter</td>
			<td class='aprsEditCaptionR' >{APRS_FILTER}</td>
		</tr>
		<tr>
			<td class='aprsEditCaptionL' >Logging</td>
			<td class='aprsEditCaptionR aprsRadio' >{APRS_LOGGING}</td>
		</tr>
		<tr>
			<td class='aprsEditCaptionL' >Debug</td>
			<td class='aprsEditCaptionR aprsRadio' >{APRS_DEBUG}</td>
		</tr>
				<tr>
			<td class='aprsEditCaptionL' >&nbsp;</td>
			<td class='aprsEditCaptionR aprsRadio' >&nbsp;</td>
		</tr>
	</tbody>
</table>";
		return $retval;
	}
	function prefsTab2(){
$retval="
<table id='aprsEditTable2' >
	<thead>
		<tr>
			<th class='aprsEditCaption' colspan='2' >Edit Beacon Settings</th>
		</tr>
	</thead>
	<tbody>

		<tr>
			<td class='aprsEditCaptionL' >Beacon Callsign</td>
			<td class='aprsEditCaptionR aprsRadio' ><div>{APRS_BEACONCALLSIGN}</div></td>
		</tr>
		<tr>
			<td class='aprsEditCaptionL' >Beacon Passcode</td>
			<td class='aprsEditCaptionR aprsRadio' ><div>{APRS_BEACONPASSCODE}</div></td>
		</tr>
		<tr>
			<td class='aprsEditCaptionL' >Beacon Port</td>
			<td class='aprsEditCaptionR aprsRadio' ><div>{APRS_BEACONPORT}</div></td>
		</tr>
		<tr>
			<td class='aprsEditCaptionL' >Beacon Latitude</td>
			<td class='aprsEditCaptionR aprsRadio' ><div>{APRS_BEACONLAT}</div></td>
		</tr>
		<tr>
			<td class='aprsEditCaptionL' >Beacon Longitude</td>
			<td class='aprsEditCaptionR aprsRadio' ><div>{APRS_BEACONLON}</div></td>
		</tr>
		<tr>
			<td class='aprsEditCaptionL' >Beacon Altitude</td>
			<td class='aprsEditCaptionR aprsRadio' ><div>{APRS_BEACONALT}</div></td>
		</tr>
		<tr>
			<td class='aprsEditCaptionL' >Beacon Text</td>
			<td class='aprsEditCaptionR aprsRadio' >{APRS_BEACONTEXT}</td>
		</tr>
		<tr>
			<td class='aprsEditCaptionL' >Beacon Interval</td>
			<td class='aprsEditCaptionR aprsRadio' >{APRS_BEACONINT}</td>
		</tr>
		<tr>
			<td class='aprsEditCaptionL' >Beacon Active</td>
			<td class='aprsEditCaptionR aprsRadio' ><div>{APRS_BEACONACTIVE}</div></td>
		</tr>
	</tbody>
</table>";
		return $retval;

	}
	function aprsPassGen(){
		$retval .= "
<div  id='aprsEditPage' >
{APRS_BREAD}
	<table id='aprsEditTable' >
		<thead>
			<tr>
				<th class='aprsEditCaption' colspan='2' >Generate APRS Passcode</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class='aprsEditCaptionL' >Callsign</td>
				<td class='aprsEditCaptionR' >{APRS_PASSCALL}</td>
			</tr>
			<tr>
				<td class='aprsEditCaptionL' >Passcode</td>
				<td class='aprsEditCaptionR' >{APRS_PASSMADE}</td>
			</tr>
			<tr>
				<td class='aprsEditCaption' colspan='2'>{APRS_PASSMAKE}</td>
			</tr>
		</tbody>
	</table>
</div>
		";
		return $retval;
	}
	function aprsNotPermitted(){
		$retval .= "
<div  id='aprsEditPage' >
{APRS_BREAD}

</div>
		";
		return $retval;
	}
}

?>