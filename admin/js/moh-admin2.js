console.log("hiya");
jQuery(document).ready(function($){

///////////////////////////////////////////////////////////////////////
/////////////////// 	get basic info	//////////////////////////////
//////////////////////////////////////////////////////////////////////
/*
for mkcal function we need:
list of all rooms in db
list of rooms booked on any given day needs, arrive and depart data
*/

var list_all_rooms_in_db = function(data){
  var action = 'moh_send_admin_data_ids';
		$.ajax({
			type:'POST',
			dataType: 'JSON',
			security: 
			url: ajaxurl,
			data: {
				action: action,
				data: data,
				
			},
			success: function(response){
			return response.data;	
					
			},
			error: function(response){
				//
			}
		}); 
}

console.log("all rooms "+ list_all_rooms_in_db);




var initialiseMonth = 0;
$('div#marieDiv').append(mkCal(initialiseMonth));

var prev = document.getElementById('prevMonth');
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

/////////////////////



	function mkCal(backMonths){

		var d = new Date();
		var yr = d.getFullYear();
		var mt = d.getMonth()-backMonths;
		console.log("*** month is " + mt);
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

   		var adminData = {
			month: pad(month+1,2),
			year: year,
			days_in_month: days_in_month
			
		};
			getAdminData(adminData);
			
		var dateForId;	
		while(day_num <= days_in_month-1){
		day_num++;
		dateForId = year + "-" + pad(month+1,2) +"-"+ pad(day_num,2);
		/*
		theString += "<li class='date-class' id='" + dateForId + "'>" +  day_num  +"<div style='display:none' id='div-" +dateForId + "'></div></li>";
		*/
		



		theString += "<li class='date-class' id='" + dateForId + "'>" +  day_num  + "</li>";
		}
	 
		while(day_count > 1 && day_count <=7){
			day_count++;
		}

		theString += "</ul>";
		return theString;
	}//end mkCal()



	///ajax function to get booking details
	function getAdminData(data){
		var action = 'moh_send_admin_data';
		$.ajax({
			type:'POST',
			dataType: 'JSON',
			url: ajaxurl,
			data: {
				action: action,
				data: data,
				
			},
			success: function(response){
				

					var getID;
					var trackData;
				 
			for(i in response.data){ 
					getID = response.data[i].data_arr_date.substr(0,5)+response.data[i].data_arr_date.substr(5,2) + response.data[i].data_arr_date.substr(7,3);
					theItem = document.getElementById(getID);
					theItemDiv = document.getElementById('div-' + getID);
					//theItemP = document.getElementById('p-'response.data[i].data_room_num);
					var dateOfi = response.data[i].data_arr_date; 
					var pStr = "";
					var anotherStr = "";

				for(k=0; k < response.data[i].names_of_all_rooms.length;k++){
					//console.log("booked today" + response.data[i].all_rooms_booked_today[0]);
					anotherStr += "<p id='" + response.data[i].data_arr_date +"-" +  response.data[i].data_room_num + "'";

				
					if(response.data[i].names_of_all_rooms[k] === response.data[i].data_room_num){
						anotherStr += "class='moh-room-class-booked'>" + response.data[i].data_room_num;
						anotherStr+= "</p>";
						
					}else{
						anotherStr += ">" + response.data[i].names_of_all_rooms[k] + "</p>";
					}
							
					// }else{
					// 	//console.log(response.data[i].names_of_all_rooms);
					// 	anotherStr += "class='moh-room-class-not-booked'>" + response.data[i].names_of_all_rooms[k];
					// 	anotherStr+= "</p>";
					// 	//response.data[i].names_of_all_rooms.splice(response.data[i].names_of_all_rooms.indexOf(response.data[i].data_room_num),1);
					// 		//console.log(anotherStr);
					// 		//console.log(response.data[i].names_of_all_rooms);
					// }







					//for every response
					//add paragraph for every room that exists
					//add correct id to each paragraph
					//add relevant room number to each paragraph.
					//console.log("232323  " + response.data[i].names_of_all_rooms[j][0]);
					// var rmArray= [response.data.names_of_all_rooms];
					// console.log("room array " + rmArray);
					// for(j=0;j<response.data[i].names_of_all_rooms.length;j++){//4
						
					// 	console.log("room array "+ response.data[i].names_of_all_rooms.length);
					// 	console.log("232323  " + response.data[i].names_of_all_rooms[j][0] + " " + response.data[i].data_room_num);
					// 	if(response.data[i].names_of_all_rooms[j][0] === response.data[i].data_room_num){
					// 		 pStr += "<p class='moh-room-class-booked'>" + response.data[i].data_room_num + "</p>";
					// 		  //console.log("not pop " + response.data[i].names_of_all_rooms[j][0]);
					// 		 response.data[i].names_of_all_rooms[j][0].shift();
							 
					// 		 //console.log("pop " + response.data[i].names_of_all_rooms[j][0]);
					//  	}	
					// }

					// for(j=0;j<response.data[i].names_of_all_rooms.length;j++){

					// 	if(response.data[i].names_of_all_rooms[j] === response.data[i].data_room_num){
					// 		//console.log("b" + response.data[i].names_of_all_rooms[j][0]);
					// 		console.log("before "+ response.data[i].names_of_all_rooms );
					// 		var mohWhat = response.data[i].names_of_all_rooms;
					// 		mohWhat.splice(mohWhat.indexOf(response.data[i].data_room_num,1));
					// 		console.log("after "+ response.data[i].names_of_all_rooms );
					// 	 pStr += "<p class='moh-room-class-booked'>" + response.data[i].data_room_num + "</p>";
					// 	 	for(x in response.data[i].names_of_all_rooms){
					// 	 		 pStr += "<p class='moh-room-class-notbooked'>" + response.data[i].names_of_all_rooms[x] + "</p>";
					// 	 	}
						// console.log(response.data[i].names_of_all_rooms[j]);
						 //response.data[i].names_of_all_rooms[j] = [];
						 //console.log(response.data[i].names_of_all_rooms[j]);	
						}
						//else{
						// 	 pStr += "<p class='moh-room-class-notbooked'>" + response.data[i].data_room_num + "</p>";
						// 	 //console.log("not");
						// }
						
					//}

					
					// for(j=0;j<response.data[i].names_of_all_rooms.length;j++){
					// //pStr += "<p class='moh-room-class' id ='p-" + response.data[i].data_room_num + "'>" + response.data[i].data_room_num + "</p>";
					// pStr += "<p class='moh-room-class'>" + response.data[i].names_of_all_rooms[j][0] + "</p>";
					// console.log(response.data[i].names_of_all_rooms[j][0]);
					// console.log("no "+ response.data[i].room_num  );
					// 	if(response.data[i].names_of_all_rooms[j][0] === response.data[i].data_room_num){
					// 		pStr += "<p>booked</p>";
					// 	}
					// }
					



					
					
					//pStr += "<p class='moh-room-class-empty'>1</p>";
						$(theItem).append(anotherStr);
					//$(theItem).append(pStr);
						//response.data[i].data_room_num,
						//);
		//trackData = response.data[i];
					// $(theItemDiv).append(
					// 	response.data[i].total_number_of_rooms,
					// 	response.data[i].names_of_all_rooms,
					// 	response.data[i].room_num,
					// 	response.data[i].arr_date,
					// 	response.data[i].dep_date,
					// 	response.data[i].room_num,
					//  	response.data[i].guest_id,
					// 	response.data[i].guest_name,
					//  	response.data[i].booking_id
					// 	);
				}
			},
			error: function(response){
				//
			}
		});
	}

	//ajax to click on individual calendar dates
	$(document).on("click", ".date-class", function(e) {
		var currentId = $(this).attr('id');
		console.log(currentId);
		//var action = 'moh_send_admin_data';
		// $.ajax({
		// 	type:'POST',
		// 	dataType: 'JSON',
		// 	url: ajaxurl,
		// 	data: {
		// 		action: action,
		// 		data: data,
				
		// 	},
		// 	success: function(response){
					
					
		// 	},
		// 	error: function(response){
		// 		//
		// 	}
		// });
	});



});
