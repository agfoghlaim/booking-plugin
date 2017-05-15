console.log("hello from moh main");
jQuery(document).ready(function($){
 $('.datepicker').datepicker({ dateFormat: 'yy-mm-dd' });
	/*=====================================
	=======  FOR CHECK AVAILABITY  =======
	=====================================*/

	/*
	Check Availabity Handling
	*/
window.onload = function(){
	//localStorage.clear();
	console.log("hello from onload");
var dateSubmit = document.getElementById('date-submit');
console.log("got date submit");
	dateSubmit.addEventListener('click', function(e){
	e.preventDefault();
	e.stopPropagation();
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

	/* 
	show booking form and get ids of rooms selected, currently it's set up to do booking on a theme page
	*/
	// $(document).on('click', '.show-booking-button', function(e){
	// 	e.preventDefault();
	// 	//e.stopPropagation() 
	// 	var roomsArray = [];
	// 	var rm_nums = JSON.parse(localStorage.getItem("selected_rooms"));
	// 	for(i=0;i<rm_nums[0].ids.length;i++){
	// 		roomsArray.push(rm_nums[0].ids[i]);
	// 	}
	// 	var arr = JSON.parse(localStorage.getItem("arrive"));
	// 	var dep = JSON.parse(localStorage.getItem("depart"));
	//     var bookingData = {arrD:arr, depD:dep, rm_nums:roomsArray};
	   
	    
	//     //mohGetForm();
	//     //bookingAjaxRequest(bookingData, moh_booking_data_action);
	//     $.ajax({
	// 				type:'POST',
	// 				dataType: 'json',
	// 				url: myAjax.ajaxurl,
	// 				data: {
	// 					action: 'marie_is_confused',
	// 					data: bookingData,
	// 					security: myAjax.getDetails
	// 				},
	// 				success: function(response){
	// 					//if(response.success){
	// 						//$('div#booking-details').html(response.data);
	// 						console.log(response);

						
	// 				},
	// 				error: function(response){
	// 					alert("error" + response);
	// 				}
	// 			});
	   

	// });



/*================================================================================*/ 
/*=================================  BOOKING FORM   ==============================*/
/*================================================================================*/
/*
Booking summary from localStorage
*/

// $('h3#booking-summary-heading').text("You are booking: " );
// $('p#booking-num-rooms').text("Number of Rooms: " + JSON.parse(localStorage.getItem("selected_rooms"))[0].ids.length);
// $('p#summary-checkin').text("Check In: " + JSON.parse(localStorage.getItem("arrive")));
// $('p#summary-checkout').text("Check Out: " + JSON.parse(localStorage.getItem("depart")));
// //$('p#summary-price').text("Total(from localStorage): " + JSON.parse(localStorage.getItem("depart")));
// $('p#summary-nights').text("No.Nights: " + JSON.parse(localStorage.getItem("numNights")));

/*
Add booking summary with info from server
*/
window.onload = function(){
	if(JSON.parse(localStorage.getItem("selected_rooms"))[0].ids.length>0){
				var summaryData = {
					arr:JSON.parse(localStorage.getItem("arrive")),
					dep: JSON.parse(localStorage.getItem("depart")),
					selectedRooms: JSON.parse(localStorage.getItem("selected_rooms"))[0].ids
				}
						 $.ajax({
								type:'POST',
								dataType: 'json',
								url: myAjax.ajaxurl,
								data: {
									action: 'moh_summary_table_info',
									data: summaryData,
									//submission: document.getElementById('xyz').value,
									security: myAjax.guest_security,
								},
								success: function(response){
									$('div#moh-confirm-booking-table').html('');
									var rowCounter = 1;
									makeStuff('table', 'moh-summary-table', false, 'moh-confirm-booking-table' );
									makeStuff('tr', 'tr-title', false, 'moh-summary-table' );
									makeStuff('td', 'td-title-rate', 'Nightly Rate', 'tr-title' );
									makeStuff('td', 'td-title-room', 'Room No', 'tr-title' );
									makeStuff('td', 'td-title-nights', 'No. Nights', 'tr-title' );
									makeStuff('td', 'td-title-cost', 'Total Cost', 'tr-title' );


									for(i in response.data){
										makeStuff('tr', 'tr-'+ rowCounter, false, 'moh-summary-table');
										makeStuff('td', 'td-'+ response.data[i].rm_rate, response.data[i].rm_rate, 'tr-'+rowCounter);
										makeStuff('td', 'td-'+ response.data[i].rm_no, response.data[i].rm_no, 'tr-'+rowCounter);
										makeStuff('td', 'td-'+ response.data[i].num_nights, response.data[i].num_nights, 'tr-'+rowCounter);
										makeStuff('td', 'td-'+ response.data[i].rm_cost, response.data[i].rm_cost, 'tr-'+rowCounter);
										
										rowCounter++;
										
									}
									makeStuff('tr', 'tr-'+ rowCounter, false, 'moh-summary-table');
									makeStuff('td', false, false, 'tr-'+rowCounter);
									makeStuff('td', false, false, 'tr-'+rowCounter);
									makeStuff('td', false, false, 'tr-'+rowCounter);
									makeStuff('td', false, false, 'tr-'+rowCounter);
									makeStuff('td', 'td-'+ rowCounter, "Total: €" +response.data[response.data.length-1].grand_total, 'moh-summary-table');
									$('h3#moh-grand-total').append(response.data[response.data.length-1].grand_total);
									//makeStuff('tr', 'tr-grand-total', 'tr-'+rowCounter);
			 					},
			 					error: function(response){
			 						alert("error" + response.error);
			 					}
			 				});
			//$(this).hide('slow');
			//});
	}
}




/*
Handle guest info form
*/
function isBlank(str){
 if(str ===''){
 	return true;
 }
}
function notProperLength(str, max, min){
	console.log(str);
	console.log(max);
	console.log(min);
 if(str.length > max || str.length <= min){
 	console.log("true");
 	return true;
 }
}

function notOnlyLetters(str){
   var patt1 = /[^-A-z]/g;
    return patt1.test(str);
}

function isProbablyEmail(str){
	var re = /\S+@\S+\.\S+/;
  	if(str.length<50){
    //console.log("one" + re.test(str));
    return re.test(str);
  }else{
  	//console.log("tryin");
    return false;
  }
}

function isAlphaNumeric(str){
	//allow . and ,
	var p = /^[a-z0-9.,]+$/i;
	return p.test(str);
}

function isNotNumeric(str){
	if(str.length<20){
		var p = /[^0-9]/g;
		return p.test(str);
	}else{
		return true;
	}
  	
}



function validateGuestInfo(){
	$('.error-label').text('');
		var fn=$('input#fn').val();  //between 2 and 16
	    var ln=$('input#ln').val(); //between 2 and 16
	    var em=$('input#email').val(); //is email
	    var em_again = $('input#email_again').val();
	   	var ad=$('input#address').val(); // is no and letters
	    var country=$('select#country').val();// use dropdown??
	    var phone=$('input#phone').val(); //is numbers
	    var postcode=$('input#postcode').val(); //is numbers and letters
	    var adults=$('input#no-adults').val(); //between 1 and 4
	    var children =$('input#no-children').val(); //between 0 and 3
	    var arr_time=$('select#arr-time').val() + " " + $('select#arr-am-pm').val(); //is numbers and letters 
	    var am_pm = $('select#arr-am-pm').val();
	    console.log("arrival time " + arr_time);
	var errorArray = [];
	var msg = '';
	var errorDiv = $('div#moh-guest-form-validation');
		if(isBlank(fn)){
			msg = ' Please enter name.';
			//errorDiv.html(msg);
			$('span#fn-err').text(msg).show();
			return false;
		}
		if(isBlank(ln)){
			msg = ' Please enter name.';
			errorDiv.html(msg);
			$('span#ln-err').text(msg).show();
			return false;
		}
		if(notProperLength(fn, 20, 2)){
			msg = ' Name must be between 2 and 20 characters.';
			$('span#fn-err').text(msg).show();
			return false;
		}
		if(notProperLength(ln, 20, 2)){
			msg = ' Name must be between 2 and 20 characters.';
			$('span#ln-err').text(msg).show();
			return false;
		}
		if(notOnlyLetters(fn)){
			msg = ' Letters only please.';
			//errorDiv.html(msg);
			$('span#fn-err').text(msg).show();
			return false;
		}
		if(notOnlyLetters(ln)){
			msg = ' Letters only please.';
			//errorDiv.html(msg);
			$('span#ln-err').text(msg).show();
			return false;
		}
		if(!isProbablyEmail(em)){
			msg = ' Email not valid';
			$('span#email-err').text(msg).show();
			return false;
		}
		if(em_again !== em){
			msg = ' Emails should match';
			$('span#email_again-err').text(msg).show();
			return false;
		}
		if(!isAlphaNumeric(ad)){
			msg = ' Enter a-z and numbers only';
			$('span#address-err').text(msg).show();
			return false;
		}
		if(isNotNumeric(phone)){
			msg = ' Enter phone number without spaces';
			$('span#phone-err').text(msg).show();
			return false;
		}


	//check email
	// if(){

	// }
	else{
		return true;
	}
	

}
 	$(document).on("click", "input#guest-info-form", function(e){
 		e.preventDefault();
 		e.stopPropagation();
 		formGuestInfo();
 	});

	function formGuestInfo(){
		if(!validateGuestInfo()){
			return;
		}
	    var fn=$('input#fn').val();  //between 2 and 16
	    var ln=$('input#ln').val(); //between 2 and 16
	    var em=$('input#email').val(); //is email
	   	var ad=$('input#address').val(); // is no and letters
	    var country=$('select#country').val();// use dropdown??
	    var phone=$('input#phone').val(); //is numbers
	    var postcode=$('input#postcode').val(); //is numbers and letters
	    var adults=$('input#no-adults').val(); //between 1 and 4
	    var children =$('input#no-children').val(); //between 0 and 3
	    //var arr_time=$('input#arr-time').val(); //is numbers and letters
	   var arr_time=$('select#arr-time').val() + " " + $('select#arr-am-pm').val();
		var arr = JSON.parse(localStorage.getItem("arrive"));
		var dep = JSON.parse(localStorage.getItem("depart"));
		var rm_num = JSON.parse(localStorage.getItem("rm_no"));
		var num_nights = JSON.parse(localStorage.getItem("numNights"));
		var security_info;
		var roomsArray = [];
		var rm_nums = JSON.parse(localStorage.getItem("selected_rooms"));
		for(i=0;i<rm_nums[0].ids.length;i++){
			console.log(rm_nums[0].ids[i]);
			roomsArray.push(rm_nums[0].ids[i]);
			//console.log(roomsArray);
		}
	     //console.log(fn, ln, em, ad, country, phone, postcode, adults, children, arr_time);
	

	    var guestData = {
	    	checkin: arr,
	    	checkout: dep,
	    	rm_num: rm_num,
	    	rm_nums: roomsArray,
	    	fname:fn, 
	    	lname:ln, 
	    	email:em,
	    	address:ad,
	    	country:country, 
	    	phone:phone,
	    	postcode:postcode,
	    	no_adults:adults,
	    	no_children:children, 
	    	arr_time:arr_time,
	    	num_nights:num_nights


		};
		console.log(guestData);
	    // console.log(fn, ln, em, ad, country, phone, postcode, adults, children, arr_time);
	
	     $.ajax({
					type:'POST',
					dataType: 'json',
					url: myAjax.ajaxurl,
					data: {
						action: 'moh_ajax_action_guest_info',
						data: guestData,
						//submission: document.getElementById('xyz').value,
						security: myAjax.guest_security,
					},
					success: function(response){
						for(i in response.data){ 
							$('div#info-success').append(
								response.data[i].guest_id,
								response.data[i].arrival_date,
								response.data[i].guest_name,
								response.data[i].num_rooms, 
								response.data[i].arrival_time,
								response.data[i].booking_id
						

								);
	
							}
					window.location.href = "http://localhost/designassociates/moh/book-room-101/booking-confirmation";	
					},
					error: function(response){
						alert("error" + response.error);
					}
				});
	
		

	}//END formGuestInfo


function makeStuff(name, id, content, appendTo){
  var el;
  if(name){
  el = document.createElement(name);
  }
  if(id){
    el.setAttribute('id', id);
  }
   if(content){
    el.appendChild(document.createTextNode(content));
  }
   if(appendTo){
     document.getElementById(appendTo).appendChild(el);
  }
  
}


});

