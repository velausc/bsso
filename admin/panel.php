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

$today = date('Y-m-d');
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
    <!-- <section class="container text-center">
        <img src="./img/logo.png" class="img-fluid" alt="Logo">
        <section class="row justify-content-center mt-4">
            <section class="col-md-4">
                <section class="card text-bg-primary mb-3">
                    <section class="card-header text-center">Informacje</section>
                    <section class="card-body">
                        <h5 class="card-title text-center">Liczba klientów dzisiaj</h5>
                        <p class="card-text text-center"><?php echo $countClients; ?></p>
                    </section>
                </section>
            </section>
            <?php if ($user_position === 'ROOT' || $user_position === 'Wlasciciel'): ?>
            <section class="col-md-4">
                <section class="card text-bg-success mb-3">
                    <section class="card-header text-center">Finanse</section>
                    <section class="card-body">
                        <h5 class="card-title text-center">Dzisiejszy <i>potencjalny</i> przychód</h5>
                        <p class="card-text text-center"><?php echo $totalPrice; ?> zł</p>
                    </section>
                </section>
            </section>
            <?php endif; ?>
        </section>
    </section> -->
</body>
</html>

<?php
$conn->close();
?>