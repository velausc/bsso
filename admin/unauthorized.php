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

$countClientsQuery = "SELECT COUNT(*) as client_count FROM orders WHERE DATE(date) = '$today'";
$countClientsResult = $conn->query($countClientsQuery);
$countClients = $countClientsResult ? $countClientsResult->fetch_assoc()['client_count'] : 0;

$sumPricesQuery = "SELECT SUM(price) as total_price FROM orders WHERE DATE(date) = '$today'";
$sumPricesResult = $conn->query($sumPricesQuery);
$totalPrice = $sumPricesResult ? $sumPricesResult->fetch_assoc()['total_price'] ?? 0 : 0;

$sql = "INSERT INTO sum_table (date, amount) VALUES ('$today', '$totalPrice') ON DUPLICATE KEY UPDATE amount = amount + $totalPrice";
$conn->query($sql);
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
    <section class="container text-center">
        <section class="alert alert-danger m-5" role="alert">
            <h4 class="alert-heading">Problem</h4>
            <p class="text-center">No authorization</p>
        </section>
    </section>
</body>
</html>

<?php
$conn->close();
?>