function increaseAvailability(slot) {

		$.getJSON('/Ajax/IncreaseAvailability/'+slot+'/',
                function(ReturnValues){
			if(true == ReturnValues['success']) {
				
				$("#slot-"+slot).html(ReturnValues['NewAvailability']);
			}
			else{ 
				$("#error-message-"+slot).show().delay(3000).fadeOut();;
                		$("#error-message-"+slot).html(ReturnValues['ErrorMessage']);
                        }
                });
		return event.preventDefault();
		return false;
}

function decreaseContractorAvailability(slot) {


		$.getJSON('/Ajax/decreasecontractoravailability/'+slot+'/',
                function(ReturnValues){
			if(true == ReturnValues['success']) {
				
				$("#slot-"+slot).html(ReturnValues['NewAvailability']);
			}
			else{ 

				$("#error-message-"+slot).show().delay(3000).fadeOut();;
                		$("#error-message-"+slot).html(ReturnValues['ErrorMessage']);
                        }
		return false;
                });
}

function decreaseAvailability(slot) {

		$.getJSON('/Ajax/DecreaseAvailability/'+slot+'/',
                function(ReturnValues){
			if(true == ReturnValues['success']) {
				
				$("#slot-"+slot).html(ReturnValues['NewAvailability']);
			}
			else{ 

				$("#error-message-"+slot).show().delay(3000).fadeOut();;
                		$("#error-message-"+slot).html(ReturnValues['ErrorMessage']);
                        }
		return false;
                });
}
function updateReadyStatus() {
	

	$.getJSON('/Ajax/UpdateReadyStatus/',
		function(ReturnValues){
			if(true == ReturnValues['success']) {

				$("#ready_status").html(ReturnValues['ReadyMessage']);
			}
			else {

				$("#error-message").html(ReturnValues['ErrorMessage']);	
			     }

		return false;
		});
}

