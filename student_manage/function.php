<?php
function connectDB($DB_name)
{
    global $conn;
    $servername = "localhost";  
    $username = "root";        
    $password = ""; 
    $dbname = $DB_name;        

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connect error: " . $conn->connect_error);
    }
}
function disconnectDB()
{
    global $conn;
    if($conn){
        mysqli_close($conn);
    }
}
function user_add(){
    connectDB('student_manage');
    global $conn;
        try{
            $username=$_POST['username'];
            $password=$_POST['password'];
            $rewrite_password=$_POST['rewrite_password'];
            $email=$_POST['email'];
            $phone_number=$_POST['phone_number'];
            $role=$_POST['role'];
            if($password!==$rewrite_password){
                die("Password and rewrite password need same");
            }
            $insert_sql = "INSERT INTO user_db (username, password, email, phone_number, role) VALUES ('{$username}','{$password}','{$email}','{$phone_number}','{$role}')";
            
            if ($conn->query($insert_sql) === TRUE) {
                echo "Sucessfully add user";
                header("Location: student.php");
            } else {
                echo "Error in database connect: " . $conn->error;
            }
        }
        catch (Exception $e){
            echo "Student name existed, please register other";
        }
    disconnectDB();
}
function user_edit(){
    connectDB('student_manage');
    global $conn;
    $id=intval($_POST['id']);
    $username=$_POST['username'];
    $password=$_POST['password'];
    $rewrite_password=$_POST['rewrite_password'];
    $email=$_POST['email'];
    $phone_number=$_POST['phone_number'];
    $role=$_POST['role'];
    if($password!==$rewrite_password){
        die("Password and rewrite password need same");
    }
    $sql="SELECT id FROM user_db WHERE id='$id'";
    $result=$conn->query($sql);
    if($result->num_rows > 0){
        $sql="SELECT id,username FROM user_db where username='$username' AND id!='$id'";
        $result=$conn->query($sql);
        if($result->num_rows > 0 ){
            die('Username exist');
        }
        $edit_sql = "UPDATE user_db SET username='$username', password='$password', email='$email', phone_number='$phone_number', role='$role' WHERE id=$id";
        if ($conn->query($edit_sql) === TRUE) {
            echo "Update successfully";
            header("Location: student.php");
        } else {
            echo "Error in update information: " . $conn->error;
        }
    }
    else{
        die("Id not exist, update fail");
    }
}
function user_delete(){
    connectDB('student_manage');
    global $conn;
    $id=intval($_POST['id']);
    $sql="SELECT id FROM user_db WHERE id='$id'";
    $result=$conn->query($sql);
    if($result->num_rows > 0){
        if ($id==$_SESSION['user_id']){
            die("<h3>You can't delete yourself</h3>");
        }
        $sql="DELETE FROM user_db WHERE id=$id";
        if($conn->query($sql)=== True){
            echo "Delete id $id complete";
            header("Location: student.php");
        }
        else{
            echo "Error in delete information: ".$conn->error;
        }
    }
    else{
        die("Username not exist, delete false");
    }
}
?>