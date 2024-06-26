<style>

    .artists-hover{
        background-color: #101E14;
        border:1px solid #269524;
        color: #FFFFFF;
    }
</style>

<?php 

include 'connection.php';
$course_name = "none";
$user_on_page = "unknown";
$user_name ="unknown";
$admin_Id = "unknown";
$is_page_login= "unknown";
$user_on_page=  "unknown";

ob_start();
 
if (isset($_SESSION["user_on_page"])) {
$user_name =  $_SESSION["username"] ;
$admin_Id = $_SESSION["admin_Id"];
$is_page_login = $_SESSION["is_page_login"];
$user_on_page =  $_SESSION["user_on_page"];

 

}

if(!isset($_SESSION["user_on_page"])){

}
ob_end_flush();

function fetch_courses($connection, $guide_name, $guide_id) {
   $sql = "SELECT C_Name FROM parent_courses";
   $result = $connection->query($sql);

   if ($result->num_rows > 0) {
       while ($row = $result->fetch_assoc()) {
           $course_name = $row["C_Name"];
           check_if_registered_already($connection, $course_name, $guide_name, $guide_id);
       }
   } else {
       echo "No results found.";
   }
}

function check_if_registered_already($connection, $course_name, $guide_name, $guide_id) {
   // Sanitize inputs to prevent SQL injection
   $guide_name = $connection->real_escape_string($guide_name);
   $guide_id = $connection->real_escape_string($guide_id);

   // Use single quotes around string values in the SQL query
   $sql = "SELECT C_Name, Course_Id, Guide_name, Guide_Key FROM parent_courses WHERE  Guide_name = '$guide_name' AND Guide_Key = '$guide_id' AND C_Name = '$course_name'";
   $result = $connection->query($sql);

   if ($result->num_rows > 0) {
       while ($row = $result->fetch_assoc()) {
           
           show_admin_courses($connection, $course_name);
       }
   } else {
       // Not registered in this course
   }
}

function show_admin_courses($connection, $cname) {
   $cname = $connection->real_escape_string($cname); // Sanitize input to prevent SQL injection

   $sql = "SELECT C_Name, Description, Guide_name, Guide_Email, ImgName, course_duration FROM parent_courses WHERE C_Name = '$cname'";
   $result = $connection->query($sql);

   if ($result) {
       if ($result->num_rows > 0) {
           while ($row = $result->fetch_assoc()) {
               $course_name = htmlspecialchars($row["C_Name"]);
               $Description = htmlspecialchars($row["Description"]);
               $Guide_name = htmlspecialchars($row["Guide_name"]);
               $Guide_Email = htmlspecialchars($row["Guide_Email"]);
               $Img_name = htmlspecialchars($row["ImgName"]);
               $course_duration = htmlspecialchars($row["course_duration"]);

               echo '
                   <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                       <div class="artists-thumb" style="border: 0px solid #150E19; border-radius: 20px; filter: drop-shadow(2px 2px 4px #219c02);">
                           <div class="artists-image-wrap">
                               <img src="' . $Img_name . '" class="artists-image img-fluid">
                           </div>
                           <div class="artists-hover">
                               <p>
                                   <strong>Course Name:</strong>
                                   ' .  $course_name . '
                               </p>
                               <p>
                                   <strong>Description:</strong>
                                   ' . $Description . '
                               </p>
                               <p>
                                   <strong>Course Duration:</strong>
                                   ' . $course_duration . '
                               </p>
                               <p>
                                   <strong>Guide Name:</strong>
                                   ' . $Guide_name . '
                               </p>
                               <p>
                                   <strong>Guide Email:</strong>
                                   ' . $Guide_Email . '
                               </p>
                               <hr>
                               <p class="mb-0">
                                   <strong>Go to Course:</strong>
                                   <a href="#" onclick="adminNavigate(\'' . $course_name . '\')">Join Course</a>
                               </p>
                           </div>
                       </div>
                   </div>';
           }
       } else {
           echo "No results found.";
       }
   } else {
       echo "Error in the SQL query: " . $connection->error;
   }
}




?>


 
            <div class="col-12 text-center">
                <h2 class="mb-4" style="color: #003366  ; margin-bottom: 30%; margin-tops: 30%;" >Your Courses</h1>
            </div>
            <?php
                fetch_courses($connection, $user_name, $admin_Id);
            ?>
    
   

<script>


function loginMassage( ){
 //   alert("kindly login on page");
}
function adminNavigate(courseName) {
    
    var parentCourseName = courseName;
    const url = `subCourses.php?parentCourseName=${encodeURIComponent(parentCourseName)}`;
    window.location.href = url;

}
function guideNavigate(userName , courseName) {

     //   var parentCourseName = courseName;
     //    const url = `subCourses.php?parentCourseName=${encodeURIComponent(parentCourseName)}`;
      //   window.location.href = url;
         

        var formElement = [userName, courseName];
        var formData = new FormData();
        var UserOnPage = "Guide";
        // Assuming you want to send userName and courseName as form data
        formData.append('userName', userName);
        formData.append('courseName', courseName);
        formData.append('user_on_page', UserOnPage);

        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'temp2.php'); // Change this to the actual PHP file that processes the form

        xhr.onload = function () {
            if (xhr.status === 200) {
                var response = JSON.parse(xhr.responseText);
                // Access individual variables
                var isRegistered = response.isRegistered;
               
                if(isRegistered === true){
                       var parentCourseName = courseName;
                       const url = `subCourses.php?parentCourseName=${encodeURIComponent(parentCourseName)}`;
                       window.location.href = url;
                }
                if(isRegistered === false){
                        alert("hello World");
                }

            } else {
                // Handle errors if any
                alert('An error occurred while processing the form.');
            }
        };

        xhr.onerror = function () {
            // Handle errors if any
            alert('An error occurred while processing the form.');
        };

        xhr.send(formData);


}
function studentNavigate(userName , courseName) {
     //   var parentCourseName = courseName;
     //   const url = `subCourses.php?parentCourseName=${encodeURIComponent(parentCourseName)}`;
     //   window.location.href = url;


        var formElement = [userName, courseName];
        var formData = new FormData();
        var UserOnPage = "student";
        // Assuming you want to send userName and courseName as form data
        formData.append('userName', userName);
        formData.append('courseName', courseName);
        formData.append('user_on_page', UserOnPage);

        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'temp2.php'); // Change this to the actual PHP file that processes the form

        xhr.onload = function () {
            if (xhr.status === 200) {
                var response = JSON.parse(xhr.responseText);
                // Access individual variables
                var isRegistered = response.isRegistered;
               
                if(isRegistered === true){
                       var parentCourseName = courseName;
                       const url = `subCourses.php?parentCourseName=${encodeURIComponent(parentCourseName)}`;
                       window.location.href = url;
                }
                if(isRegistered === false){
                        alert("hello World");
                }


            } else {
                // Handle errors if any
                alert('An error occurred while processing the form.');
            }
        };

        xhr.onerror = function () {
            // Handle errors if any
            alert('An error occurred while processing the form.');
        };

        xhr.send(formData);
}


</script>


