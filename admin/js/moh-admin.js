console.log("hiya");
jQuery(document).ready(function($){
var gotRooms;
		//var getRoomsPromise;
		
		function getAllRooms(){
		  var action = 'moh_send_admin_data_ids';
			 $.ajax({
					type:'POST',
					async:false,
					dataType: 'JSON',
					url: ajaxurl,
					data: {
						action: action
					},
					success: function(response){
					console.log(response.data);
					gotRooms = response.data;
					}
				}); 

		}
		
		
	getAllRooms();
// function appendInfo(){
// 	var marie = document.getElementById('2017-05-01-101');
// 	alert(marie);
// }

var initialiseMonth = 0;
$('div#marieDiv').append(mkCal(initialiseMonth));
//appendInfo();

var prev = document.getElementById('prevMonth');
alert("got prev");
prev.addEventListener('click', function(){
	initialiseMonth ++;
	$('div#marieDiv').html("");
	$('div#marieDiv').append(mkCal(initialiseMonth));
	
});

var next = document.getElementById('nextMonth');
next.addEventListener('click', function(){
	initialiseMonth --;
	$('div#marieDiv').html("");
	$('div#marieDiv').append(mkCal(initialiseMonth));	
});


	function mkCal(backMonths){

		var d = new Date();
		var yr = d.getFullYear();
		var mt = d.getMonth()-backMonths;
		var dt = d.getDay();

		var date = new Date(yr, mt , dt),
		    locale = "en-us",
		    dmonth = date.toLocaleString(locale, { month: "long" });
		    dyear = date.getFullYear();

		document.getElementById('display-month').innerText = dmonth;
		document.getElementById('display-year').innerText = dyear;

		var day = date.getDate();
		var month = date.getMonth();
		var year = date.getFullYear();
		var first_day = new Date(year,month, 1);
		var day_of_week = new Date(first_day).getDay();
		var blank;

		switch(day_of_week){
			case 7: blank = 0; break;
			case 1: blank = 1; break;
			case 2: blank = 2; break;
			case 3: blank = 3; break;
			case 4: blank = 4; break;
			case 5: blank = 5; break;
			case 6: blank = 6; break;
		}

		function daysInMonth(iMonth, iYear) {
		   return (new Date(year, imonth, 0).getDate());
		}

		var getnext = date;
		var imonth = month +1;
		var days_in_month = daysInMonth(imonth,year);
		var theString = "";
		theString += "<ul class='weekdays'>";
		theString += "<li>S</li><li>M</li>";
		theString += "<li>T</li><li>W</li><li>T</li>";
		theString += "<li>F</li><li>S</li></ul>";

		var day_count = 1;

		theString+=  "<ul class='days'>";

		while( blank >0){
			theString += "<li></li>";
			blank = blank-1;
			day_count++;

		}
		var day_num = 0;
		///
		//add leading zeros to day of month
		function pad(number, length) {
   				var str = '' + number;
    			while (str.length < length) {
        		str = '0' + str;
    			}
   			return str;
   		}
   		////////////////////////////////////////////
   		//// for ajax getAdminData Function/////////
   		///////////////////////////////////////////

   		adminData = {
			month: pad(month+1,2),
			year: year,
			days_in_month: days_in_month
			
		};
			
				
				function appendInfo(data){
				  var action = 'moh_send_admin_data';
						$.ajax({
							type:'POST',
							dataType: 'JSON',
							url: ajaxurl,
							data: {
								action: action,
								data: data
								
							},
							success:function(response){
								var roomNumP;
								
								for(i in response.data){
									var bookingInfoStr = "";
									roomNumP = document.getElementById(response.data[i].data_arr_date+"-"+response.data[i].data_room_num);
									theID = response.data[i].data_arr_date+"-"+response.data[i].data_room_num;
									roomNumP.innerHTML= response.data[i].data_room_num;
									for(j in response.data[i].names_of_all_rooms ){
										console.log("QQQQQQQQQQ" + response.data[i].names_of_all_rooms[j]);
										roomNumP.setAttribute('class', 'moh-booked-room');
									}
									roomNumP.setAttribute('class', 'moh-booked-room');
									//.moh-booked-room
									console.log(response.data[i].data_arr_date + " " + response.data[i].data_room_num + i );
									// roomNumP.addEventListener('click',function(e){
									bookingInfoD = $('#bookingInformation');
									// 	//var target = e.target;
									// 	bookingInfoStr = "";
									// 	bookingInfoStr += response.data[i].data_guest_id + response.data[i].data_guest_name + "</p>";
									// 	bookingInfoStr += response.data[i].data_arr_date + " " + response.data[i].data_room_num;
									// 	bookingInfoD.append(bookingInfoStr);

									// });
									function addEvent(i){
									$(document).on("click", "#"+theID, function(e) {
										bookingInfoStr = "";
										bookingInfoStr2 = "";
										bookingInfoD.html("");
										var currentId = $(this).attr('id');
										console.log("eee" +currentId + " "+ i);
										console.log(response.data[i].data_arr_date);
										bookingInfoStr += "<p><b>";
										bookingInfoStr += "Check In Date: </b>" + response.data[i].checkin + "</p><hr>";
										bookingInfoStr += "<p><b> Check Out Date: </b>" + response.data[i].checkout + "</p><hr>";
										bookingInfoStr += "<p><b>Booking ID: </b>" + response.data[i].data_booking_id+"</p><hr>";
										bookingInfoStr += "<p><b>Guest ID: </b>" +response.data[i].data_guest_id + "</p><hr>";
										bookingInfoStr += "<p><b>Guest Name: </b>"+ response.data[i].data_guest_name+"</p><hr>";
										bookingInfoStr += "<p><b>Room Number: </b>" + response.data[i].data_room_num + "</p><hr>";
										bookingInfoStr += "<p><b>Estimated Arrival: </b>" + response.data[i].data_arrival_time + "</p>";
										bookingInfoD.append(bookingInfoStr);
										
									});
									}
									addEvent(i);
								}
							
								//alert(marie);
								// function makeStuff(name, id, content, appendTo){
								//   var el;
								//   if(name){
								//   el = document.createElement(name);
								//   }
								//   if(id){
								//     el.setAttribute('id', id);
								//   }
								//    if(content){
								//     el.appendChild(document.createTextNode(content));
								//   }
								//    if(appendTo){
								//      document.getElementById(appendTo).appendChild(el);
								//   }
								  
								// }
								// var bookedArray	; 
								// for(i in response.data){ 
								// console.log("length of the data");
									// allRoomsArray = response.data[i].names_of_all_rooms;
									// bookedArray = response.data[i].all_rooms_booked_today;
									// for(j in bookedArray){
									// 	for(k in allRoomsArray){
									// 		makeStuff('p',
									//  		response.data[i].data_room_num+"-"+response.data[i].data_arr_date, 
									//  		bookedArray[j], 
									//  		response.data[i].data_arr_date 
									//  		);
									//  		allRoomsArray.splice(indexOf(bookedArray[j]), 1);
									//  	}
									// 	//bookedArray.splice(indexOf(bookedArray[j]), 1);
									// 	console.log("the array " + bookedArray);
									// }
								 
								//}
							}
						}); 

				}
				appendInfo(adminData);

			// 	getListPromise.success(function (data) {
			// 	for(k in data.data){
			// 	gotList = data.data[k].all_rooms_booked_today;
			// 	console.log("wwwww" +gotList);
			// }
				
			// 	});










			
		var dateForId;	
		while(day_num <= days_in_month-1){
		day_num++;
		dateForId = year + "-" + pad(month+1,2) +"-"+ pad(day_num,2);
	
		theString += "<li class='date-class' id='" + dateForId + "'>" +  day_num ;
		//getAdminDataIdsResponse is list of all rooms in db, see ajax fn getAdminDataIds below
			for(i in gotRooms){
				console.log("we are here "+ gotRooms);
				theString += "<p class='' id='"+  dateForId+"-"+ gotRooms[i]+"'>&nbsp;</p>";
			}
			
			
	
		}


	 
		while(day_count > 1 && day_count <=7){
			day_count++;
			theString += "</li>";
		}

		theString += "</ul>";
		return theString;
	}//end mkCal()

	// var getAdminDataIdsResponse;
	// ///ajax function to get ids
	// function getAdminDataIds(data){
	// 	var action = 'moh_send_admin_data_ids';
	//  	$.ajax({
	//  		async: false,
	// 		type:'POST',
	// 		dataType: 'JSON',
	// 		url: ajaxurl,
	// 		data: {
	// 			action: action,
	// 			data: data,
				
	// 		},
	// 		success: function(response){
	// 			getAdminDataIdsResponse = response.data.names_of_all_rooms;	
	// 			var marie = "marie";
	// 		},
	// 		error: function(response){
	// 		alert("bad");
	// 		}
	// 	}).then(function(){
	// 		console.log("ghj");
	// 		return getAdminDataIdsResponse; 
	// 		getAdminData(adminData);
	// 	});
		
	// 	//return
	// }


	// //ajax function to get booking details
	// function getAdminData(data){
	// 	var action = 'moh_send_admin_data';
	// 	$.ajax({

	// 		type:'POST',
	// 		dataType: 'JSON',
	// 		url: ajaxurl,
	// 		data: {
	// 			action: action,
	// 			data: data,
				
	// 		},
	// 		success: function(response){
	// 			 console.log("admin response "+ response.data);
	// 		},
	// 		error: function(response){
	// 			//
	// 		}
	// 	});
	// }

	//ajax to click on individual calendar dates
	// $(document).on("click", ".date-class", function(e) {
	// 	var currentId = $(this).attr('id');
	// 	console.log("eee" +currentId);
	// 	//var action = 'moh_send_admin_data';
	// 	// $.ajax({
	// 	// 	type:'POST',
	// 	// 	dataType: 'JSON',
	// 	// 	url: ajaxurl,
	// 	// 	data: {
	// 	// 		action: action,
	// 	// 		data: data,
				
	// 	// 	},
	// 	// 	success: function(response){
					
					
	// 	// 	},
	// 	// 	error: function(response){
	// 	// 		//
	// 	// 	}
	// 	// });
	// });



});
