<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add post</title>
</head>
<body>
    <?php
        require "function.php";
        require "dashboard.html";
    ?>
    <h2>Select id to delete</h2>
    <div>
        <form action="" method="post">
            <div>
                <label>Id:</label><br>
                <input type="number" name="id" id="id" required>
            </div>
            <div>
                <input type="submit" value="Submit" name="submit">
            </div>
        </form>
    </div>
    <?php
        session_start();
        $role=$_SESSION['role'];
        if(isset($_POST['submit'])){
            connectDB('student_manage');
            $id=$_POST['id'];
            if($role==="teacher"){
                $sql="SELECT id FROM user_post WHERE id=$id";
            }
            else{
                $sql="SELECT id FROM user_post WHERE id=$id AND user_id={$_SESSION['user_id']}";
            }
            $result=$conn->query($sql);
            if($result->num_rows == 0){
                die("Invalid id <br><a href='post_delete.php'>return</a>");
            }
            $sql="DELETE FROM user_post WHERE id=$id";
            if($conn->query($sql) === True){
                echo "Successful delete";
                header("Location: dashboard.php");
            }
            else{
                echo "Error in delete post: ".$conn->error;
            }
            disconnectDB();
        }
        connectDB("student_manage");
        if($role==="teacher"){
            $sql="SELECT id,title,content FROM user_post";
        }
        else{
            $sql="SELECT id,title,content FROM user_post WHERE user_id={$_SESSION['user_id']}";
        }
        $result=$conn->query($sql);
        if($result->num_rows >0){
            while($row=$result->fetch_assoc()){
                echo "<h2>Id: {$row['id']}</h2>";
                echo "<h2>{$row['title']}</h2>";
                echo $row['content']."<br>";
            }
        }
        else{
            echo "You don't have any post,or don't have authorized to delete all post";
        }
        disconnectDB();
    ?>
</body>
</html>