var printData1 = [];
var storedData1 = [];
var totalPoints1 = 60;
var print1_end = 3600;
var jump1 = 60;
var start1 = Math.round(new Date().getTime() / 1000)-3600;
var end1 = Math.round(new Date().getTime() / 1000);
var taxonomy1 = 'health-cardio-heartrate';


function drawGraph1() {

  //make sure end increase
  var m = end1 + 1;
  while(end1 < m)
  {
    end1 = Math.round(new Date().getTime() / 1000);
  } 

  if(storedData1.length > 0) {
    var lastValue = storedData1[storedData1.length - 1][1];
  }
  else{
    var lastValue = null;
  }

  $.post("http://uclcytoplasm.cloudapp.net/api/data/", 
    {      
      authkey: authkey,
      article: article,
      taxonomy: taxonomy1,
      start: start1,
      end: end1,
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
      storedData1 = storedData1.concat(fillInBlank(start1,end1,lastValue,newData));

      print1_end += 1;
      printData1 = getPrintData(storedData1,print1_end,totalPoints1);
      $.plot("#placeholder1",[printData1],graphSettings);
      
      start1 = end1 + 1;
      showData1();
      drawGraph1();
      }
    });
}


function increaseScope1() {
  if(totalPoints1*2 <= 3600)
  {
    totalPoints1 = totalPoints1*2; 
    printData1 = getPrintData(storedData1,print1_end,totalPoints1);
    $.plot("#placeholder1",[printData1],graphSettings);
  }
}


function decreaseScope1() {
  if(totalPoints1/2 >= 4)
  {
    totalPoints1 = totalPoints1/2;
    printData1 = getPrintData(storedData1,print1_end,totalPoints1);
    $.plot("#placeholder1",[printData1],graphSettings);
  }
}


function getPreData1() {
  print1_end = print1_end - jump1;

  printData1 = getPrintData(storedData1,print1_end,totalPoints1);
  $.plot("#placeholder1",[printData1],graphSettings);


}

function getFwData1() {

  if(print1_end + jump1 > storedData1.length - 1)
  {
    print1_end = storedData1.length -1;
  }
  else{
  print1_end = print1_end + jump1;
  }

  printData1 = getPrintData(storedData1,print1_end,totalPoints1);
  $.plot("#placeholder1",[printData1],graphSettings);
}


function showData1() {

  var newestData = document.getElementById("dataReceive1");
  if (printData1[printData1.length - 1][1] != null) 
  {
    newestData.innerText = printData1[printData1.length - 1][1];
  }
  else
  {
    newestData.innerText = "no data";
  }
  var newestTime = document.getElementById("timeReceive1");
  newestTime.innerText = unixTimeToDate(printData1[printData1.length - 1][0]/1000);


}

