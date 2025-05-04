<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BSSO Database</title>
    <link rel="stylesheet" href="main.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" crossorigin="anonymous"></script>
</head>
<body>
    <header class="d-flex flex-wrap align-items-center justify-content-center justify-content-md-between py-3 mb-4 border-bottom text-dark">
        <section class="col-md-3 mb-2 mb-md-0">
          <a href="/" class="d-inline-flex text-white text-decoration-none">
            <h3 class="mb-0 mx-3">BSSO Database</h3>
          </a>
        </section>
        <ul class="nav col-12 col-md-auto mb-2 justify-content-center mb-md-0">
          <li><button type="button" class="btn btn-outline-light mx-1" onclick='location.href="./index.html"'>Home</button></li>
          <li><button type="button" class="btn btn-outline-light mx-1" onclick='location.href="./coffee.html"'>Buy us a coffee!</button></li>
          <li><button type="button" class="btn btn-outline-light mx-1" onclick='location.href="./license.html"'>License</button></li>
        </ul>
        <section class="col-md-3 text-end">
          <button type="button" class="btn btn-outline-light me-2 mx-3" onclick='location.href="./admin/login.php"'>Login</button>
        </section>
      </header>
      <main class="container py-4">
          <?php
          $conn = new mysqli("localhost", "root", "", "bssodb");

          if ($conn->connect_error) {
              die("<div class='alert alert-danger'>Connection failed: " . $conn->connect_error . "</div>");
          }

          $category = isset($_GET['category']) ? strtolower($conn->real_escape_string($_GET['category'])) : '';

          if ($category) {
              echo "<h2 class='text-center mb-4 text-capitalize text-white'>" . htmlspecialchars($category) . "</h2>";

              $sql = "SELECT id, name, price, reqlvl, reputation, location, picture FROM items WHERE category = '$category'";
              $result = $conn->query($sql);

              if ($result && $result->num_rows > 0) {
                  echo "<div class='row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4'>";
                  while ($row = $result->fetch_assoc()) {
                      echo "
                      <div class='col'>
                          <div class='card h-100 shadow-lg text-center'>
                            <br>
                              <img src='admin/uploads/" . htmlspecialchars($row['picture']) . "' class='card-img-top' alt='" . htmlspecialchars($row['name']) . "'>
                              <hr>
                              <div class='card-body'>
                                  <h5 class='card-title'>" . htmlspecialchars($row['name']) . "</h5>
                                  <br>
                                  <p>Price: ". htmlspecialchars($row['price']) ." </p>
                                  <p>Required level: ". htmlspecialchars($row['reqlvl']) ." </p>
                                  <p>Reputation: ". htmlspecialchars($row['reputation']) ." </p>
                                  <p>Location: ". htmlspecialchars($row['location']) ." </p>
                              </div>
                          </div>
                      </div>";
                  }
                  echo "</div>";
              } else {
                  echo "<div class='alert alert-warning text-center'>No items found for this category.</div>";
              }
          } else {
              echo "<div class='alert alert-danger text-center'>No category selected.</div>";
          }

          $conn->close();
          ?>
      </main>
     
      <footer class="d-flex flex-wrap justify-content-between align-items-center py-3 my-4 border-top text-white">
        <p class="col-md-4 mb-0">&copy; <script>document.write(new Date().getFullYear())</script> Better Star Stable Database</p>
      
        <a href="/" class="col-md-4 d-flex align-items-center justify-content-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none" aria-label="Bootstrap">
          <svg class="bi me-2" width="40" height="32" aria-hidden="true"><use xlink:href="#bootstrap"></use></svg>
        </a>
      
        <ul class="nav col-md-4 justify-content-end">
          <li class="nav-item"><a href="#" class="nav-link px-2 text-white">BSSO Discord</a></li>
          <li class="nav-item"><a href="#" class="nav-link px-2 text-white">BSSO DB Discord</a></li>
        </ul>
      </footer>
      
</body>
</html>