console.log("hiya");
jQuery(document).ready(function($){
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
		
		theString += "<li class='date-class' id='" + dateForId + "'>" +  day_num  +"<div style='display:none' id='div-" +dateForId + "'></div></li>";

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
				 
				for(i in response.data){ 
					getID = response.data[i].data_arr_date.substr(0,5)+response.data[i].data_arr_date.substr(5,2) + response.data[i].data_arr_date.substr(7,3);
					theItem = document.getElementById(getID);
					theItemDiv = document.getElementById('div-' + getID)
					$(theItem).append(
						response.data[i].room_num,
						);
					$(theItemDiv).append(
						response.data[i].room_num,
						response.data[i].arr_date,
						response.data[i].dep_date,
						response.data[i].room_num,
					 	response.data[i].guest_id,
						response.data[i].guest_name,
					 	response.data[i].booking_id
						);
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
