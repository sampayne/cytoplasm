function addText(target,text) {

    var myTarget = document.getElementById(target);
    myTarget.innerText = text;

} 

function clickId(patient) {

  localStorage.setItem("patientId", patient.id);
  window.location = "patientDetails.html";

}


function createTable(result) {

  var str = '<table><tr><td>ID</td><td>Name</td><td>Date of birth</td><td>Address</td></tr>';
  for (var i = 0; i < result.length; i++) 
  {
    if(i%4 === 0){
      if (result[i] === 'ID')
        {
          str += '<tr><td>'+ result[i] + '</td>';
        }
      else
        {
        str += '<tr><td>'+ '<button id='+ result[i] + ' onclick = clickId(this)> ' + result[i] +'</button></td>';
        }
    }
    else{
      if(i%4 === 3){
        str += '<td>'+ result[i] + '</td></tr>';
      }
      else
      {
        str += '<td>'+ result[i] + '</td>';
      }
    }
  }
  str += '</table>';
  return str;

}

function createPatientDetails(result) {

  var str ='';
  for (var i = 0; i < result.length; i++) {
      if(result[i] === "HeartRateSensor")
      {
        str += '<button id='+ result[i] + ' onclick = drawSensorGraph()> ' + result[i] +'</button></td>';
      }
      else
      {
      str += result[i];
      }
      if(i%2 === 1)
      {
        str += '<br>';
      } 
    }
    return str;

}


function submitForm() {

   $.post("http://comp2013.hyperspacedesign.co.uk/api/login/index.php" ,
   {
    username : $('#username').val(),
    password : $('#password').val()
  },
  function(data)
  {
    if( data == "LOGIN_SUCCESSFUL")
    {
      localStorage.setItem("username", $('#username').val());
      localStorage.setItem("password", $('#password').val());
      window.location = "patientList.html";
    }
    else
    {
      addText('responseText',data);
    } 
  });

}

function loadPatientList() {

  var username = localStorage.getItem("username");
  var password = localStorage.getItem("password");
  
  $.post("http://comp2013.hyperspacedesign.co.uk/api/articles/index.php" ,
    {
      username : username,
      password : password
    },
    function(data)
    {
      var patientList = data.split('/');
      var table = document.getElementById("responseTextA");
      table.innerHTML = createTable(patientList);
    });

}

function loadPatientDetails() {

  var patientId = localStorage.getItem("patientId");

  $.post("patientDetails.php" ,
   {
    userId : patientId
  },
  function(data)
  {
    var detail = data.split('/');
    var patientDetails = document.getElementById("responseTextB");
    patientDetails.innerHTML =  createPatientDetails(detail);
  });

}

  
function drawSensorGraph() {
  
  var data = [],
      totalPoints = 300;

  var username = localStorage.getItem("username");
  var password = localStorage.getItem("password");
  var start = '';
  var end = '';
  var article = '40';
  var taxonomy = 'health-cardio-heartrate';
  var y = '';

    function getData() {

      if (data.length > 0)
        data = data.slice(1);

      // Do a random walk

      while (data.length < totalPoints) {

        $.post("http://comp2013.hyperspacedesign.co.uk/api/data/index.php" ,
        {
          start : start,
          end  : end,
          username : username,
          password : password,
          article : article,
          taxonomy : taxonomy
        },
        function(data_rec)
        {
          var readValues = data_rec.split('/');
          y = readValues[0];
      
        });

        data.push(y);
      }

      // Zip the generated y values with the x values

      var res = [];
      for (var i = 0; i < data.length; ++i) {
        res.push([i, data[i]])
      }

      return res;
    }

    // Set up the control widget

    var updateInterval = 6000;
    

    var plot = $.plot("#placeholder", [ getData() ], {
      series: {
        shadowSize: 0 // Drawing is faster without shadows
      },
      yaxis: {
        min: 0,
        max: 200
      },
      xaxis: {
        show: false
      }
    });

    function update() {

      plot.setData([getData()]);

      // Since the axes don't change, we don't need to call plot.setupGrid()

      plot.draw();
      setTimeout(update, updateInterval);
    }

    update();


  
}

/*function testRec() {

  var username = localStorage.getItem("username");
  var password = localStorage.getItem("password");
  var start = '';
  var end = '';
  var article = '40';
  var taxonomy = 'health-cardio-heartrate';


  $.post("http://comp2013.hyperspacedesign.co.uk/api/data/index.php" ,
  {
    start : start,
    end  : end,
    username : username,
    password : password,
    article : article,
    taxonomy : taxonomy
  },
  function(data_rec)
  {
    if(data_rec == 'null')
      {addText(dataRecieve,'CAN NOT RECEIVE DATA');}
    else{
    addText(dataRecieve,data_rec);}

  });

}*/