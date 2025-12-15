<?php
session_start();

if (isset($_POST['submit'])) {
    // Get form data
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validation
    $errors = [];

    if (empty($name)) {
        $errors[] = "Name is required.";
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "A valid email address is required.";
    }

    if (empty($password)) {
        $errors[] = "Password is required.";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters long.";
    } elseif (!preg_match("/[A-Z]/", $password) || !preg_match("/[0-9]/", $password)) {
        $errors[] = "Password must contain at least one uppercase letter and one number.";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    // If validation passed, proceed to store data
    if (empty($errors)) {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Create a new user array
        $new_user = [
            'name' => $name,
            'email' => $email,
            'password' => $hashed_password
        ];

        // Read existing users from the JSON file
        $file_path = 'users.json';
        if (file_exists($file_path)) {
            $users_data = json_decode(file_get_contents($file_path), true);
        } else {
            $users_data = [];
        }

        // Add the new user to the existing array
        $users_data[] = $new_user;

        // Write the updated array back to the JSON file
        if (file_put_contents($file_path, json_encode($users_data, JSON_PRETTY_PRINT))) {
            $_SESSION['message'] = "Registration successful!";
            $_SESSION['message_type'] = "success";
            header('Location: registration.php');
            exit();
        } else {
            $_SESSION['message'] = "Error writing to file.";
            $_SESSION['message_type'] = "error";
        }
    } else {
        // Display errors
        $_SESSION['message'] = implode('<br>', $errors);
        $_SESSION['message_type'] = "error";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <link href="css/style.css" rel="stylesheet">
    <style>
        .error { color: red; }
        .success { color: green; }
    </style>
</head>
<body>
    <h1>User Registration</h1>
    <?php
    if (isset($_SESSION['message'])) {
        echo "<div class='" . $_SESSION['message_type'] . "'>" . $_SESSION['message'] . "</div>";
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
    }
    ?>
    <form method="POST" action="registration.php">

    	<div class="from-input">
	        <label for="name">Name:</label>
	        <input type="text" id="name" name="name" required placeholder="Enter Your Name">
    	</div>
        
        <div class="from-input">
	        <label for="email">Email:</label>
	        <input type="email" id="email" name="email" required placeholder="Enter Your Email">
    	</div>
        
        <div class="from-input">
	        <label for="password">Password:</label>
	        <input type="password" id="password" name="password" required placeholder="Enter Password">
    	</div>
        
        <div class="from-input">
	        <label for="confirm_password">Confirm Password:</label>
	        <input type="password" id="confirm_password" name="confirm_password" required placeholder="Confirm Password">
    	</div>
        
        <button type="submit" name="submit">Register</button>
    </form>
</body>
</html>
