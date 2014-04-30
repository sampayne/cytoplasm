var json_patientList = null;
var number = null;

function addText(target, text) {
  var myTarget = document.getElementById(target);
  myTarget.innerText = text;
}


function searchById(id) {
  for (var i = 0; i < number; i ++) {
    if (json_patientList.articles[i].id === id) {
      return i;
    }
  }
  return 'error';
}


function clickId(patient) {
  var i = searchById(patient.id);

  var addition_fields = json_patientList.articles[i].additional_fields.split("/");
  var dob = addition_fields[0];
  var address = addition_fields[1];

  if (i != 'error') {
    localStorage.setItem("patientId", json_patientList.articles[i].id);
    localStorage.setItem("name", json_patientList.articles[i].name);
    localStorage.setItem("dob", dob);
    localStorage.setItem("address", address);
  }
  else {
    alert("Error! Please try again.")
  }
  window.location = "patientDetails.html";
}


function createTable(json_table) {
  var str = "<table><tr><td>ID</td><td>Name</td><td>Date of birth</td><td>Address</td>";
  number = Object.keys(json_table.articles).length;


  for (var i = 0; i < number; i++) 
  {

    var addition_fields = json_table.articles[i].additional_fields.split("/");
    var dob = addition_fields[0];
    var address = addition_fields[1];

    if (i % 2 == 0) 
    {
      str += '<tr class=even id=' + json_table.articles[i].id + ' onclick = clickId(this) ><td>' + json_table.articles[i].id + '</td> <td>' + json_table.articles[i].name 
      + '</td> <td>' + dob + '</td> <td>' + address + '</td> </tr>' ;
    } 
    else 
    {
       str += '<tr id=' + json_table.articles[i].id + ' onclick = clickId(this) ><td>' + json_table.articles[i].id + '</td> <td>' + json_table.articles[i].name 
      + '</td> <td>' + dob + '</td> <td>' + address + '</td> </tr>' ;
    }
  };
  
  str += '</table>';
  return str;
}


function loadPatientList() {
  var username = localStorage.getItem("username");
  var authkey = localStorage.getItem("authkey");
  $.post("http://uclcytoplasm.cloudapp.net/api/articles/", {
      username: username,
      authkey: authkey
    },
    function (data) {

      json_patientList = data;

      var table = document.getElementById("responseTextA");
      table.innerHTML = createTable(json_patientList);
    });
}


function createPatientDetails(result) {
  var str = '';
  for (var i = 0; i < result.length; i++) {
    str += result[i];
    if (i % 2 === 1) {
      str += '<br>';
    }
  }
  return str;
}


function loadPatientDetails() {
  var patientId = localStorage.getItem("patientId");
  var name = localStorage.getItem("name");
  var dob = localStorage.getItem("dob");
  var address = localStorage.getItem("address");
  var detail = 'ID: /' + patientId + '/Name: /' + name + '/Date Of Birth: /' + dob + '/Address: /' + address;
  var detailList = detail.split('/');
  var patientDetails = document.getElementById("responseTextB");
  patientDetails.innerHTML = createPatientDetails(detailList);
  
  
  drawGraph1();
  drawGraph2();
  drawGraph3();
  drawGraph4();
}