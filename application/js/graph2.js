var printData2 = [];
var storedData2 = [];
var totalPoints2 = 60;
var print2_end = 3600;
var jump2 = 60;
var start2 = Math.round(new Date().getTime() / 1000)-3600;
var end2 = Math.round(new Date().getTime() / 1000);
var taxonomy2 = 'health-cardio-ecg';


function drawGraph2() {

  var m = end2 + 1;
  while(end2 < m)
  {
    end2 = Math.round(new Date().getTime() / 1000);
  } 

  if(storedData2.length > 0) {
    var lastValue = storedData2[storedData2.length - 1][1];
  }
  else{
    var lastValue = null;
  }
  

  $.post("http://uclcytoplasm.cloudapp.net/api/data/", 
    {      
      authkey: authkey,
      article: article,
      taxonomy: taxonomy2,
      start: start2,
      end: end2,
      username: username
    },
    function (data) 
    {

      if(data.error != null)
      {
        alert(data.error);
      }
      else{

      var newData = zipNewData(data);
      storedData2 = storedData2.concat(fillInBlank(start2,end2,lastValue,newData));
 
      print2_end += 1;
      printData2 = getPrintData(storedData2,print2_end,totalPoints2);
      $.plot("#placeholder2",[printData2],graphSettings);
      
      start2 = end2 + 1;
      showData2();
      drawGraph2();
      }
    });

}


function increaseScope2() {
  if(totalPoints2*2 <= 3600)
  {
    totalPoints2 = totalPoints2*2; 
    printData2 = getPrintData(storedData2,print2_end,totalPoints2);
    $.plot("#placeholder2",[printData2],graphSettings);
  }
}


function decreaseScope2() {
  if(totalPoints2/2 >= 4)
  {
    totalPoints2 = totalPoints2/2;
    printData2 = getPrintData(storedData2,print2_end,totalPoints2);
    $.plot("#placeholder2",[printData2],graphSettings);
  }
}


function getPreData2() {
  print2_end = print2_end - jump2;

  printData2 = getPrintData(storedData2,print2_end,totalPoints2);
  $.plot("#placeholder2",[printData2],graphSettings);
}


function getFwData2() {

  if(print2_end + 5 > storedData2.length - 1)
  {
    print2_end = storedData2.length -1;
  }
  else{
  print2_end = print2_end + jump2;
  }

   printData2 = getPrintData(storedData2,print2_end,totalPoints2);
  $.plot("#placeholder2",[printData2],graphSettings);
}


function showData2() {
  var newestData = document.getElementById("dataReceive2");
  if (printData2[printData2.length - 1][1] != null) 
  {
    newestData.innerText = printData2[printData2.length - 1][1];
  }
  else
  {
    newestData.innerText = "no data";
  }
  var newestTime = document.getElementById("timeReceive2");
  newestTime.innerText = unixTimeToDate(printData2[printData2.length - 1][0]/1000);
}
