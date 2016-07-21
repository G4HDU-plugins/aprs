<?php

if (!defined('e107_INIT')){
    exit;
}
global $menu_pref, $aprs;
error_reporting(E_ALL);
$e107 = e107::getInstance();
$tp = e107::getParser();
$sql = e107::getDb();
$gen = new convert;
$pref = e107::getPlugPref('aprs');
e107::lan('aprs', 'menu', true); // English_menu.php or {LANGUAGE}_menu.php
require_once(e_PLUGIN . 'aprs/includes/aprs_class.php');
require_once(e_PLUGIN . 'aprs/includes/aprs_coord_class.php');
if (!is_object($aprs)){
    $aprs = new Aprs;
}
error_reporting(E_ALL);

$numrecs = $aprs->getAprs();
$imagepath = e_PLUGIN . "aprs/images/";
// <div class='aprSprite aprsIcon' > </div>
$sql = e107::getDb();

$frm = e107::getForm();
$qry = "SELECT CallsignSSID from #aprstrack group by CallsignSSID order by CallsignSSID";
if ($sql->gen($qry, false)){
    while ($row = $sql->fetch()){
        $call = strtoupper($row['CallsignSSID']);
        $option_array[$call] = $call;
    }
}
$text .= "
<div class='aprsVdata' >View Data For ";
$text .= $frm->open("aprsMenuTracking", "get", e_SELF);
$text .= $frm->select("aprsMCallsign", $option_array, $selected , $options, "Select call");
$text .= $frm->close();
$text .= "
</div>
<div id='aprsContainer' class='boxes' >
";
$now=time();
$amber=60*$pref['aprs_menuamber'];
$red=60*$pref['aprs_menured'];
//print "$now $amber $red \n";
for($inc = 0;$inc <= 2;$inc++){
    if ($inc == 0){
        $class = 'aprsMenuItemShow';
    }else{
        $class = 'aprsMenuItemHide';
    }
	$diff=$now-$aprs->data[$inc]['utime'];
//	print " $amber $red $diff <br />";
	if ($diff<$amber) {
		$colour='aprsGreen';
	}elseif($diff>=$amber && $diff<$red){
		$colour='aprsAmber';
	}else{
		$colour='aprsRed';
	}
//	print"$diff \n";
    // $wab = $aprs->getWAB($aprs->data[$inc]['Latitude'], $aprs->data[$inc]['Longitude']);
    // $qra = $aprs->getQRA($aprs->data[$inc]['Latitude'], $aprs->data[$inc]['Longitude']);
    $call = trim($aprs->data[$inc]['aprscallsCallsign']);
    $text .= "
	<div id='aprsEach{$inc}' class='box{$inc} $class' >
		<div class='aprsMenuContainer' >
			<div class='aprsMenuTrack'>
				<div class='aprsColumn aprsColumn-two'>Tracking: <b>$call</b><br /><span class='aprsLocs'>WAB: <b>{$aprs->data[$inc]['wab']}</b> | IARU: <b>{$aprs->data[$inc]['iaru']}</b></span></div>
			</div>
			<div class='aprsMenuComment'>
				<div class='aprsMenuLeft aprsMenuMedium'>Comment:<br /><span class='aprsMenuCommentText'>" . $aprs->data[$inc]['comment'] . "&nbsp;</span></div>
			</div>
			<div class='aprsMenuDetails $colour'>
				<div class='aprsMenuLeft aprsMenuMedium'>Last report <b>" . date("D dS M Y H:i:s", $aprs->data[$inc]['utime']) . "</b> UTC</div>
				<div class='aprsMenuLeft aprsMenuMedium'>Position: <b>" . abs($aprs->data[$inc]['Latitude']) . ($aprs->data[$inc]['Latitude'] > 0?" N ":" S ") . "&nbsp;&nbsp;|&nbsp;&nbsp;" . abs($aprs->data[$inc]['Longitude']) . ($aprs->data[$inc]['Longitude'] >= 0?" W ":" E ") . "</b></div>
			</div>
			<div class='aprsMenuHistoryBorder' >
				<div class='aprsMenuHistory aprsMenuHistory1 '>
					<a href='" . e_PLUGIN_ABS . "aprs/index.php?aprsCallsign={$call}' >
						<img src='" . e_PLUGIN_ABS . "aprs/images/hist48.png' title='View Data' alt='history' /><br />View Data
					</a>
				</div>
				<div class='aprsMenuHistory aprsMenuHistory2 ' >
					<a href='http://aprs.fi/#!mt=roadmap&amp;z=11&amp;call=a%2F" . $call . "&amp;timerange=43200&amp;tail=43200' target='_blank' >
						<img src='" . e_PLUGIN_ABS . "aprs/images/aprs48.png' title='View on Map' alt='history' /><br />Map View
					</a>
				</div>
			</div>
		</div>
	</div>";
}
$text .= "
	</div>
	<div class='aprsMenuCentre aprsMenuSmall'>
		Map data from <a href='http://aprs.fi' target='_blank' >http://aprs.fi</a>
	</div>
";
e107::getRender()->tablerender('WAB APRS', $text, 'wabquick_menu');

?>