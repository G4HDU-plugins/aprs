function aprsSaveCookie(LastTab){
	document.cookie="aprsLastTab="+LastTab+"; path=/";
	aprsSaveLastTime();
}
function aprsSaveLastTime(){
	var tnow=Math.floor(Date.now() / 1000);
	//document.cookie="aprsLastTabTime="+tnow+"; path=/";
}

function transitionBox(from, to) {
	function next() {
		var nextTo;
		if (to.is(":last-child")) {
			nextTo = to.closest(".boxes").children("div").first();
		} else {
			nextTo = to.next();
		}
		to.fadeIn(500, function () {
			setTimeout(function () {
				transitionBox(to, nextTo);
			}, 5000);
		});
	}

	if (from) {
		from.fadeOut(500, next);
	} else {
		next();
	}
}
$(document).ready(function(){

	var allBoxes = $("div.boxes").children("div");
	transitionBox(null, allBoxes.first());

	$('#aprsContainer').mouseenter(function(){
	//	clearInterval(aprsInterval);
	});
	$('#aprsContainer').mouseleave(function(){
	//	aprsInterval=setInterval(function(){faders(1);} , 5000);
	});
	$('.aprsColumn-one').click(function(){
	//	aprsInterval=setInterval(function(){fadersJump(-1);} , 5000);
	});
	$('.aprsColumn-three').click(function(){
	//	aprsInterval=setInterval(function(){fadersJump(1);} , 5000);
	});
	$('#aprsTab1').click(function(){
		aprsSaveCookie('1');
	});

	$('#aprsTab2').click(function(){
		aprsSaveCookie('2');
	});

	$('#aprsTab3').click(function(){
		aprsSaveCookie('3');
	});

	$('#aprsTab4').click(function(){
		aprsSaveCookie('4');
	});
	$('#aprsPrefForm').submit(function(){
		aprsSaveLastTime();
		clientSettings(1);
		serverSettings(1);
	});
	$('#aprsaction').change(function(){
		$('#aprstracking').submit();
	});
	$('#aprscallsign').change(function(){
		$('#aprstracking').submit();
	});

	$('#aprsSorter').click(function(){
		console.log('here');
		if($('[name=aprsOrder]').val()=='asc'){
			$('[name=aprsOrder]').val('desc');
		}else{
			$('[name=aprsOrder]').val('asc');
		}
		$('[name=aprsFrom]').val(0);
		$('#aprstracking').submit();
	});

	$('#aprsCallsSorter').click(function(){
		console.log('there');
		if($('[name=aprsOrder]').val()=='asc'){
			$('[name=aprsOrder]').val('desc');
		}else{
			$('[name=aprsOrder]').val('asc');
		}
		$('[name=aprsFrom]').val(0);
		$('#aprscallform').submit();
	});

	$('#aprsmenuselector').change(function(){
		$('#aprsmenutracking').submit();
	});

	$('#aprs-serverdb').change(function(){
		serverSettings(this.value);
	});
	$('#aprs-clientdb').change(function(){
		clientSettings(this.value);
	});
	clientSettings($('#aprs-clientdb').val());
	serverSettings($('#aprs-serverdb').val());
});
function clientSettings(currentVal){
	if (currentVal==0) {
		$('#aprs-clienthost').prop('disabled', true);
		$('#aprs-clientport').prop('disabled', true);
		$('#aprs-clientuser').prop('disabled', true);
		$('#aprs-clientpass').prop('disabled', true);
		$('#aprs-clientprefix').prop('disabled', true);
		$('#aprs-clienttableprefix').prop('disabled', true);
	}else{
		$('#aprs-clienthost').prop('disabled', false);
		$('#aprs-clientport').prop('disabled', false);
		$('#aprs-clientuser').prop('disabled', false);
		$('#aprs-clientpass').prop('disabled', false);
		$('#aprs-clientprefix').prop('disabled', false);
		$('#aprs-clienttableprefix').prop('disabled', false);
	}
}
function serverSettings(currentVal){
	if (currentVal==0) {
		$('#aprs-serverhost').prop('disabled', true);
		$('#aprs-serverport').prop('disabled', true);
		$('#aprs-serveruser').prop('disabled', true);
		$('#aprs-serverpass').prop('disabled', true);
		$('#aprs-serverprefix').prop('disabled', true);
		$('#aprs-servertableprefix').prop('disabled', true);
	}else{
		$('#aprs-serverhost').prop('disabled', false);
		$('#aprs-serverport').prop('disabled', false);
		$('#aprs-serveruser').prop('disabled', false);
		$('#aprs-serverpass').prop('disabled', false);
		$('#aprs-serverprefix').prop('disabled', false);
		$('#aprs-servertableprefix').prop('disabled', false);
	}
}