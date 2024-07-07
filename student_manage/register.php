<?php
    require "register.html";
    require "function.php";
    if(isset($_POST['register'])){
        connectDB('student_manage');
        try{
            $username=$_POST['username'];
            $password=$_POST['password'];
            $email=$_POST['email'];
            $phone_number=$_POST['phone_number'];

            $insert_sql = "INSERT INTO user_db (username, password, email, phone_number, role) VALUES ('{$username}','{$password}','{$email}','{$phone_number}','student')";
            
            if ($conn->query($insert_sql) === TRUE) {
                echo "Sucessfully add user";
                header("Location: login.php");
            } else {
                echo "Error in database connect: " . $conn->error;
            }
        }
        catch (Exception $e){
            echo "Student name existed, please register other";
        }
    }
    disconnectDB();
?>