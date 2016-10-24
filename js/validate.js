$(document).ready(function () {
   $("#name").keyup(validateAll);
   $("#email").keyup(validateAll);
   $("#username").keyup(validateAll);
   $("#password").keyup(validateAll);
   $("#cpassword").keyup(validateAll);

   $(function() {
      $('#planSelector').on('change', function(event) {
          validateAll();
      });
   });
});

/**
  Check name field...output error if empty or exceeds length.
*/
function validateNameField()
{
    if (document.getElementById("name").value.length == 0 || document.getElementById("name").value.length == 31)
    {
        return false;
    }
    return true;
}

/**
  Check email field...output error if empty or exceeds length or if email is invalid
*/
function validateEmailField() {
    var email = document.getElementById('email').value;
    var emailLength = document.getElementById('email').value.length;

    var emailFilter = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;

    if (!emailFilter.test(email) || emailLength == 0 || emailLength == 51) {
        return false;
    }
    return true;
}

/**
  Check username field...output error if empty or exceeds length.
*/
function validateUsernameField()
{
    if (document.getElementById("username").value.length == 0 || document.getElementById("username").value.length == 16)
    {
        return false;
    }
    return true;
}

/**
  Check password fields...output error if empty or exceeds length or passwords are the same.
*/
function validatePasswordFields(){
    var password = document.getElementById("password").value;
    var passwordLength = document.getElementById("password").value.length;
    var confirmPassword = document.getElementById("cpassword").value;
    var confirmPasswordLength = document.getElementById("cpassword").value.length;

    if (password != confirmPassword || passwordLength == 0 || passwordLength == 12 && confirmPasswordLength == 0 || confirmPasswordLength == 12){
        return false;
    }
    return true;
}

function validatePlanSelector(){
  var selector = document.getElementById("planSelector");
  var planOptions = selector.options[selector.selectedIndex].value;

  if (planOptions === ''){
    return false;
  }
  return true;
}

function validateAll(){
  if (validateNameField() === true && validateEmailField() === true && validateUsernameField() === true && validatePasswordFields() === true && validatePlanSelector() === true){
    document.getElementById("submitBtn").disabled = true;
  } else {
    document.getElementById("submitBtn").disabled = false;
  }
}
