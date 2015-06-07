function createXHRobjekt() {
	var XHRobjekt = null;
	
	try {
		ajaxRequest = new XMLHttpRequest(); // Firefox, Opera, ...
	} catch(err1) {
		try {
			ajaxRequest = new ActiveXObject("Microsoft.XMLHTTP"); // Noen IE v.
		} catch(err2) {
			try {
					ajaxRequest = new ActiveXObject("Msxml2.XMLHTTP"); // Noen IE v.
			} catch(err3) {
				ajaxRequest = false;
			}
		}
	}
	return ajaxRequest;
}



function mailControl(state, deviceID) {
	myXHRobjekt = createXHRobjekt(); 

	if (myXHRobjekt) {
		myXHRobjekt.onreadystatechange = function() { 
			if(ajaxRequest.readyState == 4){
				if (state == 1) {
					//$('#btn_' + deviceID + "_on").addClass('btn-success active');
					//$('#btn_' + deviceID + "_off").removeClass('btn-default active');
						image = document.getElementById('imgDisp' + deviceID);
						image.src = "images/metro_black/check.png";
						image.setAttribute("row-action", "0");

				} else {
					//$('#btn_' + deviceID + "_on").removeClass('btn-success active');
					//$('#btn_' + deviceID + "_off").addClass('btn-default active');
						image = document.getElementById('imgDisp'  + deviceID);
						image.src = "images/metro_black/cancel.png";
						image.setAttribute("row-action", "1");

				}
			} 
		}

		ajaxRequest.open("GET", "index.php?page=settings_exec&action=mailcontrol&state=" + state + "&id=" + deviceID + "&&rand=" + Math.random()*9999, true);
		ajaxRequest.send(null); 
	}
}


function pushControl(state, deviceID) {
	myXHRobjekt = createXHRobjekt(); 

	if (myXHRobjekt) {
		myXHRobjekt.onreadystatechange = function() { 
			if(ajaxRequest.readyState == 4){
				if (state == 1) {
					//$('#btn_' + deviceID + "_on").addClass('btn-success active');
					//$('#btn_' + deviceID + "_off").removeClass('btn-default active');
						image = document.getElementById('imgDisp2' + deviceID);
						image.src = "images/metro_black/check.png";
						image.setAttribute("row-action", "0");

				} else {
					//$('#btn_' + deviceID + "_on").removeClass('btn-success active');
					//$('#btn_' + deviceID + "_off").addClass('btn-default active');
						image = document.getElementById('imgDisp2'  + deviceID);
						image.src = "images/metro_black/cancel.png";
						image.setAttribute("row-action", "1");

				}
			} 
		}

		ajaxRequest.open("GET", "index.php?page=settings_exec&action=pushcontrol&state=" + state + "&id=" + deviceID + "&&rand=" + Math.random()*9999, true);
		ajaxRequest.send(null); 
	}
}