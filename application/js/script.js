var authkey = localStorage.getItem("authkey");
var username = localStorage.getItem("username");
var article = localStorage.getItem("patientId");


var plot;

var graphSettings =  {

    series: {
    
      shadowSize: 0
      //bars:{show:true}
    },
   
    xaxis: {
      mode: "time",
      timezone: "browser"
    }
  }


function getPrintData(storedData,end,totalPoints) {
  var print = [];

  if (storedData.length > totalPoints) 
  {
    print = storedData.slice(end - totalPoints + 1, end + 1);
  } 
  else 
  {
    for(var i = 0; i < totalPoints - end - 1; i++)
    {
      print.push(null);
    }  
    print = print.concat(storedData);
  }

  return print;
}



function unixTimeToDate(unixTime)
{
  var date = new Date(unixTime*1000);
  var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
  var year = date.getFullYear();
  var month = checkSingle(months[date.getMonth()]);
  var date1 = checkSingle(date.getDate());
  var hours = checkSingle(date.getHours());
  var minutes = checkSingle(date.getMinutes());
  var seconds = checkSingle(date.getSeconds());


  var formattedTime = date1+','+month+' '+year+' '+hours+':'+minutes+':'+seconds ;
  return formattedTime;
}

function checkSingle(character) {
  if(character.toString().length == 1)
  {
    return "0" + character;
  } 
  else
  {
    return character;
  }
}

function zipNewData(newData) {
  var length = Object.keys(newData.data).length;
  var newzip = [];

  if (newData == null) {
    return newzip;
  }

  for (var i = 0; i < length; i++) {
    newzip = newzip.concat([[newData.data[i].reading_date*1000,newData.data[i].entryValues]]);
  }

  return newzip;
}


function fillInBlank(start,end,lastValue,newData) {
  var copy = [];
  var n = lastValue;
  var k = 0;

  //alert(start);

  for (var i = 0; i < end - start + 1; i++) 
  {
    //  alert(newData);
    if(newData != null)
    {
    if ((start + i)*1000 == newData[k][0]) 
      {
        n = newData[k][1];
        k = k + 1;
      }
    }
    copy.push([(start + i)*1000, n]);
  }

  return copy;
}




function setHello() {
  var name = localStorage.getItem("name");
  var hello = document.getElementById("hello");
  hello.innerText = "WELCOME! "+name;
}


//set loading while waiting response
$(document).ready(function(){
  setHello();
  $(document).ajaxStart(function(){
    $("#load").css("display","block");
    $("#load1").css("display","block");
    $("#load2").css("display","block");
    $("#load3").css("display","block");
    $("#load4").css("display","block");
  });
  $(document).ajaxComplete(function(){
    $("#load").css("display","none");
    $("#load1").css("display","none");
    $("#load2").css("display","none");
    $("#load3").css("display","none");
    $("#load4").css("display","none");
  });
  
  $(document).ajaxStart(function(){
  $(".throbber").css("display","block");
  });
  $(document).ajaxComplete(function(){
  $(".throbber").css("display","none");
  });

});
