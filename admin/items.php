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

$feedback = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category = $_POST['category'];
    $itemName = $_POST['item_name'];
    $price = $_POST['price'];
    $level = $_POST['required_level'];
    $reputation = $_POST['reputation'];
    $location = $_POST['location'];

    $imageName = '';
    if (isset($_FILES['item_image']) && $_FILES['item_image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $imageName = basename($_FILES['item_image']['name']);
        move_uploaded_file($_FILES['item_image']['tmp_name'], $uploadDir . $imageName);
    }

    $stmt = $conn->prepare("INSERT INTO items (category, name, price, reqlvl, reputation, location, picture) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssissss", $category, $itemName, $price, $level, $reputation, $location, $imageName);

    if ($stmt->execute()) {
        $feedback = "<section class='alert alert-success'>Item added successfully!</section>";
    } else {
        $feedback = "<section class='alert alert-danger'>Error: " . $stmt->error . "</section>";
    }

    $stmt->close();
}

$itemsQuery = "SELECT * FROM items ORDER BY id DESC";
$itemsResult = $conn->query($itemsQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Items</title>
    <link rel="icon" type="image/x-icon" href="./img/favicon.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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

<main class="container my-5">
    <?= $feedback ?>
    <form class="w-75 mx-auto border rounded-5 p-4" method="POST" enctype="multipart/form-data">
        <section class="mb-3">
            <label>Select category of item</label>
            <select class="form-select" name="category" required>
                <option value="" disabled selected>Choose category</option>
                <option>Helmets</option>
                <option>Bridles</option>
                <option>Tops</option>
                <option>Saddles</option>
                <option>Gloves</option>
                <option>Saddle Pads</option>
                <option>Pants</option>
                <option>Decorations</option>
                <option>Shoes</option>
                <option>Legwraps</option>
                <option>Hairstyles</option>
                <option>Saddle Bags</option>
                <option>Makeup</option>
                <option>Pets</option>
                <option>Accessories</option>
                <option>Horses</option>
            </select>
        </section>

        <section class="input-group mb-3">
            <span class="input-group-text">@</span>
            <input type="text" name="item_name" class="form-control" placeholder="Item name" required>
        </section>

        <section class="input-group mb-3">
            <span class="input-group-text">$</span>
            <input type="number" name="price" class="form-control" placeholder="Item price" required>
        </section>

        <section class="input-group mb-3">
            <span class="input-group-text">!</span>
            <input type="number" name="required_level" class="form-control" placeholder="Required Level" required>
        </section>

        <section class="input-group mb-3">
            <span class="input-group-text">#</span>
            <input type="text" name="reputation" class="form-control" placeholder="Reputation" required>
        </section>

        <section class="input-group mb-3">
            <span class="input-group-text">?</span>
            <input type="text" name="location" class="form-control" placeholder="Location" required>
        </section>

        <section class="mb-3">
            <label for="formFile" class="form-label">Upload item image</label>
            <input class="form-control" type="file" name="item_image" id="formFile">
        </section>

        <hr>

        <h1>Filters</h1>

        <section class="mb-3">
            <label>Select category of item</label>
            <select class="form-select" name="filterColor" required>
                <option value="" disabled selected>Choose a color</option>
                <option>N/A</option>
                <option>All Colors</option>
                <option>Pink</option>
                <option>Black</option>
                <option>Green</option>
                <option>Blue</option>
                <option>White</option>
                <option>Yellow</option>
                <option>Red</option>
                <option>Grey</option>
            </select>
        </section>

        <section class="mb-3">
            <label>Select category of item</label>
            <select class="form-select" name="filterColor" required>
                <option value="" disabled selected>Choose a location</option>
                <option value="">All Locations</option>
                <option value="quest reward">Quest reward</option>
                <option value="marley's farm">Marley's farm</option>
                <option value="iceberg store">Iceberg store</option>
                <option value="jorvik city mall">Jorvik city mall</option>
                <option value="cape west village">Cape west village</option>
                <option value="cape west village - the ferry store">Cape west village - the ferry store</option>
                <option value="crescent moon village - moon fashions">Crescent moon village - moon fashions</option>
                <option value="cape west fishing village">Cape west fishing village</option>
                <option value="cape west village / jorvik city mall / silverglade village">Cape west village / jorvik city mall / silverglade village</option>
                <option value="ferdinands horse market">Ferdinands horse market</option>
                <option value="silverglade village - the fashion barn">Silverglade village - the fashion barn</option>
                <option value="earl's hideaway - jorvik stables">Earl's hideaway - jorvik stables</option>
                <option value="dino valley - star shop: the stoneground expedition (aae)">Dino valley - star shop: the stoneground expedition (aae)</option>
                <option value="clothes first floor">Clothes first floor</option>
                <option value="dino valley - star shop: the kallters: outer expedition">Dino valley - star shop: the kallters: outer expedition</option>
                <option value="south hoof">South hoof</option><option value="moorland - general store, aae">Moorland - general store, aae</option>
                <option value="silverglade manor: the wine cellar">Silverglade manor: the wine cellar</option>
                <option value="firgrove - firgrove clothes">Firgrove - firgrove clothes</option>
                <option value="firgrove - the mountain top">Firgrove - the mountain top</option>
                <option value="ingun's cliff /">Ingun's cliff /</option>
                <option value="silverglade village">Silverglade village</option>
                <option value="moorland stables">Moorland stables</option>
                <option value="vineyard's master gardeners store - silverglade manor">Vineyard's master gardeners store - silverglade manor</option>
                <option value="new hillcrest headgear - new hillcrest">New hillcrest headgear - new hillcrest</option>
                <option value="star shop : new hillcrest">Star shop : new hillcrest</option>
                <option value="south hoof farm">South hoof farm</option>
                <option value="paddock island">Paddock island</option>
                <option value="moorland">Moorland</option>
                <option value="silverglade manor - vintage wear">Silverglade manor - vintage wear</option>
                <option value="silverglade village “the silverglade star store”">Silverglade village “the silverglade star store”</option>
                <option value="lifetime star rider shop">Lifetime star rider shop</option>
                <option value="governors fall">Governors fall</option>
                <option value="goldspur's mill /the goldspur store">Goldspur's mill /the goldspur store</option>
                <option value="fortPinta">Fort pinta</option>
                <option value="wolfHallInn">Wolf hall inn</option>
                <option value="valedaleVillage">Valedale village</option>
                <option value="firgroveVillage">Firgrove village</option>
                <option value="jarlaheimJarlaheimStar">Jarlaheim - jarlaheim star</option>
                <option value="jarlaheim">Jarlaheim</option>
                <option value="addiensPlaza">Addiens plaza</option>
                <option value="marleysFarm">Marleys farm</option>
            </select>
        </section>

        <section class="mb-3">
            <label>Select category of item</label>
            <select class="form-select" name="filterColor" required>
                <option value="" disabled selected>Choose hat type</option>
                <option>N/A</option>
                <option value="">All types</option>
                <option value="beanie">Beanie</option>
                <option value="helmet">Helmet</option>
                <option value="cowboy">Cowboy Hat</option>
            </select>
        </section>

        <section class="mb-3">
            <label>Select category of item</label>
            <select class="form-select" name="filterColor" required>
                <option value="" disabled selected>Choose a color</option>
                <option>N/A</option>
                <option>All Colors</option>
                <option>Pink</option>
                <option>Black</option>
                <option>Green</option>
                <option>Blue</option>
                <option>White</option>
                <option>Yellow</option>
                <option>Red</option>
                <option>Grey</option>
            </select>
        </section>

        <button type="submit" class="btn btn-success w-100">Add Item</button>
    </form>

    <hr>

    <section class="container my-5">
        <h2 class="mb-4">All Items</h2>
        <?php if ($itemsResult && $itemsResult->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-center align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Category</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Required Level</th>
                            <th>Reputation</th>
                            <th>Location</th>
                            <th>Picture</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $itemsResult->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['id'] ?></td>
                                <td><?= htmlspecialchars($row['category']) ?></td>
                                <td><?= htmlspecialchars($row['name']) ?></td>
                                <td>$<?= number_format($row['price'], 2) ?></td>
                                <td><?= $row['reqlvl'] ?></td>
                                <td><?= htmlspecialchars($row['reputation']) ?></td>
                                <td><?= htmlspecialchars($row['location']) ?></td>
                                <td>
                                    <?php if (!empty($row['picture'])): ?>
                                        <img src="uploads/<?= htmlspecialchars($row['picture']) ?>" alt="Item Image" style="max-width: 100px;">
                                    <?php else: ?>
                                        No Image
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-warning">No items found.</div>
        <?php endif; ?>
    </section>
</main>
</body>
</html>

<?php $conn->close(); ?>
