<?php
require("ReqToken.php"); //file that fetches the access token and url
require_once 'W:/CAS/config.php';
require_once 'W:/CAS/CAS.php';

if( $_SERVER['HTTPS'] == "off")
{
    $redirect= 'https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    header("Location:$redirect");
}

///CAS Logon        
//begin edit
phpCAS::setDebug(false);
phpCAS::setVerbose(false);
phpCAS::client(CAS_VERSION_2_0, $cas_host, $cas_port, $cas_context, false);
phpCAS::setCasServerCACert($cas_server_ca_cert_path);

if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();  
}

phpCAS::forceAuthentication();
$e_user=phpCAS::getUser();
                             
// END CAS
//Begin Workday Information Gathering
// Now get user info from Workday
$authUsr = strtolower($e_user);
$nwacc_email = trim($authUsr . "@nwacc.edu") ;

$maps_url = getURL().$authUsr.'&format=json'; //build API URL
$auth = 'Authorization: Bearer '.getToken(); //grab access token 
$curl = curl_init(); // 'GET' request to the Workday API using cURL
            
 curl_setopt_array($curl, array(
    CURLOPT_SSL_VERIFYHOST => false,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_URL => $maps_url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_HTTPHEADER => array($auth),
    ));
                
$response = curl_exec($curl);
curl_close($curl); //close curl to free up resources
                
$data = json_decode($response,true); // pass the information from the json file into a variable

//check to see if $data is populated with the json file.
if(!empty($data))  
{
    $Rentry = $data['Report_Entry']; //if the variable contains data then we parse the file information
}
else{
    echo  "No Report Found"; //if there is no data exit 
    exit;
}

// check to see if the json file contains the key 'Report_Entry'
if(empty($Rentry)) {
    echo  "No Report Found"; //if we dont find the key that means we didnt hit the report
    exit;
}
else {
    $firstReport = $Rentry[0]; //if the key is found, enter the first instance of it

    //check to see if the key 'Universal_Id' exists
    if(array_key_exists('Universal_Id', $firstReport))
    {
        $UID = $firstReport['Universal_Id'];
        $Username = $firstReport['Username'];
        $Fullname = $firstReport['fullName'];
    }
    else{
        echo "Access Available for Students Only";
        exit;
    }
}

?>

<?php
/**
* uploadFile()
* 
 * @param string $file_field name of file upload field in html form
* @param bool $check_image check if uploaded file is a valid image
* @param bool $random_name generate random filename for uploaded file
* @return array
*/
function uploadFile ($file_field = null, $check_image = false, $random_name = false, $current_user = null, $current_userID = null, $FileType = null) {
       
  //Config Section    
  //Set file upload path
  $path = 'W:/FileUpload/'; //with trailing slash
  //Set max file size in bytes
  $max_size = 5000000;
  //Set default file extension whitelist
  $whitelist_ext = array('pdf');
  //Set default file type whitelist
  $whitelist_type = array('application/pdf');

  //The Validation
  // Create an array to hold any output
  $out = array('error'=>null);
               
  if (!$file_field) {
    $out['error'][] = "Please specify a valid form field name";           
  }

  if (!$path) {
    $out['error'][] = "Please specify a valid upload path";               
  }
       
  if (count($out['error'])>0) {
    return $out;
  }

  //Make sure that there is a file
  if((!empty($_FILES[$file_field])) && ($_FILES[$file_field]['error'] == 0)) {
         
    // Get filename
    $file_info = pathinfo($_FILES[$file_field]['name']);
    $name = $file_info['filename'];
    $ext = $file_info['extension'];
               
    //Check file has the right extension           
    if (!in_array($ext, $whitelist_ext)) {
      $out['error'][] = "Invalid file Extension-Must be a PDF";
    }
               
    //Check that the file is of the right type
    if (!in_array($_FILES[$file_field]["type"], $whitelist_type)) {
      $out['error'][] = "Invalid file Type-Documents must be in PDF format";
    }
               
    //Check that the file is not too big
    if ($_FILES[$file_field]["size"] > $max_size) {
      $out['error'][] = "File is too big! Max file size is 5MB.";
    }
               


    //Create full filename including path
    if ($random_name) {
      // Generate random filename
      $tmp = str_replace(array('.',' '), array('',''), microtime());
      $tmp = substr($tmp,0,9);                
      if (!$tmp || $tmp == '') {
        $out['error'][] = "File must have a name";
      }     
      $newname = $current_userID. '_'.$current_user.'_'.$FileType.'_'.$tmp.'.'.$ext;                                
    } else {
        $newname = $current_userID. '_'.$current_user.'_'.$FileType.'_'.$name.'.'.$ext;
    }
               
    //Check if file already exists on server
    if (file_exists($path.$newname)) {
      $out['error'][] = "A file with this name already exists";
    }

    if (count($out['error'])>0) {
      //The file has not correctly validated
      return $out;
    } 

    if (move_uploaded_file($_FILES[$file_field]['tmp_name'], $path.$newname)) {
      //Success
      $out['filepath'] = $path;
      $out['filename'] = $newname;
      return $out;
    } else {
      $out['error'][] = "Server Error!";
    }
         
  } else {
    $out['error'][] = "No file uploaded";
    return $out;
  }      
}
?>


<!DOCTYPE html>
<html>  
                <?php
        $title = "Student Document Secure File Dropbox";
                                include($_SERVER['DOCUMENT_ROOT']."/Templates/nwacc_template_head.php");
                ?>
                </script>
                <?php

                                $page_heading = "Student Document Secure File Dropbox";
                                
                                // no_nav=1 tells the nwacc_template_body not to display the left nav column
                                $no_nav = 1;
                                
                                include($_SERVER['DOCUMENT_ROOT']."/Templates/nwacc_template_body.php");
                                if (isset($_POST['submit'])) {
                                                $message = '';
                                                $file = uploadFile('file', true, true,$e_user,$UID,$_POST['FileType']);
                                                if (is_array($file['error'])) {
                                                                foreach ($file['error'] as $msg) {
                                                                                $message .= ' <font style="color:red;"><strong><i>** '.$msg.' **</i></strong></font> ';    
                                                                }
                                                                echo "Hello " . "<strong>" . $Fullname . ",</strong></br></br>";
                                                                echo "Please upload your PDF document. " . "<strong>" . "Documents must be in PDF format." . "</strong></br>";
                                                                echo "Contact Enrollment Support at ";
                                                                echo "<a href=\mailto:enrollmentsupport@nwacc.edu\ alt=\enrollmentsupport@nwacc.edu\ title=\enrollmentsupport@nwacc.edu\>enrollmentsupport@nwacc.edu</a>";
                                                                echo " or call/text 479-309-5532 for assistance with this Dropbox and your files." . "</br></br>";
                                                                echo "<strong>" . "Admissions Documents" . "</strong>" . " - High School Transcripts, Test Scores, Immunization Records, TB screening, Permanent Resident Card, etc" . "</br>";
                                                                echo "<strong>" . "Records Documents" . "</strong>" . " - Unofficial College Transcripts, AP and CLEP scores, Change of Degree, Change of Information, Tuition Change Request, etc. If you need a prerequisite or test score override, please complete " . "<br>&emsp;" .  "<a href=\https://www.nwacc.edu/enrollment/records/prerequisite.aspx\>https://www.nwacc.edu/enrollment/records/prerequisite.aspx</a>" . "</br>";
                                                                echo "<strong>" . "Financial Aid Documents" . "</strong>" . " - Verification forms, Tax Transcripts, Income Verification Forms and other forms needed to complete your requirements" . "</br></br>";
                                                } else {
                                                                $message = "<div  class=\"alert-success\"><strong>Success!</strong> File uploaded successfully! Please allow 3-5 business days for processing.</div>";
                                                
                                                echo "Hello " . "<strong>" . $Fullname . ",</strong></br></br>";
                                                echo "Please upload your PDF document. " . "<strong>" . "Documents must be in PDF format." . "</strong></br>";
                                                echo "Contact Enrollment Support at ";
                                                echo "<a href=\mailto:enrollmentsupport@nwacc.edu\ alt=\enrollmentsupport@nwacc.edu\ title=\enrollmentsupport@nwacc.edu\>enrollmentsupport@nwacc.edu</a>";
                                                echo " or call/text 479-309-5532 for assistance with this Dropbox and your files." . "</br></br>";
                                                echo "<strong>" . "Admissions Documents" . "</strong>" . " - High School Transcripts, Test Scores, Immunization Records, TB screening, Permanent Resident Card, etc" . "</br>";
                                                echo "<strong>" . "Records Documents" . "</strong>" . " - Unofficial College Transcripts, AP and CLEP scores, Change of Degree, Change of Information, Tuition Change Request, etc. If you need a prerequisite or test score override, please complete " . "<br>&emsp;" .  "<a href=\https://www.nwacc.edu/enrollment/records/prerequisite.aspx\>https://www.nwacc.edu/enrollment/records/prerequisite.aspx</a>" . "</br>";
                                                echo "<strong>" . "Financial Aid Documents" . "</strong>" . " - Verification forms, Tax Transcripts, Income Verification Forms and other forms needed to complete your requirements" . "</br></br>";
                                                // EMAIL
                                                if ($_POST['FileType'] == 'OtherAdvising'){
                                                                require_once $_SERVER['DOCUMENT_ROOT'].'/PHPMailer/PHPMailerAutoload.php';
                                                                $mail = new PHPMailer();
                                                                $mail->addAddress('AskYourAdvisor@nwacc.edu');
                                                                $mail->Subject = 'Drop Box File Received';
                                                                $mail->setFrom('donotreply@nwacc.edu', 'SecureDoc');

                                                                $head = '<html><head>';
                                                                $style = '<style type="text/css">
                                                                                                                body { font: 12px Arial,Helvetica,Verdana,sans-serif; }
                                                                                                                h2 { color:#54815d; font-size: 16px; }
                                                                                                                </style>';
                                                                $heading = '</head><body><h2>Boom.</h2>';


                                                                $Mailmessage = "<p>Psst. Hey, you. Guess what? ".$Fullname. "(".$UID.") was kind enough to upload a document. Why don't you go check it out? </p><p>Sincerely,</br></br>The Computer.</p>";
                                                                $mail->isSMTP();
                                                                
                                                                //Load smtp host name and smtp port from global variables
                                                                require_once $_SERVER['DOCUMENT_ROOT'].'/globals/smtp_variables.php';
                                                                
                                                                $mail->msgHTML($head . $style . $heading . $Mailmessage);
                                                                $mail->send();}
                                                }
                                                echo $message;
                                } else {
                                                echo "Hello " . "<strong>" . $Fullname . ",</strong></br></br>";
                                                echo "Please upload your PDF document. " . "<strong>" . "Documents must be in PDF format." . "</strong></br>";
                                                echo "Contact Enrollment Support at ";
                                                echo "<a href=\mailto:enrollmentsupport@nwacc.edu\ alt=\enrollmentsupport@nwacc.edu\ title=\enrollmentsupport@nwacc.edu\>enrollmentsupport@nwacc.edu</a>";
                                                echo " or call/text 479-309-5532 for assistance with this Dropbox and your files." . "</br></br>";
                                                echo "<strong>" . "Admissions Documents" . "</strong>" . " - High School Transcripts, Test Scores, Immunization Records, TB screening, Permanent Resident Card, etc" . "</br>";
                                                echo "<strong>" . "Records Documents" . "</strong>" . " - Unofficial College Transcripts, AP and CLEP scores, Change of Degree, Change of Information, Tuition Change Request, etc. If you need a prerequisite or test score override, please complete " . "<br>&emsp;" .  "<a href=\https://www.nwacc.edu/enrollment/records/prerequisite.aspx\>https://www.nwacc.edu/enrollment/records/prerequisite.aspx</a>" . "</br>";
                                                echo "<strong>" . "Financial Aid Documents" . "</strong>" . " - Verification forms, Tax Transcripts, Income Verification Forms and other forms needed to complete your requirements" . "</br></br>";
                                                
                                }

?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data" name="form1" id="form1">
<input name="file" type="file" size="20" />
<select name="FileType">
<option value="AdmissionsDoc">Admissions Documents</option>
<option value="ChangeOfRecords">Change of Records</option>
<option value="DocumentRequest">Document Request</option>
<option value="FinAidVerification">Financial Aid Verification Worksheet</option>
<option value="TaxDocuments">Tax Documents</option>
<option value="OtherAdvising">Other (Academic Advising)</option>
<option value="OtherFinAid">Other (Financial Aid)</option>
<option value="OtherRecords">Other (Student Records)</option>
</select> 
<input name="submit" type="submit" value="Upload" onclick="showLoading()" />
</form>

<script>
function showLoading() {
    if (document.getElementById("divLoadingFrame") != null) {
        return;
    }
    var style = document.createElement("style");
    style.id = "styleLoadingWindow";
    style.innerHTML = `
        .loading-frame {
            position: fixed;
            background-color: rgba(0, 0, 0, 0.8);
            left: 0;
            top: 0;
           right: 0;
            bottom: 0;
            z-index: 4;
        }

        .loading-track {
            height: 50px;
            display: inline-block;
            position: absolute;
            top: calc(50% - 50px);
            left: 50%;
       }

        .loading-dot {
            height: 5px;
            width: 5px;
            background-color: white;
            border-radius: 100%;
            opacity: 0;
        }

        .loading-dot-animated {
            animation-name: loading-dot-animated;
            animation-direction: alternate;
            animation-duration: .75s;
            animation-iteration-count: infinite;
            animation-timing-function: ease-in-out;
        }

        @keyframes loading-dot-animated {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }
    `
    document.body.appendChild(style);
    var frame = document.createElement("div");
    frame.id = "divLoadingFrame";
    frame.classList.add("loading-frame");
    for (var i = 0; i < 10; i++) {
        var track = document.createElement("div");
        track.classList.add("loading-track");
        var dot = document.createElement("div");
        dot.classList.add("loading-dot");
        track.style.transform = "rotate(" + String(i * 36) + "deg)";
        track.appendChild(dot);
        frame.appendChild(track);
    }
    document.body.appendChild(frame);
    var wait = 0;
    var dots = document.getElementsByClassName("loading-dot");
    for (var i = 0; i < dots.length; i++){
        window.setTimeout(function (dot) {
            dot.classList.add("loading-dot-animated");
        }, wait, dots[i]);
        wait += 150;
    }
};
function removeLoading() {
    document.body.removeChild(document.getElementById("divLoadingFrame"));
    document.body.removeChild(document.getElementById("styleLoadingWindow"));
};
</script>
<br><hr><br>
Have a question or need help? Contact the Enrollment Support Center at 479-986-4000.
<?php include($_SERVER['DOCUMENT_ROOT']."/Templates/nwacc_template_foot.php"); ?>
