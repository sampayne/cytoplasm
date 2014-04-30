function submitForm() {

  var pwd = $('#password').val();
  var shaObj = new jsSHA(pwd, "TEXT");
  var pwd_sha512 = shaObj.getHash("SHA-512", "HEX");

  $.post("http://uclcytoplasm.cloudapp.net/api/login/", {
      username: $('#username').val(),
      password: pwd_sha512
    },
    function (data) {

      var json_user = data;

      if(json_user.error == null)
      {
        localStorage.setItem("username",$('#username').val());
        localStorage.setItem("authkey",json_user.authkey);
        localStorage.setItem("name",json_user.name);
        window.location = "patientList.html";
      }
      else
      {
        alert(json_user.error);
      }
    });
}


function logout() {
  localStorage.removeItem("name");
  localStorage.removeItem("username");
  localStorage.removeItem("dob");
  localStorage.removeItem("address");
  localStorage.removeItem("patientId");
  localStorage.removeItem("authkey");
  json_patientList = null;
  number = null;

  window.location = "index.html";

}


function back() {
  window.location = "patientList.html"
}