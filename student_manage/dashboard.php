<?php
    require "dashboard.html";
    require "function.php";
    connectDB("student_manage");
    $sql="SELECT * FROM user_post";
    $result=$conn->query($sql);
    if($result->num_rows >0){
        echo "<h1>Here all post:</h1>";
        $index=1;
        while($row=$result->fetch_assoc()){
            echo "<h2>- Post $index:</h2>";
            echo "<h2>{$row['title']}</h2>";
            echo $row['content']."<br>";
            $index=$index+1;
        }
    }
    else{
        echo "<h2>No post here!</h2>";
    }
    disconnectDB();
?>