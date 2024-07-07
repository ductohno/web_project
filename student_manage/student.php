<?php
    session_start();
    $role=$_SESSION['role'];
    if($role==="teacher"){
        require "student.html";
        require "function.php";
        connectDB('student_manage');
        $sql="SELECT * FROM user_db";
            
        $result=$conn->query($sql);

        if ($result->num_rows > 0) {         
            while($row = $result->fetch_assoc()) {
                foreach ($row as $key=>$value) {
                    echo " " .$key.": ". $value . " ";
                }
            echo "<br>";
            }
        } else {
            echo "No data found"."<br>";
        }
        disconnectDB();
    }
    else{
        die("No authorized to access");
    }
?>
 