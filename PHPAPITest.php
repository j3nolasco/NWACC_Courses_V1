<?php
function getToken(){

  //URL to token endpoint needed to get our access token
  $tokenEnd = 'https://wd2-impl-services1.workday.com/ccx/oauth2/nwacc_preview/token';
  //a long string that includes all the keys we need for our access token
  $postdata = 'grant_type=refresh_token&refresh_token=d5yeiokbrphdwljorxq9ack2kqr6m8jimissk2gmin0a4h7bmoukk6so4m0exnv2txvr2f8s406ofvgt2wkrurlvpxol0g08dh7&client_id=OTM3ZDk3OGMtODdkZS00ZDYyLWJhODItNzcwNGI1MjZiMjE2&client_secret=duh3di0g9arsk0acg6nhxdfj4mhjuq3u8vwchqrv4koc0rqazgozm6421axl1wrbif20dfchgdhpvw0i5qtg71yz4xc8o9xnewj';

  $curl = curl_init(); // make a 'POST' to token endpoint 

      curl_setopt_array($curl, array(
          CURLOPT_RETURNTRANSFER => 1,
          CURLOPT_FAILONERROR => true,
          CURLOPT_URL => $tokenEnd,
          CURLOPT_USERAGENT => 'Codular Sample cURL Request',
          CURLOPT_POST => 1,
          CURLOPT_HTTPHEADER => array('content-type: application/x-www-form-urlencoded'),
          CURLOPT_POSTFIELDS => $postdata
          ));

          
          //...
         
        

  $response = curl_exec($curl);

  $data = json_decode($response,true); //parse the response from the 'POST'

  curl_close($curl); // close cURL to free up resources
  
  $token = $data['access_token']; //look for the 'access_token' key and store it

  return $token;
}

function getURL(){
  $url = 'https://wd2-impl-services1.workday.com/ccx/service/customreport2/nwacc_preview/j3nolasco/STU.INT113_NWACC.EDU_Class_Schedule_Outbound_CR_-__j3nolasco?format=json';
  return $url;
}
 
    $finduser = 'em26in31r8';
    
    $maps_url = getURL().$finduser.'&format=json';

    $auth = 'Authorization: Bearer '.getToken() ;
   // echo "<br>" ."<br>" . "Token: " .$auth;
    $curl = curl_init();

    curl_setopt_array($curl, array(
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
    curl_close($curl);
    
    $data = json_decode($response, true);
    $arrayCount = count($data);
 

    foreach($data as $key => $value){
      $arrayCount = count($value);
    } 
    $Rentry = $data['Report_Entry'];
    $AcademicPeriod = "";
    for ( $i = 0; $i < $arrayCount; $i++ ){
        $firstReport = $Rentry[$i];
        $AcademicPeriod = $firstReport['Academic_Period'];
       
    }
    $time = "";
    
    

?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
</head>
<body>

  <div class = "course-catalog"> 
  <div class = "print-screen">
        <button onclick="openPrint()">
            Print
        </button>
    </div>
    <section class = "left">
        <div class = "filter-container">
            <div class = "filter-content">
                <h1 class = "filter">Filter</h1>
                <input type="text" id="myInput" onkeyup="filterByName(this)" placeholder="Search for courses...">
                <h1>academic-period</h1>
                <select name="academic-periods" id="inputPeriod" >
                    <option value="<?=$AcademicPeriod ?>" disabled selected> Select Academic Period </option>
                <?php
                         $new_array = array();
                         for ( $i = 0; $i < $arrayCount; $i++ ){
                             $firstReport = $Rentry[$i];
                             $AcademicPeriod = $firstReport['Academic_Period'];
                             if(!in_array($AcademicPeriod, $new_array)){ 
                                  $new_array[$i] = $AcademicPeriod;
                                  $NewAcademicPeriod = $new_array[$i];
                                  ?>
                                  <option value="<?=$NewAcademicPeriod ?>"><?=$NewAcademicPeriod ?></option>"
                                  <?php
                                }
                             ?>                           
                         <?php
                         }?>
                </select>
                <h1>Delivery Mode</h1>
                <select name="delivery-mode" id="inputMode"  >
                    <option value="In-Person" disabled selected> Select Delivery Mode </option>
                <?php
                         $new_array = array();
                         for ( $i = 0; $i < $arrayCount; $i++ ){
                             $firstReport = $Rentry[$i];
                             $DeliveryMode = $firstReport['Delivery_Mode'];
                             if(!in_array($DeliveryMode, $new_array)){ 
                                  $new_array[$i] = $DeliveryMode;
                                  $DeliveryMode = $new_array[$i];
                                  ?>
                                  <option value="<?=$DeliveryMode ?>"><?=$DeliveryMode ?></option>"
                                  <?php
                                }
                             ?>                           
                         <?php
                         }?>
                </select>
                <h1>Academic Level</h1>
                <select name="delivery-mode" id="inputAcademic"  >
                    <option value="Undergraduate" disabled selected> Select Academic Level </option>
                <?php
                         $new_array = array();
                         for ( $i = 0; $i < $arrayCount; $i++ ){
                             $firstReport = $Rentry[$i];
                             $AcademicLevel = $firstReport['Academic_Level'];
                             if(!in_array($AcademicLevel, $new_array)){ 
                                  $new_array[$i] = $AcademicLevel;
                                  $AcademicLevel = $new_array[$i];
                                  ?>
                                  <option value="<?=$AcademicLevel ?>"><?=$AcademicLevel ?></option>"
                                  <?php
                                }
                             ?>                           
                         <?php
                         }?>
                </select>
                <h1>Meeting Pattern</h1>
                <select name ="meeting-pattern" id="inputMP">
                    <option value = "MTWR" disabled selected>Select Meeting Pattern </option>
                    <?php
                         $new_array = array();
                         for ( $i = 0; $i < $arrayCount; $i++ ){
                             $firstReport = $Rentry[$i];
                             $fullMP = $firstReport['$Meeting_Pattern'];
                             $MeetingPattern = substr($firstReport['Meeting_Pattern'],0,strpos($firstReport['Meeting_Pattern'], "|"));
                             if(!in_array($MeetingPattern, $new_array)){ 
                                  $new_array[$i] = $MeetingPattern;
                                  $MeetingPattern = $new_array[$i];
                                    if(strpos($MeetingPattern, 'M') !==false)
                                    {
                                        $MeetingPattern = str_replace('M',' Mon ', $MeetingPattern);
                                    }
                                    if (strpos($MeetingPattern, 'T') !==false)
                                    {
                                        $MeetingPattern = str_replace('T',' Tue ', $MeetingPattern);
                                    }
                                    if (strpos($MeetingPattern, 'W') !==false)
                                    {
                                        $MeetingPattern = str_replace('W',' Wed ', $MeetingPattern);
                                    }
                                    if (strpos($MeetingPattern, 'R') !==false)
                                    {
                                        $MeetingPattern = str_replace('R',' Thu ', $MeetingPattern);
                                    }
                                    if (strpos($MeetingPattern, 'F') !==false)
                                    {
                                        $MeetingPattern = str_replace('F',' Fri ', $MeetingPattern);
                                    }
                                    
                                  ?>
                                  <option value="<?=$MeetingPattern ?>"><?=$MeetingPattern ?></option>"
                                  <?php
                                }
                             ?>                           
                         <?php
                         }?>
                </select>
                <h3>Meeting Time</h3>
                <select name="hours" id="inputHr">
                    <?php  
                            for ( $i = 1; $i < 13; $i++ ){
                                
                                $time = $i; 
                                    ?>
                                    <option value="<?=$time ?>"><?=$time ?></option>"
                                    <?php
                                    
                                ?>                           
                            <?php
                            }?>
                </select>
                <select name="minutes" id="inputMin">
                        <option>00</option>
                        <option>10</option>
                        <option>20</option>
                        <option>30</option>
                        <option>40</option>
                        <option>50</option>
                        
                </select>
                <select name="am-pm" id="inputAMPM">
                        <option>AM</option>
                        <option>PM</option>
                </select>
                <p style="display:inline">to</p>
                <select name="hours" id="inputHr">
                    <?php  
                            for ( $i = 1; $i < 13; $i++ ){
                                
                                $time = $i; 
                                    ?>
                                    <option value="<?=$time ?>"><?=$time ?></option>"
                                    <?php
                                    
                                ?>                           
                            <?php
                            }?>
                </select>
                <select style="display:inline" name="minutes" id="inputMin">
                        <option>00</option>
                        <option>10</option>
                        <option>20</option>
                        <option>30</option>
                        <option>40</option>
                        <option>50</option>
                        
                </select>
                <select style="display:inline" name="am-pm" id="inputAMPM">
                        <option>AM</option>
                        <option>PM</option>
                </select>
                

                <br></br>
                <button onclick="resetValues()">reset</button>
            </div>
        </div>
    </section>
    <section class ="right">
        <div class = "courses-container">
                <div class = "course-content">
                    <h1 class = "title">Courses</h1>
                    <div class = "courses-grid" id = "list">
                        <?php
                         
                            for ( $i = 0; $i < $arrayCount; $i++ ){
                                $firstReport = $Rentry[$i];
                                $CourseListing = $firstReport['Course_Listing'] ;
                                $CourseSubjects = $firstReport['Course_Subjects'];
                                $SectionCapacity = $firstReport['Section_Capacity'];
                                $AcademicLevel = $firstReport['Academic_Level'];
                                $DeliveryMode = $firstReport['Delivery_Mode'];
                                $StartDate = $firstReport['Start_Date'];
                                $EndDate = $firstReport['End_Date'];
                                $AcademicPeriod = $firstReport['Academic_Period'];
                                $count = $i;
                                if(array_key_exists('Meeting_Pattern', $firstReport))
                                        {
                                            $MeetingPattern = $firstReport['Meeting_Pattern'];
                                          
                                        }else{
                                            $MeetingPattern = 'NA';
                                        }
                                if(array_key_exists('Instructors', $firstReport))
                                        {
                                            $Instructor = $firstReport['Instructors'];
                                        
                                        }else{
                                            $Instructor = 'NA';
                                        }
                            
                                ?>
                                
                                <div class = "course-card" id="<?=$i ?>" onclick ="drillBox(this)" >
                                    <div class = "course-card_L">
                                        <h3 class = "course-listing"><?=$CourseListing ?></h3>
                                        <p class = "course-subjects"><?=$CourseSubjects ?><p>
                                        <p class = "Academic-Period"><?=$AcademicPeriod ?><p>
                                        <p class = "delivery-mode"><?=$DeliveryMode ?><p>
                                        
                                            <br></br>
                                        <h3>Instructor</h3>
                                        <p class = "instructor"><?=$Instructor ?><p>
                                    </div>
                                    <div class = "course-card_R">
                                        <h3 class = "meeting-header">Metting Pattern</h3>
                                        <p class = "meeting-pattern"><?=$MeetingPattern ?><p> 
                                        <br></br><br></br><br></br><br></br>
                                        
                                        <p class = "academic_level"><?=$AcademicLevel ?><p>   
                                    </div>
                                </div>
                               
                            <?php
                            }?>

                                <button type = "button" class="leave" id="leave" style="display:none" onclick="closeDrill()">X</button>
                                <div class = "drill-down" id = "drill" style="display:none">    
                                    <?php
                                    for ( $j = 0; $j < $arrayCount; $j++ ){
                                        $firstReport = $Rentry[$j];
                                        $CourseListing = $firstReport['Course_Listing'] ;
                                        $CourseSubjects = $firstReport['Course_Subjects'];
                                        $SectionCapacity = $firstReport['Section_Capacity'];
                                        $AcademicLevel = $firstReport['Academic_Level'];
                                        $DeliveryMode = $firstReport['Delivery_Mode'];
                                        $AcademicPeriod = $firstReport['Academic_Period'];
                                        $StartDate = $firstReport['Start_Date'];
                                        $EndDate = $firstReport['End_Date'];
                                        
                                        $Units = $firstReport['Units_and_Unit_Type'];
                                        $count = $j;
                                        if(array_key_exists('Requirements', $firstReport))
                                        {
                                            $Requirements = $firstReport['Requirements'];
                                          
                                        }else{
                                            $Requirements = 'No Requirements';
                                        }
                                        if(array_key_exists('Course_Description', $firstReport))
                                        {
                                            $CourseDescription = $firstReport['Course_Description'];
                                          
                                        }else{
                                            $CourseDescription = 'No Description Provided';
                                        }
                                        
                                       
                                ?>
                                    <div class ="drill-info" id = "drill-info" style="display:none">
                                        <a style = "display:none"><?=$j ?></a>
                                        <h3 class = "course-listing"><?=$CourseListing ?></h3>
                                        <p class = "course-subjects"><?=$CourseSubjects ?><p>  
                                        <p class = "academic-period"> <?=$AcademicPeriod ?><p> 
                                        <p class = "delivery-mode"><?=$DeliveryMode ?><p>
                                        <h4 style = "display: inline">Start Date: </h4> <p style = "display: inline"class = "start-date"><?=$StartDate ?><p>
                                        <h4 style = "display: inline">End Date: </h4><p style = "display: inline" class = "end-date"><?=$EndDate ?><p>
                                        <h4 style="display: inline"> Units: </h4><p style="display: inline"><?=$Units ?></p>    
                                        <br></br>
                                        <h4>Course Summary</h4>
                                        <p class = "course-description"><?=$CourseDescription ?><p>
                                        <h4>Course Requirements</h4>
                                        <p class = "requirements"><?=$Requirements ?><p>     
                                        
                                    </div>  
                                    <?php
                                }?> 
                                </div>
                                <div class = "shadow" id = "shadow" style="display:none"></div>
                    </div>
                </div>
            </div>
            
        </section>
    </div>

<script>
    const Textinput =document.getElementById("myInput");
    Textinput.addEventListener("input",filterByName());

    const input = document.getElementById("inputPeriod");
    var filter = input.value.toUpperCase();
    const list =document.getElementById("list");
    var div = list.getElementsByClassName("course-card");
    var sels =document.getElementsByTagName('select');

    var filterArray = [];
    var serachValue = "";

    var courseSerach= false;
    var ap = false;
    var dm = false;
    var al = false;
    var mp = false;
    var selected_Period = document.getElementById("inputPeriod").value.toUpperCase();
    var selected_Mode = document.getElementById("inputMode").value.toUpperCase();
    var selected_Academic = document.getElementById("inputAcademic").value.toUpperCase();

    var apSelection = "";
    var dmSelection = "";
    var alSelection = "";

    function resetValues(){

        Textinput.value = "";

      for(i=0; i<sels.length; i++){
        sels[i].selectedIndex=0;
      }
      
      
        for(i=0; i < div.length; i++){
                
                    div[i].style.display = "";
                   
                
            }
        }
    
        
        //filter dropdowns -----------------------------------------------------------------------------------------------------------------

        for(j=0; j<sels.length; j++){
          sels[j].addEventListener('change', function(){
            selectionId = this.id;

            var selectedPeriod =document.getElementById("inputPeriod").value.toUpperCase();
            var selectedMode = document.getElementById("inputMode").value.toUpperCase();
            var selectedAcademic = document.getElementById("inputAcademic").value.toUpperCase();

            

            // if(courseSerach)
            // {
            //     console.log("array length: " + filterArray.length);
            //     for(i = 0; i < filterArray.length; i++)
            //     {
            //         div = [];
            //         div.push(filterArray[i]);
                     
            //     }
            // }

                        
            if(selectionId === "inputPeriod")
            {
                ap = true;
                if (dm || al ){
                    
                    for(i = 0; i<div.length; i++)
                  {
                      const h3 = div[i].getElementsByTagName("h3")[0];
                      const p = div[i].getElementsByTagName("p")[2];
                      const dms = div[i].getElementsByTagName("p")[4];
                      const als = div[i].getElementsByTagName("p")[10];
                      
                   
                      if((p.innerHTML.toUpperCase() == selectedPeriod) && (dms.innerHTML.toUpperCase() == selectedMode) && (als.innerHTML.toUpperCase() == selectedAcademic))
                      {
                        div[i].style.display = "";
                        apSelection = p.innerHTML;
                        selected_Period =selectedPeriod;

                      }else{
                        div[i].style.display = "none";
                      }
                  } 
                }else{
                
                  for(i = 0; i<div.length; i++)
                  {
                      const p = div[i].getElementsByTagName("p")[2];
                      
                       
                      if(p.innerHTML.toUpperCase() == selectedPeriod)
                      {
                        div[i].style.display = "";
                        apSelection = p.innerHTML;
                        selected_Period =selectedPeriod;
                        //console.log("P: "+p.innerHTML);

                      }else{
                        div[i].style.display = "none";
                      }
                  }    
                }   
            }
            if(selectionId === "inputMode")
            {
              dm = true;
              if(ap || al)
              {
                for(var i =0; i<div.length; i++)
                {
                    const h3 = div[i].getElementsByTagName("h3")[0];
                    const ap = div[i].getElementsByTagName("p")[2];
                    const p = div[i].getElementsByTagName("p")[4];
                    const als = div[i].getElementsByTagName("p")[10];

                        
                      if((p.innerHTML.toUpperCase() == selectedMode) && (ap.innerHTML.toUpperCase() == selectedPeriod) && (als.innerHTML.toUpperCase() == selectedAcademic))
                      {
                       div[i].style.display = "";
                       dmSelection = p.innerHTML;
                       selected_Mode =selectedMode;

                      }else{
                        div[i].style.display = "none";
                      }
                }
              }else{ 
                for(var i =0; i<div.length; i++)
                {
                        const p = div[i].getElementsByTagName("p")[4];

                        if(p.innerHTML.toUpperCase() == selectedMode)
                        {
                        div[i].style.display = "";
                        dmSelection = p.innerHTML;
                        selected_Mode =selectedMode;

                        }else{
                            div[i].style.display = "none";
                        }
                }
             }
            }
            if(selectionId === "inputAcademic")
            {
              al = true;
              if(dm||ap)
              {

                for(var i =0; i<div.length; i++)
                {
                    const h3 = div[i].getElementsByTagName("h3")[0];
                    const ap = div[i].getElementsByTagName("p")[2];
                    const dm = div[i].getElementsByTagName("p")[4];
                    const p = div[i].getElementsByTagName("p")[10];

                        
                      if((dm.innerHTML.toUpperCase() == selectedMode) && (ap.innerHTML.toUpperCase() == selectedPeriod) && (p.innerHTML.toUpperCase() == selectedAcademic))
                      {
                       div[i].style.display = "";
                       alSelection = p.innerHTML;
                       selected_Academic = selectedAcademic;

                      }else{
                        div[i].style.display = "none";
                      }
                }

              }else{
                for(var i =0; i<div.length; i++)
                {
               
                    const p = div[i].getElementsByTagName("p")[10];
                    
                      if(p.innerHTML.toUpperCase() == selectedAcademic)
                      {
                        div[i].style.display = "";
                        alSelection = p.innerHTML;
                        selected_Academic = selectedAcademic;

                      }else{
                        div[i].style.display = "none";
                      }
                }
              }
              
            }
            if(selectionId === "inputMP")
            {
                mp = true;
                if (dm || al ||ap){
                    
                    for(i = 0; i<div.length; i++)
                  {
                      const h3 = div[i].getElementsByTagName("h3")[0];
                      const p = div[i].getElementsByTagName("p")[2];
                      const dms = div[i].getElementsByTagName("p")[4];
                      const als = div[i].getElementsByTagName("p")[10];
                      
                   
                      if((p.innerHTML.toUpperCase() == selectedPeriod) && (dms.innerHTML.toUpperCase() == selectedMode) && (als.innerHTML.toUpperCase() == selectedAcademic))
                      {
                        div[i].style.display = "";
                        apSelection = p.innerHTML;
                        selected_Period =selectedPeriod;

                      }else{
                        div[i].style.display = "none";
                      }
                  } 
                }else{
                
                  for(i = 0; i<div.length; i++)
                  {
                      const p = div[i].getElementsByTagName("p")[8];
                      
                       
                      if(p.innerHTML.toUpperCase().indexOf(selectedPeriod) > -1)
                      {
                        div[i].style.display = "";
                        apSelection = p.innerHTML;
                        selected_Period =selectedPeriod;
                        //console.log("P: "+p.innerHTML);

                      }else{
                        div[i].style.display = "none";
                      }
                  }    
                }   
            }
          }, false);}

          // end of drop downs----------------------------------------------------------------------------------------------------------
   
function filterByName(){
    
    const filter = Textinput.value.toUpperCase();
    const list =document.getElementById("list");
    div = list.getElementsByClassName("course-card");
    filterArray = [];

    serachValue =filter;

      if(al || dm || ap)
      {
        for(i=0; i < div.length; i++){
            const h3 = div[i].getElementsByTagName("h3")[0];
            const ins = div[i].getElementsByTagName("p")[6];
            const ap = div[i].getElementsByTagName("p")[2];
            const dms = div[i].getElementsByTagName("p")[4];
            const als = div[i].getElementsByTagName("p")[10];   

            if((h3)){
                if(((h3.innerHTML.toUpperCase().indexOf(filter) > -1) ||  (ins.innerHTML.toUpperCase().indexOf(filter) > -1)) && (ap.innerHTML.toUpperCase() == selected_Period) && (dms.innerHTML.toUpperCase() == selected_Mode) && (als.innerHTML.toUpperCase() == selected_Academic)){
                    div[i].style.display = "";
                   
                } else {
                    div[i].style.display = "none";
                    
                }
            }
        }
    } else{
        
        for(i=0; i < div.length; i++){
            const h3 = div[i].getElementsByTagName("h3")[0];
            const ins = div[i].getElementsByTagName("p")[6];
            
            if(h3 || ins){
                if(h3.innerHTML.toUpperCase().indexOf(filter) > -1 || ins.innerHTML.toUpperCase().indexOf(filter) > -1){
                    div[i].style.display = "";
                    filterArray.push(div[i]);
                    //console.log("p: "+ ins.innerHTML);
                 
                } else {
                    div[i].style.display = "none";
                    courseSerach = true;
                }
            }
        }
    } 
        
}

    function drillBox(element){
        const shadow =document.getElementById("shadow");
        const exit =document.getElementById("leave");
        const drillDown =document.getElementById("drill");
        const drillInfo =drillDown.getElementsByClassName("drill-info");
        const list =document.getElementById("list");
        const div = list.getElementsByClassName("course-card");
        drillDown.style.display = "block";
        shadow.style.display="block";
        exit.style.display="block";   

        const parent  = element.id;
      

        for(i=0; i < drillInfo.length; i++){
          const h3c = div[i].getElementsByTagName("h3")[0];
          const h3d = drillInfo[i].getElementsByTagName("h3")[0]; 
       
            if(i == parent)
            {
                drillInfo[i].style.display = "block";             
            }else {
                drillInfo[i].style.display = "none";
            }
        }
    }
   
    function closeDrill(){
        const box =document.getElementById("drill");
        const shadow =document.getElementById("shadow");
        const exit =document.getElementById("leave");
        box.style.display = "none";
        shadow.style.display="none";
        exit.style.display="none";
    }

    function openPrint(){
        window.print("iframe.html","Print","rel=noopener");
    }

  </script>
<style>
.print-screen{
    position: fixed;
    left: 20;
    top: 20;
    z-index: 5;

    
    background-color: lightgray;
    border-radius: 50%;
}
.course-catalog{
    display: grid;
    grid-template-columns: (2 ,1fr);
    gap: 5rem;
    justify-content: center;
    padding: 2rem;
    }
.left{
    position: fixed;
    grid-column: 1;
}
.right{
    
    grid-column: 2;
}

.filter-container{
   
    display: flex;
    background-color: lightgray;
    box-shadow: 0px 0px 10px rgb(0 0 0 / 10%);
    border-radius: 1.7rem;
    height: 40rem;
    width: auto;
    flex-direction: row;
    padding: 2rem;
    gap: 2rem;

}
/* .inputPeriod{
    width: 10rem;
} */
.courses-grid{
    display: grid;
    grid-template-columns: 1fr ;
    grid-template-rows: auto auto;
    width: 30rem;
    height: 10rem;
    gap: 2.5rem;
   
}
.course-card{
    
    display: grid;
    grid-template-columns: (2 ,1fr);
    background-color: lightgray;
    box-shadow: 0px 0px 10px rgb(0 0 0 / 10%);
    border-radius: 1.7rem;
    height: auto;
    width: 150%;
    padding: 1rem; 
    padding-top: .2rem !important;
    gap: 2rem;
    &:hover{
        transform: scale(1.05);
    transition: all .4s ease-in-out;
  
   
  }  
}
.course-card_L{
    grid-column: 1;
}
.course-card_R{
    grid-column: 2;
}

.drill-down{
    position: fixed;
    z-index: 2;
    background-color: lightgray;
    box-shadow: 0px 0px 10px rgb(0 0 0 / 10%);
    border-radius: 1.7rem;
    
    inset: 0px;
    width: 700px;
    height: 700px;
    margin: auto;
    
}
.drill-info{
    padding: 1rem;
}
.leave{
    position: fixed;
    z-index: 5;
    background-color: lightgray;
    border-radius: 1.7rem;
    height: 50px;
    width: 50px;
    left: 900px
  
}
.shadow{
    position: absolute;
    z-index: 1;
    box-shadow: 0 0 0 99999px rgba(0, 0, 0, .5);
}



/* .course-card:hover  {
   
    transform: scale(1.1);
    transition: all .4s ease-in-out;
} */
</style>
    
</body>
</html>