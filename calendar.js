var current_user = "";//initialize no current user
var token;//CSRF token set at login and unset at logout

//$("#logout_id").hide();

var months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

//get the time of "right now"
var dateNow = new Date();
var monthNow = new Month(dateNow.getFullYear(), dateNow.getMonth());
var weekNow = new Week(monthNow.getDateObject());
var dayNow = dateNow.getDate();

//get the time of "current page"
var current_date = new Date();
var currentMonth = new Month(current_date.getFullYear(), current_date.getMonth());
var currentWeek = new Week(currentMonth.getDateObject());

var eventmonth = {};

function getStartDay(){
	//get the first day of the current month
	return currentMonth.getDateObject(1).getDay();
}
function getDaysInMonth(){
	//get the number of days in the month
	return currentMonth.nextMonth().getDateObject(0).getDate();
}
function removeDates(){
	//removes all dates from calendar table
	var weeks = document.getElementById("dates").getElementsByClassName("week");
	var len = weeks.length;
	for(var i = 0; i < len; i++){
		document.getElementById("dates").removeChild(weeks[0]);
	}
}
function addToCalendar(crntEvent){
	//adds events for a whole month
	//alert("start");
	//alert(eventmonth["judge"]);
	var newTr = document.createElement("tr");
	newTr.setAttribute("class", "week");
	var trIndex = 0;
	var jsondata = JSON.parse(crntEvent);
	var startDay = getStartDay();
	var i;
	var newTd;
	for(i = 0; i < startDay; i++){
		newTd = document.createElement("td");
		newTd.setAttribute("class", "gray");
		newTd.appendChild(document.createTextNode(" "));
		newTr.appendChild(newTd);
		trIndex++;
	}
	for(i=0;i<getDaysInMonth();i++){
		if(trIndex===0){
			newTr = document.createElement("tr");
			newTr.setAttribute("class", "week");
		}
			
		//var events = eventsOnDay(i+1, jsondata);
		newTd = document.createElement("td");

		var date_number = document.createElement("div");	
		date_number.innerHTML = "<h3>" + (i+1) + "</h3>";

		newTd.appendChild(date_number);

		//*********Revise part*************
		//var eventParent = document.createElement("ul");
		//alert(eventmonth.length);
		var idnum = currentMonth.year+'-'+(currentMonth.month+1)+'-'+(i+1);
		newTd.id = idnum;
		if(eventmonth["judge"]){
			//alert("in");
			
			//if(eventmonth[idnum]){
				//alert("in");
				// var events = eventmonth["_2016_03_23"];
				//alert(idnum);
			
			var eventParent = document.createElement("ul");
			for(var events in eventmonth){
				//alert(events);
				//for(var i in events){
				//alert(eventmonth[events].date);
				//alert(idnum);

				if(eventmonth[events].date == idnum){
					//alert("in");
					var eventShow = document.createElement("li");
					//alert("1");
					//var date_i = events["ele"+i]["date"];
					// eventShow.id = ""+eventmonth[events].eventid;
					// eventShow.setAttribute("onclick", "myevent(event)");

					// eventShow.innerHTML = "<a>"+eventmonth[events].title+"</a>";
					

					// eventParent.appendChild(eventShow);

					eventParent.innerHTML +="<li id="+eventmonth[events].eventid+" onclick=myevent(event)>"+eventmonth[events].title+"</li>";
				}
				//}
			}
			newTd.appendChild(eventParent);
		}


		//*********Revise part end*********

		
		if ((currentMonth.month == monthNow.month)&&(currentMonth.year == monthNow.year)&&(i+1) == dayNow) {
            newTd.setAttribute("class","red");
        }


		newTd.appendChild(document.createElement("br"));
		newTr.appendChild(newTd);
		trIndex=(trIndex+1)%7;
		if(i==getDaysInMonth()-1){
			for(;trIndex !== 0; trIndex=(trIndex+1)%7){
				var newTd2 = document.createElement("td");
				newTd2.setAttribute("class", "gray");
				newTd2.appendChild(document.createTextNode(" "));
				newTr.appendChild(newTd2);
			}
		}
		if(trIndex===0){
			document.getElementById("dates").appendChild(newTr);
		}
	}
}

function myevent(event){

	var dataString="id="+encodeURIComponent(event.target.id);
	// alert("event.target.id");

	xmlHttp = new XMLHttpRequest();
	xmlHttp.open("POST", "eventofid.php", true);
	xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	props = event.target.getBoundingClientRect();
	xmlHttp.addEventListener("load", function(event){
		var jsondata = JSON.parse(event.target.responseText);
		// alert(jsondata.title);
		if(jsondata.success){
			// alert(jsondata.eventid);
			// alert(jsondata.token);
			document.getElementById("titlelog").innerHTML = "<b>"+jsondata.title+"</b>";
			document.getElementById("typelog").innerHTML = jsondata.type;
			document.getElementById("datelog").innerHTML = jsondata.date;
			document.getElementById("startlog").innerHTML = jsondata.start;
			document.getElementById("endlog").innerHTML = jsondata.end;
			document.getElementById("contlog").innerHTML = jsondata.description;
			document.getElementById("idlog").value = jsondata.eventid;
			document.getElementById("token").value = jsondata.token;

		}else{
			alert(jsondata.error);
		}
		
	}, false);

	document.getElementById("eventdialog").style.left = props.left + 30 + 'px';
	document.getElementById("eventdialog").style.top = props.top + 120 + 'px';
	document.getElementById("eventdialog").style.display = "block";
	xmlHttp.send(dataString);

	$("div#eventdialog").show();
}

function displayDates(){//This displays all of the events for the current month on the calendar
	getEvent(currentMonth.month, currentMonth.year);
}

displayDates();
function updateMonth(){
	//updates month name being displayed as current month in the side bar
	var monthName = months[currentMonth.month];
	document.getElementById("MonthName").textContent = monthName + ", " + currentMonth.year;
}

function getEvent(month,year){
	//get events for a whole month for a particular user
	var xmlHttp = new XMLHttpRequest();
	var u = current_user;
	xmlHttp.open("POST", "database.php", true);
	xmlHttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xmlHttp.addEventListener("load", ajaxCallback, false);
	xmlHttp.send("month=" + months[month] + "&year=" + year + "&username=" + u + "&token=" + token);
}

function ajaxCallback(event){//callback for retreiving a full month's jsondata
	var crntEvent = event.target.responseText;
	if(current_user===""){crntEvent="{\"maxindex\":0}";}
	addToCalendar(crntEvent);
}

updateMonth();

//listeners

document.getElementById("PrevMonth").addEventListener("click", function(){
	currentMonth = currentMonth.prevMonth(); // Previous month would be currentMonth.prevMonth()
	removeDates();
	displayDates();
	updateMonth();
}, false);
document.getElementById("NextMonth").addEventListener("click", function(){
	currentMonth = currentMonth.nextMonth();
	removeDates();
	displayDates();
	updateMonth();
}, false);
document.getElementById("ThisMonth").addEventListener("click", function(){
	currentMonth = new Month(current_date.getFullYear(), current_date.getMonth()); // back to month now
	removeDates();
	displayDates();
	updateMonth();
}, false);
