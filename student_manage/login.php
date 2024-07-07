<?php
    require "login.html";
    require "function.php";
    if(isset($_POST['login'])){
        session_start();
        connectDB('student_manage');
        $username=$_POST['username'];
        $password=$_POST['password'];

        $sql = "SELECT id, password, role FROM user_db WHERE username = '$username'";

        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            if ($password===$row['password']) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $username;
                $_SESSION['role'] = $row['role'];
                
                header("Location: dashboard.php");
                exit();
            } else {
                echo "Incorrect password!";
            }
        } else {
            echo "User not exist!";
        }
        
        disconnectDB();
    }
?>