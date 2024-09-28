<?php
$connection = new SQLite3('releases.db');
$releaseCountQuery = 'SELECT COUNT(*) AS release_count FROM Release';
$releaseCountResult = $connection->querySingle($releaseCountQuery);
$trackCountQuery = 'SELECT COUNT(*) AS track_count FROM Track';
$trackCountResult = $connection->querySingle($trackCountQuery);
$totalDurationQuery = 'SELECT SUM(Duration) AS total_duration FROM Track';
$totalDurationResult = $connection->querySingle($totalDurationQuery);
$totalDuration = gmdate("H:i:s", $totalDurationResult);
?>

<!doctype html>
<html lang="en">
<head>
    <title>HELL INTERFACING</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon-16x16.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon-32x32.png">
</head>
<body>
<main>
    <h1>HELL INTERFACING</h1>
    <div class="titleNav">
        <nav class="navbar">
            <a class="linkNavigation" href="releases.php">Releases</a>
            <a class="linkNavigation" href="members.html">Members</a>
            <a class="linkNavigation" href="about.html">About</a>
            <a class="linkNavigation" href="contact.html">Contact</a>
        </nav>
    </div>

    <div class="title-image">
        <a href="https://hellinterfacing.bandcamp.com/" target="_blank">
            <img class="titleImage" src="media/hi.png" alt="Logo">
        </a>
    </div>

    <div class="statistics">
        <h2>Currently a home to...</h2>
        <h3><?php echo htmlspecialchars($releaseCountResult); ?> releases</h3>
        <h3><?php echo htmlspecialchars($trackCountResult); ?> tracks</h3>
        <h3><?php echo htmlspecialchars($totalDuration); ?> worth of music</h3>
    </div>
</main>
<footer>
    <em><p>This site is proudly JavaScript free!</p></em>
    <em><p>hell i/o, 2024.</p></em>
</footer>
</body>
</html>
