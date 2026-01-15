<?php
session_start();
include 'db.php';

$error = "";

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 1) {
        $user = $res->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            header("Location: inventory.php");
            exit;
        }
    }
    $error = "Invalid username or password";
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Login | JB Builders</title>
<link rel="stylesheet" href="style.css">
<style>
.login-box {
  max-width: 400px;
  margin: 80px auto;
}
.error {
  color: red;
  text-align: center;
  margin-bottom: 10px;
}
</style>
</head>
<body>

<div class="container login-box">
<h2>JB Builders Login</h2>

<?php if ($error): ?>
<p class="error"><?= $error ?></p>
<?php endif; ?>

<form method="POST">
  <input type="text" name="username" placeholder="Username" required>
  <input type="password" name="password" placeholder="Password" required>
  <button name="login">Login</button>
</form>
</div>

</body>
</html>
