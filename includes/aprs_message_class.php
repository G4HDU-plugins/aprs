<?php
/**
 * aprsTX
 *
 * @package
 * @author barry
 * @copyright Copyright (c) 2015
 * @version $Id$
 * @access public
 */
class aprsTX{
    private $callsign = "CALLSIGN";
    private $passcode = 0;
	private $latitude;
	private $longitude;
	private $altitude;
	private $iaru;
    private $destinations = array();
    private $message = "";
    private $socket;
    private $aprs_comment = "";
    public $authenticated = false;
    private $timeout = 5;
    private $aprsGateway = "euro.aprs2.net";
    private $aprsPort = 14580;
    /**
     * aprsTX::__construct()
     *
     * @param mixed $parms
     */
    function __construct($parms){
    	// set defaults when creating object
    	$this->callsign="CALLSIGN";
    	$this->passcode=0;
    	$this->latitude=0;
    	$this->longitude=0;
    	$this->altitude=0;
    	$this->iaru="IO83MM";
    	$this->destinations=$this->callsign;
    	$this->timeout=5;
    	$this->aprsGateway="euro.aprs2.net";
    	$this->aprsPort=14580;
    	if (is_array($parms)) {
    		// if parameters passed in as an array
    		$this->callsign=$parms['callsign'];
    		$this->passcode=$parms['passcode'];
    		$this->latitude=$parms['latitude'];
    		$this->longitude=$parms['longitude'];
    		$this->altitude=$parms['altitude'];
    		$this->iaru=$parms['iaru'];
    		$this->destinations=$parms['destinations'];
    		$this->timeout=$parms['timeout'];
    		$this->aprsGateway=$parms['aprsGateway'];
    		$this->aprsPort=$parms['aprsPort'];
    	}else if (is_object($parms)) {
    		// if parameter is an object
    		$this->callsign=$parms->callsign;
    		$this->passcode=$parms->passcode;
    		$this->latitude=$parms->latitude;
    		$this->longitude=$parms->longitude;
    		$this->altitude=$parms->altitude;
    		$this->iaru=$parms->iaru;
    		$this->destinations=$parms->destinations;
    		$this->timeout=$parms->timeout;
    		$this->aprsGateway=$parms->aprsGateway;
    		$this->aprsPort=$parms->aprsPort;
    	}
    }
    /**
     * aprsTX::setCallsign()
     *
     * @param mixed $callsign
     * @return
     * @todo do a validation check .
     */
    public function setCallsign($callsign){
        $this->callsign = str_pad(strtoupper($callsign),7," ");
    }
    /**
     * aprsTX::setPasscode()
     *
     * @param mixed $passcode
     * @return
     * @todo do a validation check .
     */
    public function setPasscode($passcode){
        $this->passcode = $passcode;
    }
    /**
     * aprsTX::setDestinations()
     *
     * @param mixed $destinations
     * @return
     * @todo do a validation check .
     */
    public function setDestinations($destinations){
        $this->destinations = $destinations;
    }
    /**
     * aprsTX::setComment()
     *
     * @param mixed $comment
     * @return
     * @todo do a validation check .
     */
    public function setComment($comment){
        $this->aprs_comment = $comment;
    }
    /**
     * aprsTX::setTimeout()
     *
     * @param mixed $timeout
     * @return
     * @todo do a validation check .
     */
    public function setTimeout($timeout){
        $this->timeout = $timeout;
    }
    /**
     * aprsTX::setAprsGateway()
     *
     * @param mixed $gateway
     * @return
     * @todo do a validation check .
     */
    public function setAprsGateway($gateway){
        $this->aprsGateway = $gateway;
    }
    /**
     * aprsTX::setPort()
     *
     * @param mixed $port
     * @return
     * @todo do a validation check .
     */
    public function setPort($port){
        $this->aprsPort = $port;
    }
    /**
     * aprsTX::createSocket()
     *
     * @return
     */
    public function createSocket(){
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if ($this->socket){
        //	var_dump($this->socket);
            $result = socket_connect($this->socket, $this->aprsGateway, $this->aprsPort);
      //  	var_dump($result);
            if ($result){
                // Authenticating
        //    	var_dump($this->callsign);
                $tosend = "user {$this->callsign} pass {$this->passcode}\n";
                socket_write($this->socket, $tosend, strlen($tosend));
                $authstartat = time();
                $authenticated = false;
                while ($msgin = socket_read($this->socket, 1000, PHP_NORMAL_READ)){
                    if (strpos($msgin, "{$this->callsign} verified") !== false){
                        $this->authenticated = true;
                        break;
                    }
                    // Timeout handling
                    if (time() - $authstartat > $this->timeout){
                        $this->authenticated = false;
                        break;
                    }
                }
            }else{
                $this->authenticated = false;
            }
        }
        return $this->authenticated;
    }
    /**
     * aprsTX::closeSocket()
     *
     * @return
     */
    public function closeSocket(){
        socket_close($this->socket);
    }
    public function sendStatus(){
    }
    /**
     * aprsTX::sendMessage()
     *
     * @param mixed $message
     * @return
     */
    public function sendMessage($message){
    	$this->createSocket();
    	if ($this->authenticated){
    		foreach ($this->destinations as $toCall){
    			$tosend = "{$this->callsign}>APRS,TCPIP*::" . str_pad($toCall, 9, ' ') . ":{$message}\n";
    			socket_write($this->socket, $tosend, strlen($tosend));
    		}
    	}
    	$this->closeSocket();
    }
    public function sendWX(){
    }
    public function sendPosition(){
    }
	public function sendDF(){}
	public function sendObject(){}
	public function sendTelemetry(){}
	public function sendQueries(){}

	public function sendOther(){}
}
$settings['callsign']="G4HDU"  ;
$settings['passcode']= 10642 ;
$settings['latitude']= "5351.53N" ;
$settings['longitude']= "02936.1E" ;
$settings['altitude']= "000060" ;
$settings['iaru']= "IO83MM" ;
$settings['destinations']= array("G4HDU") ;
$settings['timeout']= 5 ;
$settings['aprsGateway']= "euro.aprs2.net" ;
$settings['aprsPort']= 14580 ;
#error_reporting(0);
#require_once("base91_class.php");
#echo base91::base91_encode("Barry");
#echo base91::base91_decode("xDG3l`A");
#$aprs=new aprsTX($settings);
#$message="G4HDU Testing from PHP hopefully working";
//$aprs->createSocket();
//var_dump($aprs->authenticated);
//$aprs->closeSocket();
#$aprs->sendMessage($message);
/*
  Byte(s)	Description
   1		Flag			0x7e	Frame separator
   7		Destination
   7		Source
   0 - 56	Digipeaters				From zero to 8 digipeater callsigns
   1		Control Field 	0x03
   1		Protocol ID 	0xf0
   1		Data type
   1-255	Information
   2		FCS						Frame check Sequence

*/
?>
