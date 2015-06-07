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



function eventControl(action, deviceID, description) {
	myXHRobjekt = createXHRobjekt(); 

	if (myXHRobjekt) {
		myXHRobjekt.onreadystatechange = function() { 
			if(ajaxRequest.readyState == 4){
				if (action == 1) {
						image = document.getElementById('imgDisp' + deviceID);
						image.src = "images/metro_black/check.png";
						image.setAttribute("row-action", "0");
						
						image2 = document.getElementById('imgDisp2' + deviceID);
						image2.src = "images/metro_black/check.png";
						image2.setAttribute("row-action", "0");

				} else {
						image = document.getElementById('imgDisp' + deviceID);
						image.src = "images/metro_black/cancel.png";
						image.setAttribute("row-action", "1");
						
						image2 = document.getElementById('imgDisp2' + deviceID);
						image2.src = "images/metro_black/cancel.png";
						image2.setAttribute("row-action", "1");

				}
			} 
		}

		ajaxRequest.open("GET", "ajax_event_control.php?action=" + action + "&id=" + deviceID + "&description=" + description + "&&rand=" + Math.random()*9999, true);
		ajaxRequest.send(null); 
	}
}


function scheControl(action, deviceID) {
	myXHRobjekt = createXHRobjekt(); 

	if (myXHRobjekt) {
		myXHRobjekt.onreadystatechange = function() { 
			if(ajaxRequest.readyState == 4){
				if (action == 1) {
						image3 = document.getElementById('imgDisp3' + deviceID);
						image3.src = "images/metro_black/check.png";
						image3.setAttribute("row-action", "0");
						
						image4 = document.getElementById('imgDisp4' + deviceID);
						image4.src = "images/metro_black/check.png";
						image4.setAttribute("row-action", "0");

				} else {
						image3 = document.getElementById('imgDisp3' + deviceID);
						image3.src = "images/metro_black/cancel.png";
						image3.setAttribute("row-action", "1");
						
						image4 = document.getElementById('imgDisp4' + deviceID);
						image4.src = "images/metro_black/cancel.png";
						image4.setAttribute("row-action", "1");

				}
			} 
		}

		ajaxRequest.open("GET", "ajax_sche_control.php?action=" + action + "&id=" + deviceID + "&&rand=" + Math.random()*9999, true);
		ajaxRequest.send(null); 
	}
}

