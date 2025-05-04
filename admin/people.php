<?php
session_start();
$host = 'localhost';
$dbname = 'bssodb';
$username = 'root';
$password = '';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_COOKIE['user_id']) || !isset($_COOKIE['user_name'])) {
    header("Location: adminLogin.php");
    exit();
}

$user_name = htmlspecialchars($_COOKIE['user_name']);
$user_id = htmlspecialchars($_COOKIE['user_id']);

$positionQuery = "SELECT position FROM users WHERE id = '$user_id'";
$positionResult = $conn->query($positionQuery);
$user_position = '';

if ($positionResult && $positionResult->num_rows > 0) {
    $user_position = $positionResult->fetch_assoc()['position'];
}

if ($user_position === 'Helper') {
    header("Location: unauthorized.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_user'])) {
    $username = $conn->real_escape_string($_POST['username']);
    $name = $conn->real_escape_string($_POST['name']);
    $password = $_POST['password'];
    $repeatPassword = $_POST['repeat_password'];
    $position = $conn->real_escape_string($_POST['position']);

    if ($password !== $repeatPassword) {
        $error_message = "Passwords don't match!";
    } else {
        $checkUser_Query = "SELECT * FROM users WHERE username = '$username'";
        $checkUser_Result = $conn->query($checkUser_Query);
        
        if ($checkUser_Result->num_rows > 0) {
            $error_message = "Id already exists!";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            $sql = "INSERT INTO users (username, name, password, position) VALUES ('$username', '$name', '$hashedPassword', '$position')";
            if ($conn->query($sql) === TRUE) {
                echo "<script>alert('Added!');</script>";
            } else {
                $error_message = "Error: " . $conn->error;
            }
        }
    }
}

if (isset($_POST['delete_user'])) {
    $userId = intval($_POST['user_id']);
    $deleteQuery = "DELETE FROM users WHERE id = $userId";
    if ($conn->query($deleteQuery) === TRUE) {
        echo "<script>alert('User has been deleted!');</script>";
    } else {
        $error_message = "Błąd: " . $conn->error;
    }
}

$usersQuery = "SELECT * FROM users";
$usersResult = $conn->query($usersQuery);
?>

<!DOCTYPE html>
<html lang="en">
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
    <nav class="navbar fixed-bottom navbar-expand-sm navbar-dark bg-dark">
        <section class="container-fluid">
            <a class="navbar-brand" href="#">Welcome, <?php echo $user_name; ?>!</a>
            <section class="collapse navbar-collapse">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link active" href="./panel.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link active" href="./items.php">Add items</a></li>
                    <?php if($user_position === 'ROOT' || $user_position === 'Administrator'): ?>
                    <li class="nav-item"><a class="nav-link active" href="./people.php">Add access</a></li>
                    <?php endif; ?>
                </ul>
                <section class="ms-auto">
                    <form action="logout.php" method="post" class="d-inline">
                        <button type="submit" class="btn btn-light">Log out</button>
                    </form>
                </section>
            </section>
        </section>
    </nav>
    <section class="container w-25">
        <form class="m-5 border rounded-3 text-center" method="POST" action="">
            <img class="mb-4" src="/img/logo.png" alt="" height="80">
            <h1 class="h3 mb-3 fw-normal">Add user</h1>
            <section class="form-floating m-3">
                <input type="text" class="form-control" id="floatingInput" name="username" placeholder="Identificator" required>
                <label for="floatingInput">Identificator</label>
            </section>
            <section class="form-floating m-3">
                <input type="text" class="form-control" id="floatingInputName" name="name" placeholder="Name" required>
                <label for="floatingInputName">Name</label>
            </section>
            <section class="form-floating m-3">
                <input type="password" class="form-control" id="floatingPassword1" name="password" placeholder="Password" required>
                <label for="floatingPassword1">Password</label>
            </section>
            <section class="form-floating m-3">
                <input type="password" class="form-control" id="floatingPassword2" name="repeat_password" placeholder="Repeat password" required>
                <label for="floatingPassword2">Repeat password</label>
            </section>
            <section class="input-group m-3 w-75">
                <label class="input-group-text" for="inputGroupSelect01">!</label>
                <select class="form-select" id="inputGroupSelect01" name="position" required>
                    <option selected disabled>Choose role</option>
                    <option value="Administrator">Administrator</option>
                    <option value="Moderator">Moderator</option>
                    <option value="Helper">Helper</option>
                </select>
            </section>
            <button class="btn btn-primary w-50 m-3" type="submit" name="create_user">Create</button>
        </form>
    </section>
    <section class="container">
        <table class="table text-center">
            <thead>
                <tr>
                <th scope="col">#</th>
                <th scope="col">Name</th>
                <th scope="col">Role</th>
                <th scope="col">Settings</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($usersResult->num_rows > 0) {
                    while ($row = $usersResult->fetch_assoc()) {
                        echo "<tr>
                                <th scope='row'>" . $row['id'] . "</th>
                                <td>" . htmlspecialchars($row['name']) . "</td>
                                <th class='";
                        switch ($row['position']) {
                            case 'ROOT':
                                echo "text-danger";
                                break;
                            case 'Administrator':
                                echo "text-danger";
                                break;
                            case 'Moderator':
                                echo "text-warning";
                                break;
                            case 'Helper':
                                echo "text-info";
                                break;
                        }
                        echo "'>" . htmlspecialchars($row['position']) . "</th>
                                <td>";
                                $current_can_delete = false;

                                if ($user_position === 'ROOT' && $row['position'] !== 'ROOT') {
                                    $current_can_delete = true;
                                } elseif ($user_position === 'Administrator' && in_array($row['position'], ['Moderator', 'Helper'])) {
                                    $current_can_delete = true;
                                }
                                
                                if ($current_can_delete) {
                                    echo "<form method='POST' action=''>
                                            <input type='hidden' name='user_id' value='" . $row['id'] . "'>
                                            <button type='submit' name='delete_user' class='btn btn-danger'>&cross;</button>
                                        </form>";
                                }
                                
                        echo "</td>
                            </tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>Brak użytkowników</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </section>
    <?php if (isset($error_message)): ?>
        <section class="toast show" role="alert" aria-live="assertive" aria-atomic="true" style="position: absolute; top: 20px; right: 20px;">
            <section class="toast-header">
                <strong class="me-auto">System</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </section>
            <section class="toast-body">
                Error - <?php echo htmlspecialchars($error_message); ?>
            </section>
        </section>
    <?php endif; ?>
</body>
</html>