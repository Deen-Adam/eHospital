<?PHP

require_once("class.phpmailer.php");
require_once("formvalidator.php");

class FGMembersite
{
    var $admin_email;
    var $from_address;
    
    var $username;
    var $pwd;
    var $database;
    var $tablename;
    var $connection;
    var $rand_key;
    
    var $error_message;

    var $joined_date;
    
    //-----Initialization -------
    function FGMembersite1()
    {
        $this->sitename = 'http://deenysoft.com';
        $this->rand_key = '0iQx5oBk66oVZep';
    }
    
    function InitDB($host,$uname,$pwd,$database,$tablename)
    {
        $this->db_host  = $host;
        $this->username = $uname;
        $this->pwd  = $pwd;
        $this->database  = $database;
        $this->tablename = $tablename;
        
    }
    function SetAdminEmail($email)
    {
        $this->admin_email = $email;
    }
    
    function SetWebsiteName($sitename)
    {
        $this->sitename = $sitename;
    }
    
    function SetRandomKey($key)
    {
        $this->rand_key = $key;
    }
    
    //-------Main Operations ----------------------
    function RegisterUser()
    {
        if(!isset($_POST['submitted']))
        {
            return false;
        }
        
        $formvars = array();
        
        if(!$this->ValidateRegistrationSubmission())
        {
            return false;
        }
        
        $this->CollectRegistrationSubmission($formvars);
        
        if(!$this->SaveToDatabase($formvars))
        {
            return false;
        }
        
        /*
        if(!$this->SendUserConfirmationEmail($formvars))
        {
            return false;
        }

        $this->SendAdminIntimationEmail($formvars);
        */
        
        return true;
    }

    function ConfirmUser()
    {
        if(empty($_GET['code'])||strlen($_GET['code'])<=10)
        {
            $this->HandleError("Please provide the confirm code");
            return false;
        }
        $user_rec = array();
        if(!$this->UpdateDBRecForConfirmation($user_rec))
        {
            return false;
        }
        
        $this->SendUserWelcomeEmail($user_rec);
        
        $this->SendAdminIntimationOnRegComplete($user_rec);
        
        return true;
    }    
    
    function Login()
    {
        if(empty($_POST['email']))
        {
            $this->HandleError("Email address is empty!");
            return false;
        }
        
        if(empty($_POST['password']))
        {
            $this->HandleError("Password is empty!");
            return false;
        }
        
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        
        if(!isset($_SESSION)){ session_start(); }
        if(!$this->CheckLoginInDB($email,$password))
        {
            return false;
        }
        
        $_SESSION[$this->GetLoginSessionVar()] = $email;
        
        return true;
    }
    
    function CheckLogin()
    {
         if(!isset($_SESSION)){ session_start(); }

         $sessionvar = $this->GetLoginSessionVar();
         
         if(empty($_SESSION[$sessionvar]))
         {
            return false;
         }
         return true;
    }
    
    function UserFullName()
    {
        return isset($_SESSION['name_of_user'])?$_SESSION['name_of_user']:'';
    }
    
    function UserEmail()
    {
        return isset($_SESSION['email_of_user'])?$_SESSION['email_of_user']:'';
    }
    
    function LogOut()
    {
        session_start();
        
        $sessionvar = $this->GetLoginSessionVar();
        
        $_SESSION[$sessionvar]=NULL;
        
        unset($_SESSION[$sessionvar]);
    }
    
    function EmailResetPasswordLink()
    {
        if(empty($_POST['email']))
        {
            $this->HandleError("Email is empty!");
            return false;
        }
        $user_rec = array();
        if(false === $this->GetUserFromEmail($_POST['email'], $user_rec))
        {
            return false;
        }
        if(false === $this->SendResetPasswordLink($user_rec))
        {
            return false;
        }
        return true;
    }
    
    function ResetPassword()
    {
        if(empty($_GET['email']))
        {
            $this->HandleError("Email is empty!");
            return false;
        }
        if(empty($_GET['code']))
        {
            $this->HandleError("reset code is empty!");
            return false;
        }
        $email = trim($_GET['email']);
        $code = trim($_GET['code']);
        
        if($this->GetResetPasswordCode($email) != $code)
        {
            $this->HandleError("Bad reset code!");
            return false;
        }
        
        $user_rec = array();
        if(!$this->GetUserFromEmail($email,$user_rec))
        {
            return false;
        }
        
        $new_password = $this->ResetUserPasswordInDB($user_rec);
        if(false === $new_password || empty($new_password))
        {
            $this->HandleError("Error updating new password");
            return false;
        }
        
        if(false == $this->SendNewPassword($user_rec,$new_password))
        {
            $this->HandleError("Error sending new password");
            return false;
        }
        return true;
    }
    
    function ChangePassword()
    {
        if(!$this->CheckLogin())
        {
            $this->HandleError("Not logged in!");
            return false;
        }
        
        if(empty($_POST['oldpwd']))
        {
            $this->HandleError("Old password is empty!");
            return false;
        }
        if(empty($_POST['newpwd']))
        {
            $this->HandleError("New password is empty!");
            return false;
        }
        
        $user_rec = array();
        if(!$this->GetUserFromEmail($this->UserEmail(),$user_rec))
        {
            return false;
        }
        
        $pwd = trim($_POST['oldpwd']);

    	$salt = $user_rec['salt'];
        $hash = $this->checkhashSSHA($salt, $pwd);
        
        if($user_rec['password'] != $hash)
        {
            $this->HandleError("The old password does not match!");
            return false;
        }
        $newpwd = trim($_POST['newpwd']);
        
        if(!$this->ChangePasswordInDB($user_rec, $newpwd))
        {
            return false;
        }
        return true;
    }
    
    //-------Public Helper functions -------------
    function GetSelfScript()
    {
        return htmlentities($_SERVER['PHP_SELF']);
    }    
    
    function SafeDisplay($value_name)
    {
        if(empty($_POST[$value_name]))
        {
            return'';
        }
        return htmlentities($_POST[$value_name]);
    }
    
    function RedirectToURL($url)
    {
        header("Location: $url");
        exit;
    }
    
    function GetSpamTrapInputName()
    {
        return 'sp'.md5('KHGdnbvsgst'.$this->rand_key);
    }
    
    function GetErrorMessage()
    {
        if(empty($this->error_message))
        {
            return '';
        }
        $errormsg = nl2br(htmlentities($this->error_message));
        return $errormsg;
    }    
    //-------Private Helper functions-----------
    
    function HandleError($err)
    {
        $this->error_message .= $err."\r\n";
    }
    
    function HandleDBError($err)
    {
        $this->HandleError($err."\r\n mysqlerror:".mysqli_error($this->connection));
    }
    
    function GetFromAddress()
    {
        if(!empty($this->from_address))
        {
            return $this->from_address;
        }

        $host = $_SERVER['SERVER_NAME'];

        $from ="nobody@$host";
        return $from;
    } 
    
    function GetLoginSessionVar()
    {
        $retvar = md5($this->rand_key);
        $retvar = 'usr_'.substr($retvar,0,10);
        return $retvar;
    }
    
    function CheckLoginInDB($email,$password)
    {
        if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }          
        $email = $this->SanitizeForSQL($email);

  	    $nresult = mysqli_query($this->connection, "SELECT * FROM $this->tablename WHERE email = '$email'") or die(mysqli_error());
        // check for result 
        $no_of_rows = mysqli_num_rows($nresult);
        if ($no_of_rows > 0) {
            $nresult = mysqli_fetch_array($nresult);
            $salt = $nresult['salt'];
            $encrypted_password = $nresult['password'];
            $hash = $this->checkhashSSHA($salt, $password);
        
        }

        $qry = "SELECT name, email FROM $this->tablename WHERE email = '$email' AND password = '$hash'";
        
        $result = mysqli_query($this->connection, $qry);
        
        if(!$result || mysqli_num_rows($result) <= 0)
        {
            $this->HandleError("The email address or password does not match.");
            return false;
        }
        
        $row = mysqli_fetch_assoc($result);
        
        
        $_SESSION['name_of_user']  = $row['name'];
        $_SESSION['email_of_user'] = $row['email'];
        
        return true;
    }

 public function checkhashSSHA($salt, $password) {
 
        $hash = base64_encode(sha1($password . $salt, true) . $salt);
 
        return $hash;
    }
    
    function UpdateDBRecForConfirmation(&$user_rec)
    {
        if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }   
        $confirmcode = $this->SanitizeForSQL($_GET['code']);
        
        $result = mysqli_query($this->connection, "Select name, email from $this->tablename where confirmcode='$confirmcode'");   
        if(!$result || mysqli_num_rows($result) <= 0)
        {
            $this->HandleError("Wrong confirm code.");
            return false;
        }
        $row = mysqli_fetch_assoc($result);
        $user_rec['name'] = $row['name'];
        $user_rec['email']= $row['email'];
        
        $qry = "Update $this->tablename Set confirmcode='y' Where  confirmcode='$confirmcode'";
        
        if(!mysqli_query($this->connection, $qry))
        {
            $this->HandleDBError("Error inserting data to the table\nquery:$qry");
            return false;
        }      
        return true;
    }
    
    function ResetUserPasswordInDB($user_rec)
    {
        $new_password = substr(md5(uniqid()),0,10);
        
        if(false == $this->ChangePasswordInDB($user_rec,$new_password))
        {
            return false;
        }
        return $new_password;
    }
    
    function ChangePasswordInDB($user_rec, $newpwd)
    {
        $newpwd = $this->SanitizeForSQL($newpwd);

        $hash = $this->hashSSHA($newpwd);

	$new_password = $hash["encrypted"];

	$salt = $hash["salt"];
        
        $qry = "Update $this->tablename Set password='".$new_password."', salt='".$salt."' Where  patient_id=".$user_rec['patient_id']."";
        
        if(!mysqli_query($this->connection, $qry))
        {
            $this->HandleDBError("Error updating the password \nquery:$qry");
            return false;
        }     
        return true;
    }
    
    function GetUserFromEmail($email,&$user_rec)
    {
        if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }   
        $email = $this->SanitizeForSQL($email);
        
        $result = mysqli_query($this->connection, "Select * from $this->tablename where email='$email'");  

        if(!$result || mysqli_num_rows($result) <= 0)
        {
            $this->HandleError("There is no user with email: $email");
            return false;
        }
        $user_rec = mysqli_fetch_assoc($result);

        
        return true;
    }
    
    function SendUserWelcomeEmail(&$user_rec)
    {
        $mailer = new PHPMailer();
        
        $mailer->CharSet = 'utf-8';
        
        $mailer->AddAddress($user_rec['email'],$user_rec['name']);
        
        $mailer->Subject = "Welcome to ".$this->sitename;

        $mailer->From = $this->GetFromAddress();        
        
        $mailer->Body ="Hello ".$user_rec['name']."\r\n\r\n".
        "Welcome! Your registration  with ".$this->sitename." is completed.\r\n".
        "\r\n".
        "Regards,\r\n".
        "Deen Adam\r\n".
        "Deenysoft\r\n".
        $this->sitename;

        if(!$mailer->Send())
        {
            $this->HandleError("Failed sending user welcome email.");
            return false;
        }
        return true;
    }
    
    function SendAdminIntimationOnRegComplete(&$user_rec)
    {
        if(empty($this->admin_email))
        {
            return false;
        }
        $mailer = new PHPMailer();
        
        $mailer->CharSet = 'utf-8';
        
        $mailer->AddAddress($this->admin_email);
        
        $mailer->Subject = "Registration Completed: ".$user_rec['name'];

        $mailer->From = $this->GetFromAddress();         
        
        $mailer->Body ="A new user registered at ".$this->sitename."\r\n".
        "Name: ".$user_rec['name']."\r\n".
        "Email address: ".$user_rec['email']."\r\n";
        
        if(!$mailer->Send())
        {
            return false;
        }
        return true;
    }
    
    function GetResetPasswordCode($email)
    {
       return substr(md5($email.$this->sitename.$this->rand_key),0,10);
    }
    
    function SendResetPasswordLink($user_rec)
    {
        $email = $user_rec['email'];
        
        $mailer = new PHPMailer();
        
        $mailer->CharSet = 'utf-8';
        
        $mailer->AddAddress($email,$user_rec['name']);
        
        $mailer->Subject = "Your reset password request at ".$this->sitename;

        $mailer->From = $this->GetFromAddress();
        
        $link = $this->GetAbsoluteURLFolder().
                '/resetpwd.php?email='.
                urlencode($email).'&code='.
                urlencode($this->GetResetPasswordCode($email));

        $mailer->Body ="Hello ".$user_rec['name']."\r\n\r\n".
        "There was a request to reset your password at ".$this->sitename."\r\n".
        "Please click the link below to complete the request: \r\n".$link."\r\n".
        "Regards,\r\n".
        "Deenysoft\r\n".
        $this->sitename;
        
        if(!$mailer->Send())
        {
            return false;
        }
        return true;
    }
    
    function SendNewPassword($user_rec, $new_password)
    {
        $email = $user_rec['email'];
        
        $mailer = new PHPMailer();
        
        $mailer->CharSet = 'utf-8';
        
        $mailer->AddAddress($email,$user_rec['name']);
        
        $mailer->Subject = "Your new password for ".$this->sitename;

        $mailer->From = $this->GetFromAddress();
        
        $mailer->Body ="Hello ".$user_rec['name']."\r\n\r\n".
        "Your password is reset successfully. ".
        "Here is your updated login:\r\n".
        "username:".$user_rec['email']."\r\n".
        "password:$new_password\r\n".
        "\r\n".
        "Login here: ".$this->GetAbsoluteURLFolder()."/login.php\r\n".
        "\r\n".
        "Regards,\r\n".
        "Deen Adam\r\n".
        $this->sitename;
        
        if(!$mailer->Send())
        {
            return false;
        }
        return true;
    }    
    
    function ValidateRegistrationSubmission()
    {
        //This is a hidden input field. Humans won't fill this field.
        if(!empty($_POST[$this->GetSpamTrapInputName()]) )
        {
            //The proper error is not given intentionally
            $this->HandleError("Automated submission prevention: case 2 failed");
            return false;
        }
        
        $validator = new FormValidator();
        $validator->addValidation("name","req","Please fill in Name");
        $validator->addValidation("email","email","The input for Email should be a valid email value");
        $validator->addValidation("email","req","Please fill in Email");
  
        $validator->addValidation("password","req","Please fill in Password");

        
        if(!$validator->ValidateForm())
        {
            $error='';
            $error_hash = $validator->GetErrors();
            foreach($error_hash as $inpname => $inp_err)
            {
                $error .= $inpname.':'.$inp_err."\n";
            }
            $this->HandleError($error);
            return false;
        }        
        return true;
    }
    
    function CollectRegistrationSubmission(&$formvars)
    {
        $formvars['name'] = $this->Sanitize($_POST['name']);
        $formvars['lastname'] = $this->Sanitize($_POST['lastname']);
        $formvars['email'] = $this->Sanitize($_POST['email']);
        $formvars['password'] = $this->Sanitize($_POST['password']);
        $formvars['gender'] = $this->Sanitize($_POST['gender']);
        $formvars['birthday'] = $this->Sanitize($_POST['birthday']);
        $formvars['address'] = $this->Sanitize($_POST['address']);
   
    }
    
    function SendUserConfirmationEmail(&$formvars)
    {
        $mailer = new PHPMailer();
        
        $mailer->CharSet = 'utf-8';
        
        $mailer->AddAddress($formvars['email'],$formvars['name']);
        
        $mailer->Subject = "Your registration with ".$this->sitename;

        $mailer->From = $this->GetFromAddress();       
        
        $confirmcode = $formvars['confirmcode'];
        
        $confirm_url = $this->GetAbsoluteURLFolder().'/confirm.php?code='.$confirmcode;
        
        $mailer->Body ="Hello ".$formvars['name']."\r\n\r\n".
        "Thanks for your registration with ".$this->sitename."\r\n".
        "Please click the link below to confirm your registration.\r\n".
        "$confirm_url\r\n".
        "\r\n".
        "Regards,\r\n".
        "Deen Adam\r\n".
        $this->sitename;

        if(!$mailer->Send())
        {
            $this->HandleError("Failed sending registration confirmation email.");
            return false;
        }
        return true;
    }
    function GetAbsoluteURLFolder()
    {
        $scriptFolder = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')) ? 'https://' : 'http://';

        $urldir ='';
        $pos = strrpos($_SERVER['REQUEST_URI'],'/');
        if(false !==$pos)
        {
            $urldir = substr($_SERVER['REQUEST_URI'],0,$pos);
        }

        $scriptFolder .= $_SERVER['HTTP_HOST'].$urldir;

        return $scriptFolder;
    }
    
    function SendAdminIntimationEmail(&$formvars)
    {
        if(empty($this->admin_email))
        {
            return false;
        }
        $mailer = new PHPMailer();
        
        $mailer->CharSet = 'utf-8';
        
        $mailer->AddAddress($this->admin_email);
        
        $mailer->Subject = "New Registration: ".$formvars['name'];

        $mailer->From = $this->GetFromAddress();         
        
        $mailer->Body ="A new user registered at ".$this->sitename."\r\n".
        "Name: ".$formvars['name']."\r\n".
        "Email address: ".$formvars['email']."\r\n".
        "UserName: ".$formvars['email'];
        
        if(!$mailer->Send())
        {
            return false;
        }
        return true;
    }
    
    function SaveToDatabase(&$formvars)
    {
        if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }
        if(!$this->Ensuretable())
        {
            return false;
        }
        if(!$this->IsFieldUnique($formvars,'email'))
        {
            $this->HandleError("The Email address is already registered.");
            return false;
        }
        
	if(!$this->IsFieldUnique($formvars,'email'))
        {
            $this->HandleError("The Username is already taken. Please try again!");
            return false;
        } 
              
        if(!$this->InsertIntoDB($formvars))
        {
            $this->HandleError("Inserting to Database failed!");
            return false;
        }

        if(strlen($_POST['password']) <= 5) {
            $this->HandleError("Password is too short");
        }

        if(strlen($_POST['cpassword']) <= 5) {
            $this->HandleError("Confirm password is too short.");
        }

        if($_POST['password'] != $_POST['cpassword']) {
            $this->HandleError("Passwords do not match.");
        } 

        return true;
    }
    
    function IsFieldUnique($formvars,$fieldname)
    {
        $field_val = $this->SanitizeForSQL($formvars[$fieldname]);
        $qry = "select email from $this->tablename where $fieldname='".$field_val."'";
        $result = mysqli_query($this->connection, $qry);   
        if($result && mysqli_num_rows($result) > 0)
        {
            return false;
        }
        return true;
    }
    
    function DBLogin()
    {

        $this->connection = mysqli_connect($this->db_host,$this->username,$this->pwd);

        if(!$this->connection)
        {   
            $this->HandleDBError("Database Login failed! Please make sure that the DB login credentials provided are correct");
            return false;
        }
        if(!mysqli_select_db($this->connection, $this->database))
        {
            $this->HandleDBError('Failed to select database: '.$this->database.' Please make sure that the database name provided is correct');
            return false;
        }
        if(!mysqli_query($this->connection, "SET NAMES 'UTF8'"))
        {
            $this->HandleDBError('Error setting utf8 encoding');
            return false;
        }
        return true;
    }    
    
    function Ensuretable()
    {
        $result = mysqli_query($this->connection,"SHOW COLUMNS FROM $this->tablename");   
        if(!$result || mysqli_num_rows($result) <= 0)
        {
            return $this->CreateTable();
        }
        return true;
    }
   
   /*
    function CreateTable()
    {
       
    	$qry = "Create Table $this->tablename (".
                "patient_id INT NOT NULL AUTO_INCREMENT ,".
                "name VARCHAR( 128 ) NOT NULL ,".
                "lastname VARCHAR( 20 ) NOT NULL ,".
                "email VARCHAR( 64 ) NOT NULL ,".
                "password VARCHAR( 80 ) NOT NULL ,".
                "salt VARCHAR( 50 ) NOT NULL ,".
                "gender VARCHAR( 15 ) NOT NULL ,".
                "birthday DATE NOT NULL ,".
                "address VARCHAR( 30 ) NOT NULL ,".
                "confirmcode VARCHAR(32) ,".
                "joined_date DATETIME NULL ,".
                "imageUrl VARCHAR( 200 ) NULL ,".
                "PRIMARY KEY ( patient_id )".
                ")";
	
                
        if(!mysqli_query($this->connection, $qry))
        {
            $this->HandleDBError("Error creating the table \nquery was\n $qry");
            return false;
        }
        return true;
    }
*/
    
    function InsertIntoDB(&$formvars)
    {
    
        $confirmcode = $this->MakeConfirmationMd5($formvars['email']);

        $formvars['confirmcode'] = $confirmcode;

	   $hash = $this->hashSSHA($formvars['password']);

	   $encrypted_password = $hash["encrypted"];

	   $salt = $hash["salt"];

       $joined_date = date("Y-m-d H:i:s");



        
      

 
        $insert_query = 'insert into '.$this->tablename.'(
		name,
        lastname,
		email,
        password,
        salt,
        gender,
        birthday,	
		address,
		confirmcode,
        joined_date
		)
		values
		(
		"' . $this->SanitizeForSQL($formvars['name']) . '",
        "' . $this->SanitizeForSQL($formvars['lastname']) . '",
		"' . $this->SanitizeForSQL($formvars['email']) . '",
		"' . $encrypted_password . '",
        "' . $salt . '",
        "' . $this->SanitizeForSQL($formvars['gender']) . '",
        "' . $this->SanitizeForSQL($formvars['birthday']) . '",
        "' . $this->SanitizeForSQL($formvars['address']) . '",
		"' . $confirmcode . '",
        "' . $joined_date . '"
		)';  

 
        if(!mysqli_query($this->connection, $insert_query))
        {
            $this->HandleDBError("Error inserting data to the table\nquery:$insert_query");
            return false;
        }        
        return true;
    }
    function hashSSHA($password) {
 
        $salt = sha1(rand());
        $salt = substr($salt, 0, 10);
        $encrypted = base64_encode(sha1($password . $salt, true) . $salt);
        $hash = array("salt" => $salt, "encrypted" => $encrypted);
        return $hash;
    }
    function MakeConfirmationMd5($email)
    {
        $randno1 = rand();
        $randno2 = rand();
        return md5($email.$this->rand_key.$randno1.''.$randno2);
    }
    function SanitizeForSQL($str)
    {
        if( function_exists( "mysql_real_escape_string" ) )
        {
              $ret_str = mysqli_real_escape_string( $str );
        }
        else
        {
              $ret_str = addslashes( $str );
        }
        return $ret_str;
    }
    
 /*
    Sanitize() function removes any potential threat from the
    data submitted. Prevents email injections or any other hacker attempts.
    if $remove_nl is true, newline chracters are removed from the input.
    */
    function Sanitize($str,$remove_nl=true)
    {
        $str = $this->StripSlashes($str);

        if($remove_nl)
        {
            $injections = array('/(\n+)/i',
                '/(\r+)/i',
                '/(\t+)/i',
                '/(%0A+)/i',
                '/(%0D+)/i',
                '/(%08+)/i',
                '/(%09+)/i'
                );
            $str = preg_replace($injections,'',$str);
        }

        return $str;
    }    
    function StripSlashes($str)
    {
        if(get_magic_quotes_gpc())
        {
            $str = stripslashes($str);
        }
        return $str;
    } 


}
?>
