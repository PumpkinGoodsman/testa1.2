<?php 
    include 'connection.php';

    $lecture_number = 0;
    $lecture_topic = "none";
    $course_name = "none";
    $user_on_page = "unknown";
    $user_name ="unknown";
    $admin_Id = "unknown";
    $is_page_login= "unknown";
    $user_on_page=  "unknown";
    
    session_start();
    if (isset($_SESSION["user_on_page"])) {
      $user_name =  $_SESSION["username"] ;
      $admin_Id = $_SESSION["admin_Id"];
      $is_page_login= $_SESSION["is_page_login"];
      $user_on_page=  $_SESSION["user_on_page"];
    
    
      }
    function exit_page(){
      header("Location: index.php");
    } 
    if(!isset($_SESSION["user_on_page"])){
       header("Location: index.php");
    }
    
    
    if (isset($_GET['courseName'])) {
        $course_name = $_GET['courseName'];
        
    }
    
    if (isset($_GET['courseName'])   && isset($_GET['lectureNo'])) {
        $course_name = $_GET['courseName'];
        $lecture_number = $_GET['lectureNo'];
        
        // Sanitize input if necessary (e.g., using htmlspecialchars)
        $course_name = htmlspecialchars($course_name);
        $lecture_topic = htmlspecialchars($lecture_topic);
    } else {
         
    }
    
   
    function show_lectures_topics($connection, $course_name) {
      // Convert course name to lowercase
      $course_to_lower = strtolower($course_name);
      
      // Replace spaces with backslashes
      $course_to_lower_slashes = str_replace(' ', '_', $course_to_lower);
  
      $sql = "SELECT lecture_no, Lecture_topic , lesson_pdf  FROM `$course_to_lower`";
      $result = $connection->query($sql);
  
      if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
              $lecture_no = htmlspecialchars($row["lecture_no"]);
              $lecture_topic = htmlspecialchars($row["Lecture_topic"]);
              // You can handle these values as required
               $pdf_link = htmlspecialchars($row["lesson_pdf"]);
              // $video_url = htmlspecialchars($row["video_url"]);
              // $lecture_description = htmlspecialchars($row["Lecture_Description"]);
              // $lecture_outcomes = htmlspecialchars($row["lecture_outcomes"]);
  
              echo '
              <li>
               <a style="color: #000000;"  href="#lesson' . $lecture_no . '">' . $lecture_topic . '</a> <br>
               <span class="pdf_link"><a style="color:#006600;" href="' . $pdf_link . '" download>Download PDF</a></span>
              </li>
              ';
          }
      }
  }
  
   

  function show_lecture($connection, $course_name, $lecture_topic, $lecture_no) {
    $lower_case_Course_name = strtolower($course_name);
    // Use prepared statements to prevent SQL injection
    $sql = "SELECT lecture_no, Lecture_topic, Lecture_Description, lecture_outcomes, video_url, lesson_pdf FROM `$lower_case_Course_name` WHERE lecture_no = ?";

    
    // Prepare the statement
    $stmt = $connection->prepare($sql);
    
    if ($stmt === false) {
        die("Prepare failed: " . $connection->error);
    }

    // Bind parameters
    $stmt->bind_param("s", $lecture_no);
    
    // Execute the statement
    if (!$stmt->execute()) {
        die("Execute failed: " . $stmt->error);
    }
    
    // Get the result
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Use htmlspecialchars to escape data for safe HTML output
            $lecture_no = htmlspecialchars($row["lecture_no"]);
            $lecture_topic = htmlspecialchars($row["Lecture_topic"]);
            $Lecture_Description = htmlspecialchars($row["Lecture_Description"]);
            $lecture_outcomes = htmlspecialchars($row["lecture_outcomes"]);
            $video_url = htmlspecialchars($row["video_url"]);
            $lesson_pdf = htmlspecialchars($row["lesson_pdf"]);

            // Output HTML using double quotes and escaped double quotes within attributes
            echo "
            <div class='course_details' style='background-image: none; background-color:  #E0E0E0;  filter: drop-shadow(2px 2px 4px grey);'>
                <div class='video_box'>
                    <h2 style='color: #006600;'>{$course_name}</h2>
                    <iframe src=\"$video_url\" allowfullscreen></iframe>
                </div>
                <div class='course_info'>
                    <h4 style='color:#006600;'>Video Description:</h4>
                    <p style='color:#000000;'>{$Lecture_Description}</p>
                    <h4 style='color: #006600;'>Lecture Outcomes:</h4>
                    <ul>
                        <li style='color:#000000;' >{$lecture_outcomes}</li>
                    </ul>
                    <div class='pdf_download'>
                        <h4 style='color:#006600;'>Download PDF:</h4>
                        <p><a style='color:#000000;' href='{$lesson_pdf}' download>{$lecture_no} PDF</a></p>
                    </div>
                    <div class='lesson_navigation'>
                        <a href='#' class='btn btn-primary' onclick='goToPreviousLecture(\"{$course_name}\", {$lecture_no})'>Previous Lesson</a>
                        <a href='#' class='btn btn-primary' onclick='goToNextLecture(\"{$course_name}\", {$lecture_no})'>Next Lesson</a>
                    </div>
                </div>
            </div>
            ";
            
        }
    } else {
        // Handle the case where no matching lecture was found
        echo "
        <div class='fancy-message'>
            <h2>Congratulations!</h2>
            <p>You have successfully completed the course.</p>
        </div>
        ";
    }

    // Close the statement
    $stmt->close();
}



?>    



<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <meta name="description" content="">
  <meta name="author" content="">

  <title>Festava Live - Bootstrap 5 CSS Template</title>

  <!-- CSS FILES -->
  <link rel="preconnect" href="https://fonts.googleapis.com">

  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100;200;400;700&display=swap" rel="stylesheet">

  <link href="css/bootstrap.min.css" rel="stylesheet">

  <link href="css/bootstrap-icons.css" rel="stylesheet">

  <link href="css/templatemo-festava-live.css" rel="stylesheet">

  <style>
    body{
      background-color:  #D3D3D3;
    }
    .navbar{
      background-color: black;
    }
    .course_details {
      background-color: #f5f5f5;
      padding: 30px;
      border-radius: 10px;
      margin-bottom: 30px;
      margin-top: 10%;
      background-image: url("images/edward-unsplash-blur.jpg");
      
    }

    .course_details .video_box {
      margin-bottom: 20px;
    }

    .course_details .course_info {
      line-height: 1.8;
       
      
    }

    .course_details h2 {
      font-size: 24px;
      margin-bottom: 15px;
    }

    .course_details p {
      margin-bottom: 20px;
    }

    .course_details h4 {
      font-size: 18px;
      margin-bottom: 10px;
    }

    .course_details ul {
      list-style-type: disc;
      margin-left: 20px;
      margin-bottom: 20px;
    }

    .course_details ul li {
      margin-bottom: 5px;
      
    }

    .course_details ul li a {
      color: #333;
      text-decoration: none;
    }

    .course_details ul li a:hover {
      color: #007bff;
    }
    .lesson_list{
      background-color: #f5f5f5;
      margin-top: 32%;
      border-radius: 10px;
       
    }
    .lesson_list li  a {
      color: whitesmoke;
       
    }
    .lesson_list li   {
      color: whitesmoke;
      border-bottom: 2px solid white;
      list-style: none;
      width: 16vw;
     
    }
     iframe{
        width: 770px;
        height: 450px;
    }
    .fancy-message {
      background-color: transparent;
      border: 2px solid #006600;
      border-radius: 10px;
      padding: 20px;
      text-align: center;
      margin: 20px auto;
      width: 50%;
    }

    .fancy-message h2 {
        color: #006600;
    }

    .fancy-message p {
        color: #000;
    }


    @media (max-width: 830px ) and (min-width: 714px){
      iframe{
        width: 450px;
        height: 450px;
      }
    }
    @media (max-width: 430px ) and (min-width: 320px){
      iframe{
        width: 320px;
        height: 450px;
      }
    }  
    
  </style>
</head>

<body style="background-image: none; background-color:  #D3D3D3;" >
    <main>
      <?php
            include 'header.php';
            if($user_on_page == "unknown"){
              show_hader_2();
            }
            if($user_on_page == "admin"){
                show_admin_hader_2();
            }
            if($user_on_page == "guide"){
                show_guide_hader_2();
            }
            if($user_on_page == "student"){
                show_student_hader_2();
            }
      ?>
      <div class="hero_area">
        <!-- header section strats -->

        <!-- end header section  idth="100%" height="400" -->
      </div>

      <!-- content section -->
      <section class="content_section layout_padding">
        <div class="col-12 text-center" >
          <h2 class="mb-4" style="margin-top: 10%; color: #003366;"><?php echo $course_name . " " .   "lecture" ." ". $lecture_number ?></h2>
        </div>
        <div class="container" >
          <div class="row">
            <div class="col-md-3" >
              <div class="lesson_list" style="background-image: none; background-color:  #D3D3D3; ;  filter: drop-shadow(2px 2px 4px grey);">
                <h6  style="color: #006600; margin-left:12%;"><?php  echo $course_name ?> Lessons</h6>
                <ul>
                    <?php
                      show_lectures_topics($connection, $course_name );
                    ?>
                </ul>
              </div>
            </div>
            <div class="col-md-9">
              <?php
                  show_lecture($connection, $course_name, $lecture_topic ,  $lecture_number  );
              ?>
            </div>
          </div>
        </div>
      </section>
      <?php include 'events.php'; ?>
      <?php include 'footer.php'; ?>
    </main>  
 
  <!-- end content section -->

  <!-- footer section -->
  
  <!-- end footer section -->



</body>
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.sticky.js"></script>
    <script src="js/click-scroll.js"></script>
    <script src="js/custom.js"></script>
    <script type="text/javascript" src="js/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.js"></script>
    <script>
        function goToPreviousLecture(courseName , lectureNo){
          
          lectureNo = lectureNo - 1 ;
          if(lectureNo <= 0 ){
            alert("this is the first lecture");
          }
          if(lectureNo > 0 ){
            const url = `courseLecture.php?courseName=${encodeURIComponent(courseName)}&lectureNo=${encodeURIComponent(lectureNo)}`;
            // Use window.location.href to navigate to the specified URL
            window.location.href = url;
          }
        }

        function goToNextLecture(courseName , lectureNo){
          
            lectureNo = lectureNo + 1 ;
            const url = `courseLecture.php?courseName=${encodeURIComponent(courseName)}&lectureNo=${encodeURIComponent(lectureNo)}`;
            // Use window.location.href to navigate to the specified URL
            window.location.href = url;
        }
    </script>
</html>
