<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete user</title>
</head>
<body>
    <?php
        require "student.php";
    ?>
    <h1>Delete student</h1>
    <div>
        <form action="" method="post">
            <label>Select the id to delete: </label>
            <input type="number" name="id" id="id" required>
            <input type="submit" value="submit" name="submit">
        </form>
    </div>
    <?php
        if(isset($_POST['submit'])){
            user_delete();
        }
    ?>
</body>
</html>