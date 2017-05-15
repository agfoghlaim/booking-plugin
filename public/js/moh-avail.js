window.onload = function(){
	//localStorage.clear();
	console.log("hello from onload");
var dateSubmit = document.getElementById('date-submit');
console.log("got date submit");
	dateSubmit.addEventListener('click', function(e){
	e.preventDefault();

		if(arrivalInFuture()){	
			console.log("checking if dates are ok");
			var arrive = document.getElementById('arrive').value;
			var depart = document.getElementById('depart').value;
			var numNights = document.getElementById('num-nights');
			var diff = Math.round(Math.abs(new Date(depart.replace(/-/g,'/')) - new Date(arrive.replace(/-/g,'/')))/(1000*60*60*24));
			var formData = {arrive:arrive, 
							depart:depart
						};

			localStorage.setItem('arrive', JSON.stringify(arrive));
			localStorage.setItem('depart', JSON.stringify(depart));
			localStorage.setItem('numNights', JSON.stringify(diff));
			console.log("num nights ");
			mohCheckAvail(formData, 'moh_check_avail_action');
		}
	});

	/*
	Check Availabity Ajax
	*/

		function mohCheckAvail(formData, action){
			//console.log("mohCheckAvail");
		$.ajax({
			type:'POST',
			dataType: 'JSON',
			url: myAjax.ajaxurl,
			data: {
				action: 'moh_check_avail_action',
				data: formData,
				submission: document.getElementById('xyz').value,
				security: myAjax.checkAvail_security
			},
			success: function(response){
				$('div#show-rooms-info').html('');
					console.log(response.data[0].room_type);
						for(i in response.data){ 
						
							$('div#show-rooms-info').append(
															response.data[i].room_type,
															response.data[i].room_number,
															response.data[i].room_thumbnail,
															response.data[i].room_rate, 
															response.data[i].room_description,
															response.data[i].room_book_button,
															response.data[i].room_remove_button,
															response.data[i].room_show_booking_form
															
															);
						}
						//jQuery( document.body ).trigger( 'post-load' );
			},
			error: function(response){
				$('div#show-rooms-info').append("<p>Sorry, an error occured</p>");
			}
		});
	};

}//end window onload

/*
CHECK AVAILABITY FORM VALIDATION FUNCTIONS
*/
function arrivalInFuture(){
	arrive = document.getElementById('arrive').value;
	depart = document.getElementById('depart').value;
	var arr = new Date(arrive);
	var dep = new Date(depart);
	var now = new Date();
	arrMsg = $('p#arr-err');
	arrMsg.html("");
	var sixMonths = now.setMonth(now.getMonth() + 6);
	console.log(arr);
	
	if( checkDateFormat(arrive) && checkDateFormat(depart) ){
		if(arr > new Date() && arr < new Date(sixMonths)){
			//console.log("pass 6 months");
			//if it passes above check before dept
			if(arr<dep){
				//console.log("arrival is before dept");
				//if arr passes before dept check 2 weeks
				if(dep-arr <=1209600000){
					return true;
					//console.log("less than 2 weeks");
				}else{
					arrMsg.html('Sorry we only accept bookings for a maximum of 14 days.');
				}
				
			}else{
				arrMsg.html('Please check Arrival Date is before Departure Date');
			}
		}else{
			//console.log("fail");
			arrMsg.html('Please enter a valid Arrival Date, date must be in the future and within 6 months');
		}
	}//end check dateformat()
	else{
		//console.log("date format fail");
		arrMsg.html('Please enter date in format yyyy-mm-dd.');
	}
}




function checkDateFormat(theDt){
	var yr = theDt.substring(0,4);
	var mt= theDt.substring(5,7);
	var dt=theDt.substring(8,10); 
	var hyp = theDt.substring(4,5) + theDt.substring(7,8);
		if(hyp == '--' && !isNaN(yr) && !isNaN(mt) && !isNaN(dt)){
			return true;
		}else{
			return false;
		}
}


	/*
	======================================
	=====================================
	=======  FOR SELECTING ROOMS  =========
	=====================================
	=======================================
	/*
	

/*================================================================================*/ 
/*===============================  SELECT ROOMS  ==============================*/
/*================================================================================*/
//room object for localStorage
		var roomObj = {
			    	ids: []
			    };

	$(document).on("click", ".get-the-room", function(e) {
		roomObj.ids.push($(this).val());
	    var selectedRooms = [];
	    selectedRooms.push(roomObj);
	   $(this).text('Room Selected').fadeOut(1000);
	   $('button#remove-'+$(this).val()).text('Remove Room').fadeIn(1000);
	   
	  
	    localStorage.setItem('selected_rooms', JSON.stringify(selectedRooms));
	    localStorage.setItem('rm_no', JSON.stringify($(this).val()));
  		$('input.show-booking-button').show();
	 });

	//to remove selected room from localStorage
	$(document).on('click', '.remove-the-room', function(e){
		var selectedRooms = [];
		
		for(j=0;j< roomObj.ids.length;j++){
		 		if(roomObj.ids[j] === $(this).val()){
					//console.log("remove " + $(this).val());
					var index = roomObj.ids.indexOf($(this).val());
					roomObj.ids.splice(index, 1);
					 selectedRooms.push(roomObj);
					localStorage.setItem('selected_rooms', JSON.stringify(selectedRooms));
					$(this).text('Room Removed').fadeOut(1000);
					$('button#add-'+$(this).val()).text('Select Room').fadeIn(1000);
			 	}
			} 
	});