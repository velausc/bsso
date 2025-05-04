<?php
$host = 'localhost';
$dbname = 'bssodb';
$username = 'root';
$password = '';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$showToast = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $user);

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $hashedPassword = $row['password'];

        if (password_verify($pass, $hashedPassword)) {
            setcookie("user_id", $row['id'], time() + (86400 * 30), "/");
            setcookie("user_name", $row['name'], time() + (86400 * 30), "/");
            header("Location: panel.php");
            exit();
        } else {
            $showToast = true;
        }
    } else {
        $showToast = true;
    }

    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Panel</title>
    <link rel="icon" type="image/x-icon" href="./img/favicon.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <section class="container text-center" style="transform: translateY(200px);">
        <main class="form-signin w-25 m-auto border rounded-3">
            <form class="m-3" method="POST" action="">
              <section class="d-flex justify-content-center mb-4">
                <img class="rounded-circle" src="./img/logo.png" alt="" height="80">
              </section>
              <h1 class="h3 mb-3 fw-normal text-center">Login Panel</h1>
              <section class="form-floating m-2">
                <input type="text" name="username" class="form-control" id="floatingInput" placeholder="Nazwa użytkownika" min="4" max="16" required>
                <label for="floatingInput">Username</label>
              </section>
              <section class="form-floating m-2">
                <input type="password" name="password" class="form-control" id="floatingPassword" placeholder="Hasło" min="8" max="32" required>
                <label for="floatingPassword">Password</label>
              </section>
              <section class="form-check text-start my-3">
                <input class="form-check-input" type="checkbox" value="remember-me" id="flexCheckDefault">
                <label class="form-check-label" for="flexCheckDefault">
                  Remember me
                </label>
              </section>
              <button class="btn btn-primary w-100 py-2" type="submit">Log in</button>
              <p class="mt-5 mb-3 text-body-secondary text-center">&copy; <script>document.write(new Date().getFullYear())</script> Better Star Stable Database</p>
            </form>
          </main>
    </section>

    <?php if ($showToast): ?>
    <section class ="toast" role="alert" aria-live="assertive" aria-atomic="true" style="position: fixed; top: 20px; right: 20px;">
      <section class="toast-header">
        <strong class="me-auto">Notification</strong>
        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
      </section>
      <section class="toast-body">
        Wrong username or password.
      </section>
    </section>
    <script>
      $(document).ready(function() {
        $('.toast').toast({ delay: 3000 });
        $('.toast').toast('show');
      });
    </script>
    <?php endif; ?>
</body>
</html>