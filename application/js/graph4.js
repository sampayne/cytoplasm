var printData4 = [];
var printData4_1 = [];
var storedData4 = [];
var storedData4_1 = [];
var totalPoints4 = 60;
var print4_end = 3600;
var jump4 = 60;
var start4 = Math.round(new Date().getTime() / 1000)-3600;
var end4 = Math.round(new Date().getTime() / 1000);
var taxonomy4 = 'health-cardio-bloodpressure';


function drawGraph4() {

  var m = end4 + 1;
  while(end4 < m)
  {
    end4 = Math.round(new Date().getTime() / 1000);
  } 

  var lastValue1 = lastValue(storedData4);
  var lastValue2 = lastValue(storedData4_1);


  $.post("http://uclcytoplasm.cloudapp.net/api/data/", 
    {      
      authkey: authkey,
      article: article,
      taxonomy: taxonomy4,
      start: start4,
      end: end4,
      username: username
    },
    function (data) 
    {

      if(data.error != null)
      {
        alert(data.error);
      }
      else{

      var newData1 = zipNewData4(data,0);
      var newData2 = zipNewData4(data,1);

      storedData4 = storedData4.concat(fillInBlank(start4,end4,lastValue1,newData1));
      storedData4_1 = storedData4_1.concat(fillInBlank(start4,end4,lastValue2,newData2));

      
      print4_end += 1;
      printData4 = getPrintData(storedData4,print4_end,totalPoints4);
      printData4_1 = getPrintData(storedData4_1,print4_end,totalPoints4);

      $.plot("#placeholder4",[{data:printData4},{data:printData4_1}],graphSettings);
      
      start4 = end4 + 1;
      showData4();
      drawGraph4();
      }
    });

}


function increaseScope4() {
  if(totalPoints4*2 <= 3600)
  {
    totalPoints4 = totalPoints4*2; 
    printData4 = getPrintData(storedData4,print4_end,totalPoints4);
    printData4_1 = getPrintData(storedData4_1,printData4_1,totalPoints4);
    $.plot("#placeholder4",[{data:printData4},{data:printData4_1}],graphSettings);
  }
}


function decreaseScope4() {
  if(totalPoints4/2 >= 4)
  {
    totalPoints4 = totalPoints4/2;
    printData4 = getPrintData(storedData4,print4_end,totalPoints4);
    printData4_1 = getPrintData(storedData4_1,printData4_1,totalPoints4);
    $.plot("#placeholder4",[{data:printData4},{data:printData4_1}],graphSettings);
  }
}


function getPreData4() {
  print4_end = print4_end - jump4;

  printData4 = getPrintData(storedData4,print4_end,totalPoints4);
  printData4_1 = getPrintData(storedData4_1,print4_end,totalPoints4);
  $.plot("#placeholder4",[{data:printData4},{data:printData4_1}],graphSettings);
}


function getFwData4() {

  if(print4_end + 5 > storedData4.length - 1)
  {
    print4_end = storedData4.length -1;
  }
  else{
  print4_end = print4_end + jump4;
  }

  printData4 = getPrintData(storedData4,print4_end,totalPoints4);
  printData4_1 = getPrintData(storedData4_1,print4_end,totalPoints4);
  $.plot("#placeholder4",[{data:printData4},{data:printData4_1}],graphSettings);
}


function showData4() {
  var newestData = document.getElementById("dataReceive4");
  if (printData4[printData4.length - 1][1] != null) 
  {
    newestData.innerText = printData4[printData4.length - 1][1] + '/' + printData4_1[printData4_1.length - 1][1];
  }
  else
  {
    newestData.innerText = "no data";
  }
  var newestTime = document.getElementById("timeReceive4");
  newestTime.innerText = unixTimeToDate(printData4[printData4.length - 1][0]/1000);
}


function zipNewData4(newData,n) {
  var length = Object.keys(newData.data).length;
  var newzip = [];
  var seperate = [];

  if (newData == null) {
    return newzip;
  }

  for (var i = 0; i < length; i++) {
    seperate = newData.data[i].entryValues.split(',');
    newzip = newzip.concat([[newData.data[i].reading_date*1000,seperate[n]]]);
  }

  return newzip;
}


function lastValue(stored)
{
   if(stored.length > 0) {
    var lastValue = stored[stored.length - 1][1];
  }
  else{
    var lastValue = null;
  }
}