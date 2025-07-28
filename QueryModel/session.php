
<?php
function IsSessionActive($usertoken,$pathDirection ) {    
    session_start();
    if (isset($_SESSION[$usertoken])) {
    // if (isset($_SESSION['teacher_number'])) {
    header("Location:{$pathDirection}_dashboard.php");
    exit();
    } 
    else{
        header("Location:{$pathDirection}_login.php");
    }
}



?>