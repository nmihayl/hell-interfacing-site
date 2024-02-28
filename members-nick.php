<?php
$connection = new SQLite3('releases.db');
$results = $connection->query('SELECT * FROM Label WHERE Member = "Nick"');
?>

<!DOCTYPE html>
<html>
<head>
    <title>Releases - HELL INTERFACING</title>
    <link rel="stylesheet" type="text/css" href="css/releasestyle.css">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon-16x16.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon-32x32.png">
</head>
<body>
<header>
</header>
<main>
    <h1>
    <img src="favicon-32x32.png" alt="logo" class="logo">
    RELEASES</h1>
    <div class="release-table">
        <?php
        echo '<table>';
        echo '
        <tr>
        <td>Cover Art</td>
        <td>Artist</td>
        <td>Title</td>
        <td>Release Date</td>
        <td>Type</td>
        <td>Catalog Number</td>
        <td>Format</td>
        <td>Streaming</td>
        <td>RateYourMusic</td>
        </tr>';
        while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
            echo '<tr>';
            echo '
                <td><img src="media/releases/' . $row['Cat'] . '.jpg" width="200px"></td>
                <td>' . $row['Alias'] . '</td>
                <td>' . $row['ReleaseName'] . '</td>
                <td>' . $row['ReleaseDate'] . '</td>
                <td>' . $row['ReleaseType'] . '</td>
                <td>' . $row['Cat'] . '</td>
                <td>' . $row['ReleaseFormat'] . '</td>
                <td class="streaming">';
            
                // Check if Bandcamp link exists
                if (!empty($row['StreamBC'])) {
                    echo '<a href="' . $row['StreamBC'] . '" target="_blank"><img src="media/bandcamp.png" width="50px"></a>';
                }
                // Check if Spotify link exists
                if (!empty($row['StreamS'])) {
                    echo '<a href="' . $row['StreamS'] . '" target="_blank"><img src="media/spotify.png" width="50px"></a>';
                }
                // Check if Apple Music link exists
                if (!empty($row['StreamAM'])) {
                    echo '<a href="' . $row['StreamAM'] . '" target="_blank"><img src="media/applemusic.png" width="50px"></a>';
                }
                echo '</td>
                    <th><a href="' . $row['RYM'] . '" target="_blank"><img src="media/rym.png" width="50px"></a></th>
                ';
                echo '</tr>';
            }
            echo '</table>';
            ?>
    </div>
</main>
    <footer>
        <br>
        <a class="sticky-button" href="members.html">Return to members</a>
        <br>
        <img src="media/baller.png" alt="Fixed Image" class="baller">
        <br>
    </footer>
</body>
</html>