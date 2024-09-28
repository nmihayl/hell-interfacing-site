<?php
$connection = new SQLite3('releases.db');

// Get the member name from the GET request
$memberName = isset($_GET['MemberName']) ? $_GET['MemberName'] : '';

// Base query to fetch releases including filtering by the member name if provided
$query = '
SELECT
    r.Cat,
    a.AliasName AS ArtistName,  -- Use AliasName for display
    r.Title,
    r.ReleaseDate,
    r.ReleaseType,
    r.CoverArt,
    r.StreamBC,
    r.StreamS,
    r.StreamAM,
    r.MetaRYM,
    r.MetaMB,
    GROUP_CONCAT(f.FormatName, ", ") AS ReleaseFormats
FROM Release r
JOIN Aliases a ON r.AliasID = a.AliasID
LEFT JOIN ReleaseFormats rf ON r.Cat = rf.ReleaseCat
LEFT JOIN Formats f ON rf.FormatID = f.FormatID
';

// If a member name is provided, add the WHERE clause to filter by member name
if (!empty($memberName)) {
    $query .= ' WHERE a.MemberName = :memberName ';
}

// Group and order the results
$query .= ' GROUP BY r.Cat ORDER BY r.ReleaseDate';

// Prepare the query
$statement = $connection->prepare($query);

// Bind the member name parameter if provided
if (!empty($memberName)) {
    $statement->bindValue(':memberName', $memberName, SQLITE3_TEXT);
}

// Execute the query
$results = $statement->execute();

// Store results in an array
$releases = [];
while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
    $releases[] = $row; // Store each row in the array
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Releases | HELL INTERFACING</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon-16x16.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon-32x32.png">
</head>
<body>
<header>
    <a href="index.php" class="linkNavigation">Return</a>
</header>
<main>
    <h1>
        RELEASES
        <img src="media/baller.png" alt="Fixed Image" class="baller">
    </h1>

    <div class="release-table">
        <?php
        if (!empty($memberName)) {
            echo '<h2>Releases by ' . htmlspecialchars($memberName) . '</h2>';
        }

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

        foreach ($releases as $row) {
            echo '<tr>';
            echo '
                    <td><a href="release-details.php?cat=' . $row['Cat'] . '">
                            <img src="media/releases/' . $row['Cat'] . '.jpg" width="200px"><br>
                            <b>' . htmlspecialchars($row['ArtistName']) . '</b> <br> ' . htmlspecialchars($row['Title']) . '
                        </a></td>';
            echo '<td>' . htmlspecialchars($row['ReleaseDate']) . '</td>';
            echo '<td>' . htmlspecialchars($row['ReleaseType']) . '</td>';
            echo '<td>' . htmlspecialchars($row['Cat']) . '</td>';
            echo '<td>' . htmlspecialchars($row['ReleaseFormats']) . '</td>';
            echo '<td class="streaming">';
            // Streaming links (Bandcamp, Spotify, Apple Music)
            if (!empty($row['StreamBC'])) {
                echo '<a href="' . htmlspecialchars($row['StreamBC']) . '" target="_blank"><img class="streaming" src="media/bandcamp.png" width="50px"></a>';
            }
            if (!empty($row['StreamS'])) {
                echo '<a href="' . htmlspecialchars($row['StreamS']) . '" target="_blank"><img class="streaming" src="media/spotify.png" width="50px"></a>';
            }
            if (!empty($row['StreamAM'])) {
                echo '<a href="' . htmlspecialchars($row['StreamAM']) . '" target="_blank"><img class="streaming" src="media/applemusic.png" width="50px"></a>';
            }
            echo '</td>';
            echo '<td>';
            // Meta links (RateYourMusic, MusicBrainz)
            if (!empty($row['MetaRYM'])) {
                echo '<a href="' . htmlspecialchars($row['MetaRYM']) . '" target="_blank"><img class="streaming" src="media/rym.png" width="50px"></a>';
            }
            if (!empty($row['MetaMB'])) {
                echo '<a href="' . htmlspecialchars($row['MetaMB']) . '" target="_blank"><img class="streaming" src="media/mb.png" width="50px"></a>';
            }
            echo '</td>';
            echo '</tr>';
        }
        echo '</table>';

        // Mobile table layout
        echo '<table class="mobile-table">';
        foreach ($releases as $row) {  // Loop through stored results for mobile layout
            echo '<tr>';
            echo '
                    <td><a href="release-details.php?cat=' . $row['Cat'] . '">
                            <img src="media/releases/' . $row['Cat'] . '.jpg" width="200px"><br>
                            <b>' . htmlspecialchars($row['ArtistName']) . '</b> <br> ' . htmlspecialchars($row['Title']) . '
                        </a></td>
                    <td>'
                . htmlspecialchars($row['ReleaseDate']) . '
                     <br>'
                . htmlspecialchars($row['ReleaseType']) . '
                    <br>'
                . htmlspecialchars($row['Cat']) . '
                    <br>'
                . htmlspecialchars($row['ReleaseFormats']) .
                '</td>
                    <td class="streaming">';
            // Check if Bandcamp link exists
            if (!empty($row['StreamBC'])) {
                echo '<a href="' . htmlspecialchars($row['StreamBC']) . '" target="_blank"><img class="streaming" src="media/bandcamp.png" width="50px"></a>';
            }

            // Check if Spotify link exists
            if (!empty($row['StreamS'])) {
                echo '<a href="' . htmlspecialchars($row['StreamS']) . '" target="_blank"><img class="streaming" src="media/spotify.png" width="50px"></a>';
            }

            // Check if Apple Music link exists
            if (!empty($row['StreamAM'])) {
                echo '<a href="' . htmlspecialchars($row['StreamAM']) . '" target="_blank"><img class="streaming" src="media/applemusic.png" width="50px"></a>';
            }

            // Check if RateYourMusic link exists
            if (!empty($row['MetaRYM'])) {
                echo '<a href="' . htmlspecialchars($row['MetaRYM']) . '" target="_blank"><img class="streaming" src="media/rym.png" width="50px"></a>';
            }
            echo '</td>';
            echo '</tr>';
        }
        echo '</table>';
        ?>
    </div>
</main>
<footer>
    <br>
</footer>
</body>
</html>
