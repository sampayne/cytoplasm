var printData3 = [];
var storedData3 = [];
var totalPoints3 = 60;
var print3_end = 3600;
var jump3 = 60;
var start3 = Math.round(new Date().getTime() / 1000)-3600;
var end3 = Math.round(new Date().getTime() / 1000);
var taxonomy3 = 'health-general-activity';


function drawGraph3() {

  var m = end3 + 1;
  while(end3 < m)
  {
    end3 = Math.round(new Date().getTime() / 1000);
  } 

  if(storedData3.length > 0) {
    var lastValue = storedData3[storedData3.length - 1][1];
  }
  else{
    var lastValue = null;
  }
  

  $.post("http://uclcytoplasm.cloudapp.net/api/data/", 
    {      
      authkey: authkey,
      article: article,
      taxonomy: taxonomy3,
      start: start3,
      end: end3,
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
      storedData3 = storedData3.concat(fillInBlank(start3,end3,lastValue,newData));
      
      print3_end += 1;
      printData3 = getPrintData(storedData3,print3_end,totalPoints3);
      $.plot("#placeholder3",[printData3],graphSettings);
      
      start3 = end3 + 1;
      showData3();
      drawGraph3();
      }
    });

}


function increaseScope3() {
  if(totalPoints3*2 <= 3600)
  {
    totalPoints3 = totalPoints3*2; 
    printData3 = getPrintData(storedData3,print3_end,totalPoints3);
    $.plot("#placeholder3",[printData3],graphSettings);
  }
}


function decreaseScope3() {
  if(totalPoints3/2 >= 4)
  {
    totalPoints3 = totalPoints3/2;
    printData3 = getPrintData(storedData3,print3_end,totalPoints3);
    $.plot("#placeholder3",[printData3],graphSettings);
  }
}


function getPreData3() {
  print3_end = print3_end - jump3;

  printData3 = getPrintData(storedData3,print3_end,totalPoints3);
  $.plot("#placeholder3",[printData3],graphSettings);
}


function getFwData3() {

  if(print3_end + 5 > storedData3.length - 1)
  {
    print3_end = storedData3.length -1;
  }
  else{
  print3_end = print3_end + jump3;
  }

   printData3 = getPrintData(storedData3,print3_end,totalPoints3);
  $.plot("#placeholder3",[printData3],graphSettings);
}


function showData3() {
  var newestData = document.getElementById("dataReceive3");
  if (printData3[printData3.length - 1][1] != null) 
  {
    newestData.innerText = printData3[printData3.length - 1][1];
  }
  else
  {
    newestData.innerText = "no data";
  }
  var newestTime = document.getElementById("timeReceive3");
  newestTime.innerText = unixTimeToDate(printData3[printData3.length - 1][0]/1000);
}
