<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit post</title>
</head>
<body>
    <?php
        require "function.php";
        require "dashboard.html";
    ?>
    <h2>Edit post here:</h2>
    <div>
        <form action="" method="post">
            <div>
                <label>Id</label><br>
                <input type="number" name="id" id="id" required>
            </div>
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
        session_start();
        $role=$_SESSION['role'];
        if(isset($_POST['submit'])){
            connectDB('student_manage');
            $id=$_POST['id'];
            $title=$_POST['title'];
            $content=$_POST['content'];
            if($role==="teacher"){
                $sql="SELECT id FROM user_post WHERE id=$id";
            }
            else{
                $sql="SELECT id FROM user_post WHERE id=$id AND user_id={$_SESSION['user_id']}";
            }
            $result=$conn->query($sql);
            if($result->num_rows == 0){
                die("Invalid id, <a href='post_edit.php'>return</a>");
            }
            $sql="UPDATE user_post SET title='{$title}', content='{$content}' WHERE id=$id";
            if($conn->query($sql) === True){
                echo "Successful edit";
                header("Location: dashboard.php");
            }
            else{
                echo "Error in edit post: ".$conn->error;
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
            echo "You don't have any post,or don't have authorized to edit all post";
        }
        disconnectDB();
    ?>
</body>
</html>