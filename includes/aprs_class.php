<?php
if (!defined('e107_INIT')){
    exit;
}

require_once(e_PLUGIN . 'aprs/templates/aprs_template.php');
require_once(e_HANDLER . 'np_class.php');
/*

   findu get data
   raw.cgi

   This displays raw packets.
   name	units	default	remarks
   call	 	(mandatory)
   start	hours	24	start of history
   length	hours	start	length of history
   time	 	0	if 1, prepends a timestamp to each packet
   Example:
   http://www.findu.com/cgi-bin/raw.cgi?call=g4hdu-9&start=48&length=24&time=1

   rawposit.cgi

   This displays raw position packets.
   name	units	default	remarks
   call	 	(mandatory)
   start	hours	24	start of history
   length	hours	start	length of history
   time	 	0	if 1, prepends a timestamp to each packet
   Example:
   http://www.findu.com/cgi-bin/rawposit.cgi?call=g4hdu-9&start=48&length=24

   http://www.findu.com/cgi-bin/raw.cgi?call=g4hdu-9&start=48&length=24&time=1
   20150508171941,G4HDU-9>APDR13,TCPIP*,qAC,T2LEIPZIG:=5331.43N/00256.10Wk041/002/A=000255 http://g4hdu.co.uk Listening 145.500


*/
/**
* aprs
*
* @package aprs
* @author barry
* @copyright Copyright (c) 2015
* @version $Id$
* @access public
*/
class aprs{
    public $data;
    public $ns;

    private $current;
    private $prefs;
    private $mes;
    private $sql;
    private $localsql;
    private $tp;
    private $frm;
    private $gen;
    private $sc;
    private $parameters;
    private $template;
    private $np;
    public $admin;
    public $viewer;

    /**
    * aprs::__construct()
    */
    function __construct(){
        // initialise all objects
        $this->prefs = e107::getPlugPref('aprs', '', true);
        $this->mes = e107::getMessage();
        $this->tp = e107::getParser();
        $this->frm = e107::getForm();
        $this->ns = e107::getRender();
        $this->gen = e107::getDateConvert();
        $this->sc = e107::getScBatch('aprs', true);
        $this->template = new aprsTemplate;
        $this->np = new nextprev;
        if ($this->prefs['aprs_serverdb'] == 1){
            // using remote db
            // var_dump($this->prefs);
            $this->sql = new DB;
            $host = $this->prefs['aprs_serverhost'];
            $user = $this->prefs['aprs_serveruser'];
            $password = $this->prefs['aprs_serverpass'];
            $prefix = $this->prefs['aprs_servertableprefix'];
            $database = $this->prefs['aprs_serverprefix'];
            if (!empty($this->prefs['aprs_serverport'])){
                $host .= ':' . $this->prefs['aprs_serverport'];
            }
            // print "$host $user $password $database $prefix<br />";
            $res = $this->sql->connect($host, $user, $password, true);
            if ($res === false){
                $this->mes->addError('Unable to connect to remote database');
            }else{
                // $this->mes->addInfo('Connected to remote server');
                $res = $this->sql->database($database, $prefix);
                if ($res === false){
                    $this->mes->addError('Unable to select remote database');
                }
            }
        }else{
            // using local db
            $this->sql = e107::getDb();
        }
        $this->admin = check_class($this->prefs['aprs_adminclass']);
        $this->viewer = check_class($this->prefs['aprs_viewclass']) || check_class($this->prefs['aprs_adminclass']); //view class and admins can view{
        $this->sc->admin = $this->admin;
        $this->sc->viewer = $this->viewer;
        $this->aprsIcon("/z", 24);
        $this->aprsIcon("/z", 48);
        $this->aprsIcon("/z", 64);
        $tmp = '';
    }
    /**
    * aprs::saveSession()
    *
    * @return
    */
    private function saveSession(){
        $_SESSION['aprs'] = $this->current;
    }
    /**
    * aprs::getSession()
    *
    * @return
    */
    private function getSession(){
        $this->current->$_SESSION['aprs'] ;
    }
    /**
    * aprs::checkUpdate()
    *
    * @return
    */
    private function checkUpdate(){
        /*
    	'prefUpdate' => '20', this is the vakue to reset to - initial time interval
  'prefIncrement' => '0', this is how much to increate the time interval by if no data change
  'prefInterval' => '0', the number of seconds to elapse before incrementing by prefIncrement drconds
  'prefMaxInterval' => '0', Never wait more than this long before updating
    	   */
        /*
    	   adaptive update

    	   if next update is zero or if time to go check
    	   		pull data and cache it
    	   if data changed
    	   		reset timer
    	   else
	    	   if maximum interval reached
    		   		do nothing
    		   else
    	   			increment timer
    	   calculate next update time


    	   */

        $timeNow = time();
        $prefUpdate = $this->prefs->getPref('prefUpdate', 0);
        $prefIncrement = $this->prefs->getPref('prefIncrement', 0);
        $prefInterval = $this->prefs->getPref('prefInterval', 0);
        $prefMaxInterval = 60 * $this->prefs->getPref('prefMaxInterval', 0);
        $nextIncrement = $this->prefs->getPref('nextIncrement', 0);
        $lastUpdate = $this->prefs->getPref('lastUpdate', $timeNow);
        $nextUpdate = $this->prefs->getPref('nextUpdate', $timeNow - 1);
        $dataChanged = false;
        // var_dump($this->prefs);
        // print "<br /> Last update $lastUpdate - " . date('d m y H:i:s', $lastUpdate);
        if ($nextUpdate < $timeNow){
            $dataChanged = $this->getAprs();
            $lastUpdate = $timeNow;
        }
        if ($dataChanged){
            $nextUpdate = $timeNow + $prefUpdate; // reset next update time
            // print "<br />Data Changed";
        }else{
            // print "<br />No Update";
            // print "<br /> Last update $lastUpdate - " . date('d m y H:i:s', $lastUpdate);
            // calculate seconds elapsed frm last update
            $elapsedTime = $timeNow - $lastUpdate;
            // print "<br />elapsed time $elapsedTime";
            $requiredIncrements = intval($elapsedTime / $prefIncrement);
            $addTime = ceil($requiredIncrements * $prefInterval);
            if ($addTime > $prefMaxInterval){
                $addTime = $prefMaxInterval;
            }
            $nextUpdate = $lastUpdate + $addTime;
            if ($nextUpdate < ($timeNow + $prefUpdate)){
                $nextUpdate = $timeNow + $prefUpdate;
            }
        }
        /*
        print "<br />pref update $prefUpdate";
        print "<br />elapsed $elapsedTime";
        print "<br />increments $requiredIncrements";
        print "<br />add time $addTime";
        print "<br />Time now $timeNow - " . date('d m y H:i:s', $timeNow);
        print "<br /> Last update $lastUpdate - " . date('d m y H:i:s', $lastUpdate);
        print "<br /> next update $nextUpdate - " . date('d m y H:i:s', $nextUpdate);
    	   */
        $this->prefs->set('lastUpdate', $lastUpdate);
        $this->prefs->set('nextUpdate', $nextUpdate);
        $this->prefs->set('nextIncrement', $nextIncrement);
        $this->prefsSave();
    }
    /**
    * aprs::prefsSave()
    *
    * @return
    */
    private function prefs107Save(){
        $this->prefs->save(false, true);
    }
    /**
    * aprs::getAprs()
    *
    * @return
    */
    function getAprs(){
        $qry = "SELECT *,unix_timestamp(ReportTime) as utime from #aprsposits
    	left join #aprscalls on CallsignSSID=aprscallsCallsign
    	WHERE aprscallsMenu=1 ";
        $numrecs = $this->sql->gen($qry, false);
        if ($numrecs > 0){
            while ($row = $this->sql->fetch()){
                $this->data[] = $row;
            }
        }else{
            $numrecs = false;
        }
        // var_dump($this->data);
        return $numrecs;
    }
    function getFiAprs(){
        $qry = "SELECT * from #aprsdata WHERE aprsdata_callsign='G4HDU-9' ORDER by aprsdata_last DESC LIMIT 1";
        $numrecs = $this->sql->gen($qry, false);
        if ($numrecs > 0){
            $row = $this->sql->fetch();
        }
        // var_dump($row);
        if (is_null($row) || (time() - $row['aprsdata_last']) > 60){
            $url = 'http://api.aprs.fi/api/get?name=G4HDU-9&what=loc&apikey=15686.i0g0X7kl7LLZ0Ge5&format=json';
            $userAgent = "User-Agent: wabaprs/1.1.1-beta1 (+http://g4hdu.co.uk/wabaprs/)";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
            curl_setopt($ch, CURLOPT_HEADER, false);

            $data = curl_exec($ch);
            curl_close($ch);
            $max = json_decode($data, false);
            $callsign = $max->entries[0]->name;
            // var_dump($callsign);
            // calculate wab and qra
            $lat = $max->entries[0]->lat;
            $lng = $max->entries[0]->lng;
            $wab = $this->getWAB($lat, $lng);
            $qra = $this->getQRA($lat, $lng);
            $arg = array(
                'aprsdata_id' => 0,
                'aprsdata_callsign' => $callsign,
                'aprsdata_data' => $data,
                'aprsdata_wab' => $wab,
                'aprsdata_qra' => $qra,
                'aprsdata_last' => time());
            $this->sql->insert('aprsdata', $arg, false);
            $this->sql->gen($qry, false);

            $row = $this->sql->fetch();
        }
        $data = $row['aprsdata_data'];
        $max = json_decode($data, false);
        $max->wab = $row['aprsdata_wab'];
        $max->qra = $row['aprsdata_qra'];
        // var_dump($max);
        $this->data = $max;
        return ;
    }
    /**
    * aprs::getWAB()
    *
    * @param integer $lat
    * @param integer $lng
    * @return
    */
    private function getWAB($lat = 0, $lng = 0){
        $ll2w = new LatLng($lat, $lng);
        $ll2w->WGS84ToOSGB36();
        $os2w = $ll2w->toOSRef();
        $osref .= $os2w->toSixFigureString();
        $fbit = substr($osref, 0, 3) . substr($osref, 5, 1);
        return $fbit;
    }
    /**
    * aprs::getQRA()
    *
    * @param integer $lat
    * @param integer $lng
    * @return
    */
    private function getQRA($lat = 0, $lng = 0){
        $iaru = new IARU;
        $qra = $iaru->toIARU($lat, $lng);
        return $qra;
    }
    /**
    * aprs::aprsMain()
    *
    * @return
    */
    private function aprsRecords(){
        $perpage = 15;
        $qry = "SELECT CallsignSSID,aprscallsComment
        from #aprstrack
        left join #aprscalls on CallsignSSID=aprscallsCallsign
        group by CallsignSSID
        order by CallsignSSID";
        if ($this->sql->gen($qry, false)){
            while ($row = $this->sql->fetch()){
                $call = strtoupper($row['CallsignSSID']);
                $option_array[$call] = $call;
                if ($call == $this->callsign){
                    $selcomment = $row['aprscallsComment'];
                }
            }
        }
        $ops_array = array('trackchange' => 'Track Changes', 'trackfull' => 'Tracking History', 'messages' => 'Messages', 'status' => 'Status', 'weather' => 'Weather');
        $bread[] = array();
        $this->sc->rec['bread'] = $this->frm->breadcrumb($bread);
        $text .= $this->tp->parseTemplate($this->template->aprsMainHeader(), false, $this->sc);
        $text .= $this->frm->open("aprsTracking", "get", e_SELF);
        $text .= $this->frm->hidden('aprsFrom', $this->from);
        $text .= $this->frm->hidden('aprsOrder', $this->order);
        $this->template->order = $this->order;
        $this->sc->rec['selectop'] = $this->frm->select("aprsAction", $ops_array, $this->action , $opoptions, "Select Activity");
        $this->sc->rec['selectcall'] = $this->frm->select("aprsCallsign", $option_array, $this->callsign , $options, "Select call");
        $this->sc->rec['selcomment'] = $selcomment;

        $text .= $this->tp->parseTemplate($this->template->aprsSelect(), false, $this->sc);
        $text .= $this->frm->close();
        switch ($this->action){
            case 'trackfull':
                // get total number of records
                $qry = "select CallsignSSID from #aprstrack where callsignssid='{$this->callsign}' ";
                $total = $this->sql->gen($qry, false);
                $bread[] = array();
                $this->sc->rec['bread'] = $this->frm->breadcrumb($bread);
                $text .= $this->tp->parseTemplate($this->template->aprsTableHeader(), false, $this->sc);
                $qry = "select *,unix_timestamp(ReportTime) as unixReport from #aprstrack where callsignssid='{$this->callsign}' order by ReportTime $this->order limit {$this->from},$perpage";
                if ($this->sql->gen($qry, false)){
                    while ($this->sc->rec = $this->sql->fetch()){
                        $text .= $this->tp->parseTemplate($this->template->aprsTableDetail(), false, $this->sc);
                    }
                }else{
                    $text .= $this->tp->parseTemplate($this->template->aprsTableNoDetail(), false, $this->sc);
                }
                $text .= $this->tp->parseTemplate($this->template->aprsTableFooter(), false, $this->sc); ;
                break;
            case 'messages':
                $qry = "select *,unix_timestamp(ReportTime) as unixReport from #aprsmsg where CallsignSSID='{$this->callsign}' or CallsignTo='{$this->callsign}' ";
                $total = $this->sql->gen($qry, false);
                $bread[] = array();
                $this->sc->rec['bread'] = $this->frm->breadcrumb($bread);
                $text .= $this->tp->parseTemplate($this->template->aprsMsgHeader(), false, $this->sc);

                $qry = "select *,unix_timestamp(ReportTime) as unixReport from #aprsmsg where CallsignSSID='{$this->callsign}' or CallsignTo='{$this->callsign}' order by ReportTime $this->order limit {$this->from},$perpage";
                if ($this->sql->gen($qry, false)){
                    while ($this->sc->rec = $this->sql->fetch()){
                        $text .= $this->tp->parseTemplate($this->template->aprsMsgDetail(), false, $this->sc);
                    }
                }else{
                    $text .= $this->tp->parseTemplate($this->template->aprsMsgNoDetail(), false, $this->sc);
                }
                $text .= $this->tp->parseTemplate($this->template->aprsMsgFooter(), false, $this->sc); ;

                break;
            case'status' : ;
                break;
            case'weather' :
                $qry = "select *,unix_timestamp(ReportTime) as unixReport from #aprswx where CallsignSSID='{$this->callsign}'";;
                $total = $this->sql->gen($qry, false);
                $bread[] = array();
                $this->sc->rec['bread'] = $this->frm->breadcrumb($bread);
                $text .= $this->tp->parseTemplate($this->template->aprsWxHeader(), false, $this->sc);

                $qry = "select *,unix_timestamp(ReportTime) as unixReport from #aprswx where CallsignSSID='{$this->callsign}' order by ReportTime $this->order limit $start,$perpage";;
                if ($this->sql->gen($qry, false)){
                    while ($this->sc->rec = $this->sql->fetch()){
                        $text .= $this->tp->parseTemplate($this->template->aprsWxDetail(), false, $this->sc);
                    }
                }else{
                    $text .= $this->tp->parseTemplate($this->template->aprsWxNoDetail(), false, $this->sc);
                }
                $text .= $this->tp->parseTemplate($this->template->aprsWxFooter(), false, $this->sc); ;

                break;
            case 'trackchange':
                $qry = "select CallsignSSID from #aprstrack group by concat(wab,iaru,Icon) having callsignssid='{$this->callsign}' ";
                $total = $this->sql->gen($qry, false);
                $bread[] = array();
                $this->sc->rec['bread'] = $this->frm->breadcrumb($bread);
                $text .= $this->tp->parseTemplate($this->template->aprsTableHeader(), false, $this->sc);

                $qry = "select *,unix_timestamp(ReportTime) as unixReport from #aprstrack group by concat(wab,iaru,Icon) having callsignssid='{$this->callsign}' order by ReportTime $this->order  limit {$this->from},$perpage";
                if ($this->sql->gen($qry, false)){
                    while ($this->sc->rec = $this->sql->fetch()){
                        $text .= $this->tp->parseTemplate($this->template->aprsTableDetail(), false, $this->sc);
                    }
                }else{
                    $text .= $this->tp->parseTemplate($this->template->aprsTableNoDetail(), false, $this->sc);
                }
                $text .= $this->tp->parseTemplate($this->template->aprsTableFooter(), false, $this->sc); ;
                break;
            default: ;
                $text .= $this->tp->parseTemplate($this->template->aprsTableNoSelect(), false, $this->sc);
        } // switch
        $amount = $perpage;
        $options['glyphs'] = true;
        $options['type'] = 'record';
        $options['template'] = 'dropdown';
        $url = e_SELF . "?aprsFrom=[FROM]&amp;aprsAction={$this->action}&amp;aprsCallsign={$this->callsign}&amp;aprsOrder={$this->order}";
        // var_dump($total);
        // var_dump($this->from);
        // var_dump($amount);
        $total = varset($total, 0);
        $this->sc->rec['perpage'] = $this->frm->pagination($url, $total, $this->from, $perpage, $options);
        // $text .=  $this->frm->pagination($url, $total, $from, $perpage, $options);
        $text .= $np;
        $text .= $this->tp->parseTemplate($this->template->aprsMainFooter(), false, $this->sc);
        $this->ns->tablerender("APRS History", $text);
    }
    /**
    * aprs::callMain()
    *
    * @return
    */
    private function callMain(){
        $start = 0;
        $perpage = 15;
        $qry = "select * from #aprscalls  ";
        $total = $this->sql->gen($qry, false);
        $text .= $this->frm->open("aprsCallForm", "get", e_SELF);
        $text .= $this->frm->hidden('aprsAction', $this->action);
        $text .= $this->frm->hidden('aprsFrom', $this->from);
        $text .= $this->frm->hidden('aprsOrder', $this->order);
        $text .= $this->frm->close();
        $this->template->order = $this->order;
        $bread[] = array('url' => e_SELF, 'text' => 'APRS');
        $this->sc->rec['bread'] = $this->frm->breadcrumb($bread);
        $text .= $this->tp->parseTemplate($this->template->aprsCallsHeader(), false, $this->sc);

        $qry = "select * from #aprscalls  order by aprscallsCallsign $this->order limit {$this->from},$perpage";
        if ($this->sql->gen($qry, false)){
            while ($this->sc->rec = $this->sql->fetch()){
                $this->sc->rec['aprscallsActive'] = ($this->sc->rec['aprscallsActive'] == 1?"<i class='fa fa-thumbs-o-up faMid aprsGreenIcon'></i>":"<i class='fa fa-thumbs-o-down faMid aprsRedIcon'></i>");
                $this->sc->rec['aprscallsWX'] = ($this->sc->rec['aprscallsWX'] == 1?"<i class='fa fa-thumbs-o-up faMid aprsGreenIcon'></i>":"<i class='fa fa-thumbs-o-down faMid aprsRedIcon'></i>");
                $this->sc->rec['aprscallsMenu'] = ($this->sc->rec['aprscallsMenu'] == 1?"<i class='fa fa-thumbs-o-up faMid aprsGreenIcon'></i>":"<i class='fa fa-thumbs-o-down faMid aprsRedIcon'></i>");
                $this->sc->rec['action'] = "
					<a href='" . e_SELF . "?aprsFrom={$this->from}&amp;aprsOrder={$this->order}&amp;aprsAction=edit&amp;aprsCallID={$this->sc->rec['aprscalls_ID']}'><i class='fa fa-pencil-square-o faMid'></i></a>
&nbsp;&nbsp;<a href='" . e_SELF . "?aprsFrom={$this->from}&amp;aprsOrder={$this->order}&amp;aprsAction=delete&amp;aprsCallID={$this->sc->rec['aprscalls_ID']}'><i class='fa fa-trash-o faMid aprsDeleteIcon'></i></a>";
                $text .= $this->tp->parseTemplate($this->template->aprsCallsDetail(), false, $this->sc);
            }
        }else{
            $text .= $this->tp->parseTemplate($this->template->aprsCallsNoDetail(), false, $this->sc);
        }
        $text .= $this->tp->parseTemplate($this->template->aprsCallsFooter(), false, $this->sc); ;
        $this->ns->tablerender("APRS Calls", $this->mes->render() . $text);
    }
    /**
    * aprs::callDelete()
    *
    * @return
    */
    private function callDelete(){
        $this->action = '';

        $text .= $this->frm->open("aprsCallDeleteit", "get", e_SELF);
        $text .= $this->frm->hidden('aprsAction', 'calls');
        $text .= $this->frm->hidden('aprscallFrom', $this->callFrom);
        $text .= $this->frm->hidden('aprsOrder', $this->order);
        $text .= $this->frm->hidden('aprsCallID', $this->callID);
        $qry = "select * from #aprscalls where aprscalls_ID={$this->callID}";
        $this->sql->gen($qry, false);
        $this->sc->rec = $this->sql->fetch();
        $this->sc->rec['delcancel'] = $this->frm->button('aprscallscancel', 'cancel', 'cancel', 'Cancel', $options);
        $this->sc->rec['deldo'] = $this->frm->button('aprscallsdelete', 'danger', 'danger', 'Delete', $options);
        $this->sc->rec['bread'] = $this->frm->breadcrumb($bread);
        $text .= $this->tp->parseTemplate($this->template->aprsCallsDelete(), false, $this->sc);
        $text .= $this->frm->close();
        $this->ns->tablerender("APRS Calls", $this->mes->render() . $text);
    }
    /**
    * aprs::callDelok()
    *
    * @return
    */
    private function callDelok(){
        $qry = "DELETE from #aprscalls where aprscalls_ID={$this->callID}";
        if ($this->sql->gen($qry, false)){
            $this->mes->addSuccess("Record Deleted");
        }else{
            $this->mes->addError("Failed to delete record");
        }
    }
    /**
    * aprs::callEdit()
    *
    * @return
    */
    private function callEdit(){
        if ($this->action == 'create'){
            $this->callID = 0;
        }else{
            $qry = "select * from #aprscalls where aprscalls_ID={$this->callID}";
            $this->sql->gen($qry, false);
            $this->sc->rec = $this->sql->fetch();
        }

        $bread[] = array('url' => e_SELF, 'text' => 'APRS');
        $bread[] = array('url' => e_SELF . "?aprsFrom={$this->callFrom}&aprsOrder={$this->order}&aprsAction=calls", 'text' => 'Call List');
        $this->sc->rec['bread'] = $this->frm->breadcrumb($bread);
        $text .= $this->frm->open("aprsCallEdit", "get", e_SELF);
        $text .= $this->frm->hidden('aprsAction', $this->action);
        $text .= $this->frm->hidden('aprscallFrom', $this->callFrom);
        $text .= $this->frm->hidden('aprsOrder', $this->order);
        $text .= $this->frm->hidden('aprsCallID', $this->callID);

        $this->sc->rec['edcall'] = $this->frm->text('aprscallsCallsign', $this->sc->rec['aprscallsCallsign']);
        $options = array('maxlength' => 180, 'noresize' => true, 'size' => 15);
        $this->sc->rec['edcomment'] = $this->frm->textarea('aprscallsComment', $this->sc->rec['aprscallsComment'], 3, 80, $options, true);

        $this->sc->rec['edactive'] = $this->frm->radio_switch('aprscallsActive', ($this->sc->rec['aprscallsActive'] == 1?true:false) , 'Active', 'Inactive', $options);
        $this->sc->rec['edmenu'] = $this->frm->radio_switch('aprscallsMenu', ($this->sc->rec['aprscallsMenu'] == 1?true:false) , 'Show', 'Hide', $options);
        $this->sc->rec['edwx'] = $this->frm->radio_switch('aprscallsWX', ($this->sc->rec['aprscallsWX'] == 1?true:false) , 'Weather', 'Non Weather', $options);
        $this->sc->rec['edcancel'] = $this->frm->button('aprscallscancel', 'cancel', 'cancel', 'Cancel', $options);
        $this->sc->rec['edupdate'] = $this->frm->button('aprscallsupdate', 'update', 'update', 'Update', $options);

        $text .= $this->tp->parseTemplate($this->template->aprsCallsEdit(), false, $this->sc);
        // }else{
        // $text .= $this->tp->parseTemplate($this->template->aprsCallsNone(), false, $this->sc);
        // }
        $text .= $this->frm->close();
        $this->ns->tablerender("APRS Calls", $text);
    }
    /**
    * aprs::callSave()
    *
    * @return
    */
    private function callSave(){
        if ($_GET['aprsCallID'] == 0){
            // check it doesnt already exist
            $qry = "SELECT aprscalls_ID from #aprscalls WHERE aprscallsCallsign='{$_GET['aprscallsCallsign']}'";
            $exists = $this->sql->gen($qry, false);
            if (!$exists){
                $qry = "INSERT INTO #aprscalls
		(aprscallsCallsign,
		aprscallsComment,
		aprscallsActive,
		aprscallsMenu,
		aprscallsWX) VALUES (
		'{$_GET['aprscallsCallsign']}',
		'{$_GET['aprscallsComment']}',
		{$_GET['aprscallsActive']},
		{$_GET['aprscallsMenu']},
		{$_GET['aprscallsWX']}
		)";
                $res = $this->sql->gen($qry, false);
                if ($res === false){
                    $this->mes->addError("Failed to create record");
                }else{
                    $this->mes->addSuccess("Record created OK");
                }
            }else{
                $this->mes->addWarning("Call already exists - not created");
            }
        }else{
            $qry = "UPDATE #aprscalls set
		aprscallsCallsign='{$_GET['aprscallsCallsign']}',
		aprscallsComment='{$_GET['aprscallsComment']}',
		aprscallsActive={$_GET['aprscallsActive']},
		aprscallsMenu={$_GET['aprscallsMenu']},
		aprscallsWX={$_GET['aprscallsWX']}
		WHERE aprscalls_ID=" . (int)$_GET['aprsCallID'];
            $res = $this->sql->gen($qry, false);
            if ($res === false){
                $this->mes->addError("Failed to save changes");
            }else{
                $this->mes->addSuccess("Changes saved OK");
            }
        }
        $this->action = 'calls';
    }
    /**
    * aprs::editPrefs()
    *
    * @return
    */
    private function editPrefs(){
        // get the prefs record
        // should never be more than 1 record
        $qry = "select * from #aprsprefs LIMIT 1";
        if ($this->sql->gen($qry, false)){
            // got one
            $this->sc->rec = $this->sql->fetch();
            $this->aprsprefs_id = $this->sc->rec['aprsprefs_id'];
        }else{
            // no rec exists so a blank one
            $this->sc->rec = array();
            $this->aprsprefs_id = 0;
        }
        $this->action = 'prefsave';
        $bread[] = array('url' => e_SELF, 'text' => 'APRS');
        $bread[] = array('url' => e_SELF . "?aprsFrom={$this->callFrom}&aprsOrder={$this->order}&aprsAction=calls", 'text' => 'Call List');
        $this->sc->rec['bread'] = $this->frm->breadcrumb($bread);
        $text .= $this->frm->open("aprsPrefsEdit", "get", e_SELF);
        $text .= $this->frm->hidden('aprsAction', $this->action);
        $text .= $this->frm->hidden('aprscallFrom', $this->callFrom);
        $text .= $this->frm->hidden('aprsOrder', $this->order);
        $text .= $this->frm->hidden('aprsprefs_id', $this->aprsprefs_id);

        $this->sc->rec['aprsprefs_host'] = $this->frm->text('aprsprefs_host', $this->sc->rec['aprsprefs_host']);
        $this->sc->rec['aprsprefs_port'] = $this->frm->text('aprsprefs_port', $this->sc->rec['aprsprefs_port']);
        $this->sc->rec['aprsprefs_mycall'] = $this->frm->text('aprsprefs_mycall', $this->sc->rec['aprsprefs_mycall']);
        $this->sc->rec['aprsprefs_client'] = $this->frm->text('aprsprefs_client', $this->sc->rec['aprsprefs_client']);
        $this->sc->rec['aprsprefs_pass'] = $this->frm->text('aprsprefs_pass', $this->sc->rec['aprsprefs_pass']);
        $this->sc->rec['aprsprefs_filter'] = $this->frm->text('aprsprefs_filter', $this->sc->rec['aprsprefs_filter'], 80, array('size' => 80));
        $this->sc->rec['aprsprefs_logging'] = $this->frm->radio_switch('aprsprefs_logging', ($this->sc->rec['aprsprefs_logging'] == 1?true:false) , 'Active', 'Inactive', $options);
        $this->sc->rec['aprsprefs_debug'] = $this->frm->radio_switch('aprsprefs_debug', ($this->sc->rec['aprsprefs_debug'] == 1?true:false) , 'Active', 'Inactive', $options);
        $this->sc->rec['aprsprefs_beaconactive'] = $this->frm->radio_switch('aprsprefs_beaconactive', ($this->sc->rec['aprsprefs_beaconactive'] == 1?true:false) , 'Active', 'Inactive', $options);
        $this->sc->rec['aprsprefs_beacontext'] = $this->frm->text('aprsprefs_beacontext', $this->sc->rec['aprsprefs_beacontext']);
        $this->sc->rec['aprsprefs_beaconcallsign'] = $this->frm->text('aprsprefs_beaconcallsign', $this->sc->rec['aprsprefs_beaconcallsign']);
        $this->sc->rec['aprsprefs_beaconpass'] = $this->frm->text('aprsprefs_beaconpass', $this->sc->rec['aprsprefs_beaconpass']);
        $this->sc->rec['aprsprefs_beaconport'] = $this->frm->text('aprsprefs_beaconport', $this->sc->rec['aprsprefs_beaconport']);
        $this->sc->rec['aprsprefs_beaconlat'] = $this->frm->text('aprsprefs_beaconlat', $this->sc->rec['aprsprefs_beaconlat']);
        $this->sc->rec['aprsprefs_beaconlon'] = $this->frm->text('aprsprefs_beaconlon', $this->sc->rec['aprsprefs_beaconlon']);
        $this->sc->rec['aprsprefs_beaconalt'] = $this->frm->text('aprsprefs_beaconalt', $this->sc->rec['aprsprefs_beaconalt']);
        $option_array = array('1' => '1 minute',
            '2' => '2 minutes',
            '3' => '3 minutes',
            '4' => '4 minutes',
            '5' => '5 minutes',
            '10' => '10 minutes',
            '15' => '15 minutes',
            '20' => '20 minutes',
            '25' => '25 minutes',
            '30' => '30 minutes',
            '45' => '45 minutes',
            '60' => '60 minutes');
        $this->sc->rec['aprsprefs_beaconinterval'] = $this->frm->select('aprsprefs_beaconinterval', $option_array, $this->sc->rec['aprsprefs_beaconinterval'], array(), false);
        $this->sc->rec['prefcancel'] = $this->frm->button('aprsprefcancel', 'cancel', 'cancel', 'Cancel', $options);
        $this->sc->rec['prefupdate'] = $this->frm->button('aprsprefupdate', 'update', 'update', 'Update', $options);
        $tabsArray[0] = array('caption' => 'General', 'text' => $this->tp->parseTemplate($this->template->prefsTab1(), false, $this->sc));
        $tabsArray[1] = array('caption' => 'Beacon', 'text' => $this->tp->parseTemplate($this->template->prefsTab2(), false, $this->sc));

        $this->sc->rec['tabs'] = $this->frm->tabs($tabsArray);

        $text .= $this->tp->parseTemplate($this->template->aprsPrefsEdit(), false, $this->sc);

        $text .= $this->frm->close();
        $this->ns->tablerender("APRS Calls", $this->mes->render() . $text);
    }
    /**
    * aprs::prefsSave()
    *
    * @return
    */
    private function prefsSave(){
        if ($_GET['aprsprefs_id'] == 0){
            $qry = "INSERT INTO #aprsprefs
		(aprsprefs_id,
		aprsprefs_host,
		aprsprefs_port,
		aprsprefs_mycall,
		aprsprefs_client,
		aprsprefs_pass,
		aprsprefs_filter,
		aprsprefs_logging,
		aprsprefs_debug,
		aprsprefs_beaconactive,
		aprsprefs_beaconcallsign,
		aprsprefs_beaconpass,
		aprsprefs_beaconport,
		aprsprefs_beaconlat,
		aprsprefs_beaconlon,
		aprsprefs_beaconalt,
		aprsprefs_beacontext,
		aprsprefs_beaconinterval) VALUES (
		1,
		'{$_GET['aprsprefs_host']}',
		'{$_GET['aprsprefs_port']}',
		'{$_GET['aprsprefs_mycall']}',
		'{$_GET['aprsprefs_client']}',
		'{$_GET['aprsprefs_pass']}',
		'{$_GET['aprsprefs_filter']}',
		'{$_GET['aprsprefs_logging']}',
		'{$_GET['aprsprefs_debug']}',
		'{$_GET['aprsprefs_beaconactive']}',
		'{$_GET['aprsprefs_beaconcallsign']}',
		'{$_GET['aprsprefs_beaconpass']}',
		'{$_GET['aprsprefs_beaconport']}',
		'{$_GET['aprsprefs_beaconlat']}',
		'{$_GET['aprsprefs_beaconlon']}',
		'{$_GET['aprsprefs_beaconalt']}',
		'{$_GET['aprsprefs_beacontext']}',
		'{$_GET['aprsprefs_beaconinterval']}'
		)";
            $res = $this->sql->gen($qry, true);
            if ($res === false){
                $this->mes->addError("Failed to create record");
            }else{
                $this->mes->addSuccess("Record created OK");
            }
        }else{
            $qry = "UPDATE #aprsprefs set
	aprsprefs_host			=	'{$_GET['aprsprefs_host']}',
	aprsprefs_port			=	'{$_GET['aprsprefs_port']}',
	aprsprefs_mycall		=	'{$_GET['aprsprefs_mycall']}',
	aprsprefs_client		=	'{$_GET['aprsprefs_client']}',
	aprsprefs_pass			=	'{$_GET['aprsprefs_pass']}',
	aprsprefs_filter		=	'{$_GET['aprsprefs_filter']}',
	aprsprefs_logging		=	'{$_GET['aprsprefs_logging']}',
	aprsprefs_debug			=	'{$_GET['aprsprefs_debug']}',
	aprsprefs_beaconactive	=	'{$_GET['aprsprefs_beaconactive']}',
	aprsprefs_beaconcallsign=	'{$_GET['aprsprefs_beaconcallsign']}',
	aprsprefs_beaconpass	=	'{$_GET['aprsprefs_beaconpass']}',
	aprsprefs_beaconport	=	'{$_GET['aprsprefs_beaconport']}',
	aprsprefs_beaconlat		=	'{$_GET['aprsprefs_beaconlat']}',
	aprsprefs_beaconlon		=	'{$_GET['aprsprefs_beaconlon']}',
	aprsprefs_beaconalt		=	'{$_GET['aprsprefs_beaconalt']}',
	aprsprefs_beacontext	=	'{$_GET['aprsprefs_beacontext']}',
	aprsprefs_beaconinterval=	'{$_GET['aprsprefs_beaconinterval']}'
		WHERE aprsprefs_id=1";
            $res = $this->sql->gen($qry, false);
            if ($res === false){
                $this->mes->addError("Failed to save settings");
            }else{
                $this->mes->addSuccess("Settings saved OK");
            }
        }
        $this->action = 'calls';
    }
    /**
    * aprs::passcode()
    *
    * @return
    */
    private function passcode(){
        $this->action = 'passcode';
        $bread[] = array('url' => e_SELF, 'text' => 'APRS');
        $bread[] = array('url' => e_SELF . "?aprsFrom={$this->callFrom}&aprsOrder={$this->order}&aprsAction=calls", 'text' => 'Call List');
        $this->sc->rec['bread'] = $this->frm->breadcrumb($bread);
        $text .= $this->frm->open("aprsPasscode", "get", e_SELF);
        $text .= $this->frm->hidden('aprsAction', $this->action);
        $text .= $this->frm->hidden('aprscallFrom', $this->callFrom);
        $text .= $this->frm->hidden('aprsOrder', $this->order);
        $text .= $this->frm->hidden('aprsprefs_id', $this->aprsprefs_id);
        $passmade = '-';
        require_once("aprs_pass_class.php");
        $callin = (isset($_GET['aprspasscall'])?strtoupper($_GET['aprspasscall']):'');
        if (!empty($callin)){
            $passmade = aprsPass::aprsPassGen($callin);
            $this->sc->rec['passmade'] = $passmade;
        }else{
            $passmade;
        }

        $this->sc->rec['passcall'] = $this->frm->text('aprspasscall', $callin);
        $this->sc->rec['passmake'] = $this->frm->button('aprspassmake', 'update', 'update', 'Generate', $options);

        $text .= $this->tp->parseTemplate($this->template->aprsPassGen(), false, $this->sc);

        $text .= $this->frm->close();
        $this->ns->tablerender("APRS Calls", $this->mes->render() . $text);
    }
    /**
    * aprs::aprsNotPermitted()
    *
    * @return
    */
    public function aprsNotPermitted(){
        $this->mes->addWarning("Unauthorised Access");
        // $text .= $this->tp->parseTemplate($this->template->aprsNotPermitted(), false, $this->sc);
        $text = 'You do not have permission to access this part of the web site. Contact the site administrator if you should have access';
        $this->ns->tablerender("APRS", $this->mes->render() . $text);
    }
    /**
    * aprs::aprsMain()
    *
    * @return
    */
    function aprsMain(){
        $this->action = varset($_GET['aprsAction'], 'list');
        $this->opts = $_GET['aprsopSelector'];
        // get the current order from session
        // if not exist then default to desc
        // then check if get is set if so use that then set the session
        $this->order = varset($_SESSION['aprsOrder'], 'desc');
        if (isset($_GET['aprsOrder'])){
            $this->order = $_GET['aprsOrder'];
        }
        $_SESSION['aprsOrder'] = $this->order;
        // the same for admin of buddy list
        $this->callOrder = varset($_SESSION['aprsCallOrder'], 'desc');
        if (isset($_GET['aprsCallOrder'])){
            $this->CallOrder = $_GET['aprsCallOrder'];
        }
        $_SESSION['aprsCallOrder'] = $this->CallOrder;
        // get other parameters if any
        $this->callsign = $_GET['aprsCallsign'];
        $this->page = (int)$_GET['aprsPage'];
        $this->from = (int)$_GET['aprsFrom'];
        $this->callID = intval($_GET['aprsCallID']);
        $selected = strtoupper($csign);
        $opselected = strtolower($this->opts);

        $this->callPage = (int)$_GET['aprscallPage'];
        $this->callFrom = (int)$_GET['aprscallFrom'];
        if (isset($_GET['aprscallscancel'])){
            $this->action = 'calls';
        }
        if (isset($_GET['aprscallsupdate'])){
            $this->callSave();;
            $this->action = 'calls';
        }
        if (isset($_GET['aprscallsdelete'])){
            $this->callDelok();
            $this->action = 'calls';
        }

        if (isset($_GET['aprsprefcancel'])){
            $this->action = 'calls';
        }
        if (isset($_GET['aprsprefupdate'])){
            $this->prefsSave();;
            $this->action = 'settings';
        }
        if ($this->admin){
            switch ($this->action){
                case 'passcode':
                    $this->passcode();
                    break;
                case 'settings':
                    $this->editPrefs();
                    break;
                case 'edit':
                case 'create':
                    $this->callEdit();;
                    break;
                case 'delete':
                    $this->callDelete();
                    break;
                case 'calls':
                    $this->callMain();
                    break;
                case 'list':
                case 'trackfull':
                case 'messages':
                case 'status':
                case 'weather':
                case 'trackchange':
                default:
                    $this->aprsRecords(); ;
            } // switch($this->action)
        }elseif ($this->viewer){
            switch ($this->action){
                case 'list':
                case 'trackfull':
                case 'messages':
                case 'status':
                case 'weather':
                case 'trackchange':
                default:
                    $this->aprsRecords(); ;
            } // switch($this->action)
        }else{
            $this->aprsNotPermitted();
        }
    }
    /**
    * aprs::status()
    *
    * @return
    */
    private function status(){
        return;
        $callsign = 'G4HDU-2';
        $passcode = '10642';
        $aprs_altinfeet = 208;
        // W is the WX icon. See http://wa8lmf.net/aprs/APRS_symbols.htm
        // Use GPS coordinate format, see http://www.csgnetwork.com/gpscoordconv.html
        $aprs_coord = '5351.53N/00256.17W';
        $aprs_comment = 'G4HDU Testing APRS Send';
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if ($socket){
            $result = socket_connect($socket, 'rotate.aprs2.net', 14580);
            if ($result){
                // Authenticating
                $tosend = "user $callsign pass $passcode\n";
                socket_write($socket, $tosend, strlen($tosend));
                $authstartat = time();
                $authenticated = false;
                while ($msgin = socket_read($socket, 1000, PHP_NORMAL_READ)){
                    if (strpos($msgin, "$callsign verified") !== false){
                        $authenticated = true;
                        break;
                    }
                    // Timeout handling
                    if (time() - $authstartat > 5)
                        break;
                }
                if ($authenticated){
                    // Sending position
                    $tosend = "$callsign>APRS,TCPIP*:>" . date('Hmi') . "z$aprs_comment\n";
                    socket_write($socket, $tosend, strlen($tosend));
                    echo $tosend;
                }
            }
            socket_close($socket);
        }
    }
    /**
    * aprs::beacon()
    *
    * @return
    */
    private function beacon(){
        // not yet implemented
        $callsign = 'G4HDU-2';
        $passcode = '10642';
        $aprs_altinfeet = 208;
        // W is the WX icon. See http://wa8lmf.net/aprs/APRS_symbols.htm
        // Use GPS coordinate format, see http://www.csgnetwork.com/gpscoordconv.html
        $aprs_coord = '5351.53N/00256.17W';
        $aprs_comment = 'G4HDU Testing APRS Send';
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if ($socket){
            $result = socket_connect($socket, 'rotate.aprs2.net', 14580);
            if ($result){
                // Authenticating
                $tosend = "user $callsign pass $passcode\n";
                socket_write($socket, $tosend, strlen($tosend));
                $authstartat = time();
                $authenticated = false;
                while ($msgin = socket_read($socket, 1000, PHP_NORMAL_READ)){
                    if (strpos($msgin, "$callsign verified") !== false){
                        $authenticated = true;
                        break;
                    }
                    // Timeout handling
                    if (time() - $authstartat > 5)
                        break;
                }
                if ($authenticated){
                    // Sending position
                    $tosend = "$callsign>APRS,TCPIP*:>" . date('Hmi') . "z{$aprs_coord}k=" .
                    str_pad($aprs_altinfeet, 6, '0', STR_PAD_LEFT) . " $aprs_comment\n";
                    socket_write($socket, $tosend, strlen($tosend));
                    echo $tosend;
                }
            }
            socket_close($socket);
        }
    }
    private function aprsIcon($symbol = '/', $size = 24){
    	return;
        switch ($size){
            case '64':
                $width = 64;
                $height = 64;
                $mult = 63;
                $imgWidth = 60;
                $imgHeight = 60;
                $padding = 2;
                break;
            case '48':
                $width = 48;
                $height = 48;
                $mult = 42;
                $imgWidth = 41;
                $imgHeight = 41;
                $offset = 1;
                $padding = 3;
                break;
            case '24':
            default:
                $width = 24;
                $height = 24;
                $mult = 21;
                $imgWidth = 19;
                $imgHeight = 19;
                $offset = 1;
                $paddingLeft = 3;
                $paddingRight = 2;
                $paddingTop = 2;
                $paddingBottom = 3;
        } // switch
        $slash = substr($symbol, 0, 1);
        $icon = substr($symbol, 1, 1);
        $iconval = ord($icon) - 33;
        $row = intval($iconval / 16);
        $rowPixel = ($row * $mult) + $offset ;
        $col = $iconval - ($row * 16);
        $colPixel = ($col * $mult) + $offset;

        if ($slash != '/'){
            // alternate set
            $retval = "
		<div style='
			display:inline-block;
			width:{$width}px;
			height:{$height}px;
			background-color:#ffff00;
			padding:{$paddingTop}px {$paddingRight}px {$paddingBottom}px {$paddingLeft}px;
			margin:0px;'>
			<img src='images/blank.png' style='
				width: {$imgWidth}px;
    			height: {$imgHeight}px;
    			margin:0px;
    			padding:0px;
    			background: url(images/sprite_sec_{$mult}.png) -{$colPixel}px -{$rowPixel}px  ; ' />
   	 	</div>";
        }else{
            // standard set
            $retval = "
		<div style='
			display:inline-block;
			width:{$width}px;
			height:{$height}px;
			background-color:#ffff00;
			padding:{$paddingTop}px {$paddingRight}px {$paddingBottom}px {$paddingLeft}px;
			margin:0px;'>
			<img src='images/blank.png' style='
				width: {$imgWidth}px;
    			height: {$imgHeight}px;
    			margin:0px;
    			padding:0px;
    			background: url(images/sprite_pri_{$mult}.png) -{$colPixel}px -{$rowPixel}px  ; ' />
   	 	</div>";
        }
        print $retval . '<br /><br />';
        return $retval;
    }
}

?>