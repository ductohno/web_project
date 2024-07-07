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
    <h2>Add new post here</h2>
    <div>
        <form action="" method="post">
            <div>
                <label>Title:</label><br>
                <input type="text" name="title" id="title" required>
            </div>
            <div>
                <label>Content:</label><br>
                <input type="text" name="content" id="content" required>
            </div>
            <div>
                <input type="submit" value="Submit" name="submit">
            </div>
        </form>
    </div>
    <?php
        if(isset($_POST['submit'])){
            session_start();
            connectDB('student_manage');
            $title=$_POST['title'];
            $content=$_POST['content'];
            $user_id=$_SESSION['user_id'];
            $sql="INSERT INTO user_post (title,content,user_id) VALUES ('{$title}','{$content}','{$user_id}')";
            if($conn->query($sql) === True){
                echo "Successful add post";
                header("Location: dashboard.php");
            }
            else{
                echo "Error in add post: ".$conn->error;
            }
            disconnectDB();
        }
    ?>
</body>
</html>