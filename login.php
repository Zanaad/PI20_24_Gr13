<?php
include 'db_connect.php';
include 'validation.php';

$validator = new LoginValidation();

session_start();

if (isset($_POST['login'])) {

  $email = mysqli_real_escape_string($conn, $_POST['email']);
  $password = mysqli_real_escape_string($conn, $_POST['password']);

  //$select_users = mysqli_query($conn, "SELECT * FROM `users` WHERE email = '$email'") or die('query failed');
  $stmt=$conn->prepare("SELECT * FROM 'users' WHERE email = ?");
  $stmt->bind_param('s', $email);
  $stmt->execute();
  $result=$stmt->get_result();

  //if (mysqli_num_rows($select_users) > 0) {
    //$row = mysqli_fetch_assoc($select_users);
if($result->num_rows>0){
$row=$result->fetch_assoc();
}
    $salt = $row['salt'];
    $storedHashedPassword = $row['hashPassword'];
    $enteredHashedPassword = hash('sha256', $password . $salt);

    if ($storedHashedPassword === $enteredHashedPassword) {
      $_SESSION['user_id'] = $row['id'];
      $_SESSION['user_name'] = $row['name'];
      $validator->setLoggedin("Logged in successfully!");
      header('location:home.php');
      exit();
    } else {
      $validator->setPasswordErr("Invalid password!");
    }
  } else {
    $validator->setEmailErr("Invalid email!");
  }

$stmt->close();
}

if (isset($_POST['logout'])) {
  session_destroy();
  header('location:login.php');
  exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Log in</title>
  <link rel="stylesheet" href="css/material-design-iconic-font.min.css">
  <link rel="stylesheet" href="login.css">
  <meta name="robots" content="noindex, follow">
</head>

<body>
  <div class="main">
    <section class="sign-in">
      <div class="container">
        <div class="signin-content">
          <div class="signin-image">
            <figure><img src="images/signin-image.jpg" alt="sign up image"></figure>
            <a href="signup.php" class="signup-image-link">Create an account</a>
          </div>
          <div class="signin-form">
            <h2 class="form-title">Log in</h2>
            <form method="POST" class="register-form" id="login-form">
              <div class="form-group">
                <label for="your_email"><i class="zmdi zmdi-account material-icons-name"></i></label>
                <input type="email" name="email" id="your_email" placeholder="Email">
                <span style="color: red;"><?php echo $validator->getEmailErr(); ?></span>
              </div>
              <div class="form-group">
                <label for="your_pass"><i class="zmdi zmdi-lock"></i></label>
                <input type="password" name="password" id="your_pass" placeholder="Password">
                <span style="color:red"><?php echo $validator->getPasswordErr(); ?></span>
              </div>
              <div class="form-group">
                <input type="checkbox" name="remember-me" id="remember-me" class="agree-term">
                <label for="remember-me" class="label-agree-term"><span><span></span></span>Remember me</label>
              </div>
              <div class="form-group form-button">
                <input type="submit" name="login" id="signin" class="form-submit" value="Log in" style="margin-top: 30px;">
                <span style="color: green;"><?php echo $validator->getLoggedin(); ?></span>
                <input type="submit" name="logout" id="signout" class="form-submit" value="Log out">
              </div>
            </form>
            <div class="social-login">
              <span class="social-label">Or login with</span>
              <ul class="socials">
                <li><a href="#"><i class="display-flex-center zmdi zmdi-facebook"></i></a></li>
                <li><a href="#"><i class="display-flex-center zmdi zmdi-twitter"></i></a></li>
                <li><a href="#"><i class="display-flex-center zmdi zmdi-google"></i></a></li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</body>

</html>
