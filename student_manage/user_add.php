<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add student</title>
</head>
<body>
    <?php
        require "student.php";
    ?>
    <h1>New student/teacher</h1>
    <div>
        <form action="user_add.php" method="post">
            <label>Username:</label><br>
                <input type="username" name="username" id="username" required><br>
            <label>Password:</label><br>
                <input type="password" name="password" id="password" required><br>
            <label>Enter password again:</label><br>
                <input type="password" name="rewrite_password" id="rewrite_password" required><br>
            <label>Email:</label><br>
                <input type="email" name="email" id="email" required><br>
            <label>Phone number</label><br>
                <input type="tel" name="phone_number" id="phone_number" required><br>
            <label>Teacher</label>
                <input type="radio" name="role" id="teacher" value="teacher" required><br>
            <label>Student</label>
                <input type="radio" name="role" id="student" value="student" required><br>
            <input type="submit" value="Add new" name="add_new" required><br>
         </form>
    </div>
    <?php
        if(isset($_POST['add_new'])){
            user_add();
        }
    ?>
    
</body>
</html>