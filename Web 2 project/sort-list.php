<?php
/*
Name: Tyson La
Date: September 20th
Description: Blog Home Page
*/


require('authenticate.php');

$servername = "localhost";
$username = "serveruser";
$password = "gorgonzola7!";
$dbname = "serverside";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if a sorting parameter is provided in the URL, default to sorting by MovieID if not set
$sortColumn = isset($_GET['sort']) ? $_GET['sort'] : 'MovieID';

// Define sorting order (ASC or DESC), default to ascending order
$sortOrder = isset($_GET['order']) ? $_GET['order'] : 'ASC';

// Get the selected sorting options from the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sortColumn = $_POST['sort'];
    $sortOrder = $_POST['order'];
}

// Modify the SQL query to include genre information
$sql = "SELECT movie.*, GROUP_CONCAT(genre.name SEPARATOR ', ') AS genre_list
        FROM movie
        LEFT JOIN movie_genre ON movie.MovieID = movie_genre.movie_id
        LEFT JOIN genre ON movie_genre.genre_id = genre.genre_id
        GROUP BY movie.MovieID
        ORDER BY $sortColumn $sortOrder";

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css"> <!-- Include your CSS file for styling -->
    <title>Welcome to Movie CMS!</title>
</head>

<body>
    <!-- Movie CMS header -->
    <div class="movie-cms-box">
        <h1>Welcome to Movie CMS</h1>
        <a href="index.php" class="nav-link">Home</a>
        <div class="sorting-options">
            <form method="post" action="">
                <label for="sort">Sort by:</label>
                <select id="sort" name="sort">
                    <option value="MovieID" <?php if ($sortColumn == 'MovieID') echo 'selected'; ?>>Movie ID</option>
                    <option value="Title" <?php if ($sortColumn == 'Title') echo 'selected'; ?>>Title</option>
                    <option value="Release_Date" <?php if ($sortColumn == 'Release_Date') echo 'selected'; ?>>Release Date</option>
                    <option value="Age_Rating" <?php if ($sortColumn == 'Age_Rating') echo 'selected'; ?>>Age Rating</option>
                    <!-- Add more options for other columns if needed -->
                </select>
                <label for="order">Order:</label>
                <select id="order" name="order">
                    <option value="ASC" <?php if ($sortOrder == 'ASC') echo 'selected'; ?>>Ascending</option>
                    <option value="DESC" <?php if ($sortOrder == 'DESC') echo 'selected'; ?>>Descending</option>
                </select>
                <input type="submit" value="Apply">
            </form>
        </div>
        <div class="movie-list">
        <?php
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $movieId = $row["MovieID"];
        $title = $row["Title"];
        $releaseDate = $row["Release_Date"];
        $ageRating = $row["Age_Rating"];
        $description = $row["Description"];
        $language = $row["Language"];
        $runtime = $row["Runtime"];
        $poster = $row["Movie_Poster"];
        $director = $row["Director"];
        $actors = $row["Actors"];
        $genre = $row["genre_list"]; // Update the key to "genre_list"
?>
<div class="movie">
    <h2><a href='show.php?id=<?= $movieId ?>'><?= $title ?></a></h2>
    <p>Release Date: <?= $releaseDate ?></p>
    <p>Age Rating: <?= $ageRating ?></p>
    <p>Description: <?= $description ?></p>
    <p>Language: <?= $language ?></p>
    <p>Runtime: <?= $runtime ?></p>
    <p>Director: <?= $director ?></p>
    <p>Actors: <?= $actors ?></p>
    <p>Genres: <?= $genre ?></p> <!-- Update the key to "genre_list" -->
    <img src="data:image/jpeg;base64,<?= base64_encode($poster) ?>" alt="Movie Poster" width="200">
</div>
<?php
    }
}
?>

               
        
        </div>
    </div>
</body>
</html>