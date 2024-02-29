<?php
$connection = new SQLite3('releases.db');
$results = $connection->query('SELECT * FROM Label');
?>

<!DOCTYPE html>
<html>
<head>
    <title>Releases - HELL INTERFACING</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name=viewport content="width=device-width, initial-scale=1, user-scalable=yes">
</head>
<body>
    <header>
    </header>
    <main>
        <h1>
        <a href="index.html"><img src="media/placeholder.png" href="index.html" alt="logo" class="logo"></a>
            RELEASES
            <img src="media/baller.png" alt="Fixed Image" class="baller">
        </h1>
        <div class="release-table">
            <?php
            //desktop table layout
            echo '<table class="desktop-table">';
            echo '
            <tr>
            <td>Release</td>
            <td>Date</td>
            <td>Type</td>
            <td>Catalog Number</td>
            <td>Format</td>
            <td>Stream</td>
            <td>Meta</td>
            </tr>';
            while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
                echo '<tr>';
                echo '
                    <td><img src="media/releases/' . $row['Cat'] . '.jpg" width="200px"><br><b> ' . $row['Alias'] . ' </b> <br> ' . $row['ReleaseName'] . ' </td>
                    <td>' . $row['ReleaseDate'] . '</td>
                    <td>' . $row['ReleaseType'] . '</td>
                    <td>' . $row['Cat'] . '</td>
                    <td>' . $row['ReleaseFormat'] . '</td>
                    <td class="streaming">';
                        // Check if Bandcamp link exists
                        if (!empty($row['StreamBC'])) {
                            echo '<a href="' . $row['StreamBC'] . '" target="_blank"><img class="streaming" src="media/bandcamp.png" width="50px"></a>';
                        }
                    
                        // Check if Spotify link exists
                        if (!empty($row['StreamS'])) {
                            echo '<a href="' . $row['StreamS'] . '" target="_blank"><img class="streaming" src="media/spotify.png" width="50px"></a>';
                        }
                    
                        // Check if Apple Music link exists
                        if (!empty($row['StreamAM'])) {
                            echo '<a href="' . $row['StreamAM'] . '" target="_blank"><img class="streaming" src="media/applemusic.png" width="50px"></a>';
                        }

                    
                        echo '</td>
                            <th><a href="' . $row['RYM'] . '" target="_blank"><img class="streaming" src="media/rym.png" width="50px"></a></th>
                        ';
                        echo '</tr>';
                    }
                        echo '</table>';


            //mobile table layout
            echo '<table class="mobile-table">';
            echo '
            <tr>
            </tr>';
            while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
                echo '<tr>';
                echo '
                    <td><img src="media/releases/' . $row['Cat'] . '.jpg" width="200px"> <b> ' . $row['Alias'] . ' </b> <br> ' . $row['ReleaseName'] . ' </td>
                    <td>'
                     . $row['ReleaseDate'] . '
                     <br>'
                      . $row['ReleaseType'] . '
                    <br>'
                    . $row['Cat'] . '
                    <br>'
                    . $row['ReleaseFormat'] . 
                    '</td>
                    <td class="streaming">';
                        // Check if Bandcamp link exists
                        if (!empty($row['StreamBC'])) {
                            echo '<a href="' . $row['StreamBC'] . '" target="_blank"><img class="streaming" src="media/bandcamp.png" width="50px"></a>';
                        }
                    
                        // Check if Spotify link exists
                        if (!empty($row['StreamS'])) {
                            echo '<a href="' . $row['StreamS'] . '" target="_blank"><img class="streaming" src="media/spotify.png" width="50px"></a>';
                        }
                    
                        // Check if Apple Music link exists
                        if (!empty($row['StreamAM'])) {
                            echo '<a href="' . $row['StreamAM'] . '" target="_blank"><img class="streaming" src="media/applemusic.png" width="50px"></a>';
                        }

                        // Check if RateYourMusic link exists
                        if (!empty($row['RYM'])) {
                            echo '<a href="' . $row['RYM'] . '" target="_blank"><img class="streaming" src="media/rym.png" width="50px"></a>';
                        }
                        echo '</tr>';
                    }
                        echo '</table>';
                        ?>
        </div>
</main>
<footer>
</footer>
</body>
</html>