<!DOCTYPE html>
<html lang="en">	
<head>
	<meta charset="UTF-8">
	<meta content="IE=edge" http-equiv="X-UA-Compatible">
	<meta content="initial-scale=1.0, width=device-width" name="viewport">
	<meta name="theme-color" content="#2962FF">
	<meta name="description" content="Hospital System">
	<meta name="keywords" content="eHospital System">
	<title>Physician Home</title>

	<!-- CSS Bootstrap -->
	<link href="css/base.css" rel="stylesheet">
	<!-- Material Design Icons -->
	<link href="add-script/fonts/material-design-iconic-font/css/material-design-iconic-font.min.css" rel="stylesheet">
	<!-- Google Font -->
	<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600' rel='stylesheet' type='text/css'>
	<!-- Font Awesome -->
	<link href="http://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">
	<!-- Iconicons -->
	<link href="add-script/fonts/ionicons/css/ionicons.min.css" rel="stylesheet">
	<!-- Owl Carousel -->
	<link href="add-script/css/owl.carousel.css" rel="stylesheet">
	<link href="add-script/css/owl.theme.default.css" rel="stylesheet">
	<!-- Style.css -->
	<link href="add-script/css/style.css" rel="stylesheet">


	<link href="css/styler.css" rel="stylesheet">


</head>
	
<body>

	<header class="header">
		<a class = "head-title" href="./index.php"><strong>Physician Home</strong></a>   
	</header>

	<style>
		table, th, td {
    	border: 2px solid black;
    	border-collapse: collapse;
    	margin-left: : 20px;
		}
		th, td {
    	padding: 5px;
    	text-align: center;
		}
	</style>

	<hgroup>
  		<h1>Search Patient</h1>
	</hgroup>
	
	<!-- Form Code Start -->
	<div id='fg_membersite'>
	<form id='form1' name="form1" method='post' accept-charset='UTF-8' action=''>
	<input type='hidden' name='submitted' id='submitted' value='1'/>
  		<div class="group">
    		<input type='text' id='patient_id' name='patient_id' value='' class="form-control" maxlength="50" ><span class="highlight"></span>
    		<span class="bar"></span>
    		<label>Patient ID</label>
  		</div>
  		<button type='submit' name='view' id='view' class="button buttonBlue">Search
    	<div class="ripples buttonRipples"><span class="ripplesCircle"></span></div>
  		</button>

  		<a class = 'head-title' type='submit' href='./update_prescription.php' name='edit' id='edit' class="">EDIT RECORD</a> <br>
  		<a class = 'head-title' type='submit' href='./add_appointment.php' name='edit' id='edit' class="">ADD APPOINTMENT</a> <br>
  		<a class = 'head-title' type='submit' href='./employee_login.php' name='logout' id='logout' class="">LOGOUT</a>
	</form>

<?php

// Make sure you edit php.ini file which you can find it inside your webserver php folder.
// Open the php.ini file and enable soap client to avoid having errors.
//$client = new SoapClient("patient_home.wsdl");

if(isset($_POST['view']))
{ 
	$patientId = $_POST['patient_id'];

	$database = mysqli_connect("127.0.0.1", "root", "1234567890") or die("Could not open database");
  	mysqli_select_db($database, "ehospital") or die("Could not select database");

   if($patientId ==""||$patientId==null){
	  $query = "SELECT `patient`.`patient_id`, `patient`.`name`, `patient`.`lastname`, `patient`.`gender`, `patient`.`email`, `patient`.`birthday`, `prescription`.`prescription_id`, `prescription`.`prescription_title`,`prescription`.`prescription_date`, `employee`.`employee_lname`, `employee`.`employee_type`, `medical_result`.`result_title` FROM `medical_result` INNER JOIN `patient` ON `medical_result`.`patient_id` = `patient`.`patient_id` INNER JOIN `employee` ON `medical_result`.`employee_id` = `employee`.`employee_id` INNER JOIN `prescription` ON `medical_result`.`prescription_id` = `prescription`.`prescription_id`";
	}
	else {
	  $query = "SELECT `patient`.`patient_id`, `patient`.`name`, `patient`.`lastname`, `patient`.`gender`, `patient`.`email`, `patient`.`birthday`, `prescription`.`prescription_id`, `prescription`.`prescription_title`,`prescription`.`prescription_date`, `employee`.`employee_lname`, `employee`.`employee_type`, `medical_result`.`result_title` FROM `medical_result` INNER JOIN `patient` ON `medical_result`.`patient_id` = `patient`.`patient_id` INNER JOIN `employee` ON `medical_result`.`employee_id` = `employee`.`employee_id` INNER JOIN `prescription` ON `medical_result`.`prescription_id` = `prescription`.`prescription_id` WHERE `medical_result`.`patient_id` = '$patientId';";
	}
  
  $result = mysqli_query($database, $query) or die("Could not execute sql query");
  mysqli_close($database);

  	//$count = 0;
	$response = "<table border=5>
		 			<tr><th>Patient ID</th>
		 				<th>First Name</th>
		 				<th>Last Name</th>
		 				<th>Gender</th>
    					<th>Email</th>
						<th>Birthday</th>
						<th>Prescription ID</th>
						<th>Prescription Title</th>
						<th>Prescription Date</th>
						<th>Employee Name</th>
						<th>Employee Role</th>
						<th>Result</th>
    				</tr>";

	if($result === false) 
    	return "Unable to execute query";
	else
	$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	foreach( $result as $row ) 
	 	 $response = $response.'
		  <tr>
			<td>'.$row['patient_id'].'</td>
    		<td>'.$row['name'].'</td>
    		<td>'.$row['lastname'].'</td>
    		<td>'.$row['gender'].'</td>
    		<td>'.$row['email'].'</td>
			<td>'.$row['birthday'].'</td>
			<td>'.$row['prescription_id'].'</td>
    		<td>'.$row['prescription_title'].'</td>
    		<td>'.$row['prescription_date'].'</td>
    		<td>'.$row['employee_lname'].'</td>
    		<td>'.$row['employee_type'].'</td>
    		<td>'.$row['result_title'].'</td>
		  </tr>';

		//$count++;
	$response = $response.'</table>';
  	echo $response;
  }

?>
	

<!-- client-side Form Validations:
Uses the excellent form validation script from JavaScript-coder.com-->
<script>
    function submitForm(action)
    {
        document.getElementById('form1').action = action;
        document.getElementById('form1').submit();
    }
</script>

<script type='text/javascript'>
// <![CDATA[

    var frmvalidator  = new Validator("login");
    frmvalidator.EnableOnPageErrorDisplay();
    frmvalidator.EnableMsgsTogether();

    frmvalidator.addValidation("email","req","Please provide your email address");
    
    frmvalidator.addValidation("password","req","Please provide the password");

// ]]>
</script>
</div>
<!--
Form Code End (see html-form-guide.com for more info.)
-->

	
	
	<!-- Base Javascript -->
	<script src="js/base.js" type="text/javascript"></script>
	

    <!-- jQuery -->
	<script src="add-script/js/jquery-1.11.2.min.js" type="text/javascript"></script>
	<!-- Inview -->
	<script src="add-script/js/jquery.inview.min.js" type="text/javascript"></script>
	<!-- SmoothScroll -->
	<script src="add-script/js/smoothscroll.js" type="text/javascript"></script>
	<!-- jQuery Knob -->
	<script src="add-script/js/jquery.knob.min.js" type="text/javascript"></script>
	<!-- Owl Carousel -->
	<script src="add-script/js/owl.carousel.min.js" type="text/javascript"></script>
	<!-- Isotope -->
	<script src="add-script/js/isotope.pkgd.min.js" type="text/javascript"></script>
	<!-- Images Loaded -->
	<script src="add-script/js/imagesloaded.pkgd.min.js" type="text/javascript"></script>
	<!-- google maps -->
	<script src="https://maps.googleapis.com/maps/api/js?v=3.exp"></script>
	<!-- Scripts.js -->
	<script src="add-script/js/scripts.js"></script>

	<script src="add-script/js/bootstrap.js"></script>



	</body>
</html>