<?php
$connection = new SQLite3('releases.db');

// Get 'cat' and 'MemberName' from the URL parameters
$cat = isset($_GET['cat']) ? $_GET['cat'] : '';
$memberName = isset($_GET['MemberName']) ? $_GET['MemberName'] : '';

// Modify the release query to allow filtering by MemberName if provided
$releaseQuery = '
SELECT
    r.Cat,
    a.AliasName AS ArtistName,
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
WHERE 1=1 ';

// If 'cat' is provided, filter by 'Cat'
if (!empty($cat)) {
    $releaseQuery .= ' AND r.Cat = :cat';
}

// If 'MemberName' is provided, filter by 'MemberName'
if (!empty($memberName)) {
    $releaseQuery .= ' AND a.MemberName = :memberName';
}

$releaseQuery .= ' GROUP BY r.Cat';

// Prepare the statement
$statement = $connection->prepare($releaseQuery);

// Bind 'cat' if it's set
if (!empty($cat)) {
    $statement->bindValue(':cat', $cat, SQLITE3_TEXT);
}

// Bind 'MemberName' if it's set
if (!empty($memberName)) {
    $statement->bindValue(':memberName', $memberName, SQLITE3_TEXT);
}

// Execute the query and fetch the results
$releaseResult = $statement->execute()->fetchArray(SQLITE3_ASSOC);

$trackQuery = '
SELECT
    t.TrackNumber,
    t.Title AS TrackTitle,
    t.Duration
FROM Track t
WHERE t.ReleaseCat = :cat
ORDER BY t.DiscNumber, t.TrackNumber';

$trackStatement = $connection->prepare($trackQuery);
$trackStatement->bindValue(':cat', $cat, SQLITE3_TEXT);
$trackResults = $trackStatement->execute();

$totalDurationQuery = '
SELECT SUM(Duration) as TotalDuration
FROM Track
WHERE ReleaseCat = :cat';

$totalDurationStatement = $connection->prepare($totalDurationQuery);
$totalDurationStatement->bindValue(':cat', $cat, SQLITE3_TEXT);
$totalDurationResult = $totalDurationStatement->execute()->fetchArray(SQLITE3_ASSOC);

$totalDurationFormatted = gmdate("H:i:s", $totalDurationResult['TotalDuration']);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($releaseResult['Title']); ?></title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
<header>
    <a href="releases.php" class="linkNavigation">Return</a>
</header>
<main>
    <h1><?php echo $releaseResult['ArtistName'] . ' - ' . $releaseResult['Title']; ?></h1>

    <!-- Original Table Version -->
    <table class="desktop-table">
        <tr>
            <td style="width: 300px; text-align: center;">
                <img src="media/releases/<?php echo htmlspecialchars($releaseResult['Cat']); ?>.jpg" class="titleImage" alt="Cover Art">
            </td>
        </tr>
    </table>

    <table class="desktop-table">
        <tr>
            <?php if (!empty($releaseResult['StreamBC'])): ?>
                <td style="text-align: center;">
                    <a href="<?php echo htmlspecialchars($releaseResult['StreamBC']); ?>" target="_blank">
                        <img class="streaming" src="media/bandcamp.png" alt="Bandcamp">
                    </a>
                </td>
            <?php endif; ?>
            <?php if (!empty($releaseResult['StreamS'])): ?>
                <td style="text-align: center;">
                    <a href="<?php echo htmlspecialchars($releaseResult['StreamS']); ?>" target="_blank">
                        <img class="streaming" src="media/spotify.png" alt="Spotify">
                    </a>
                </td>
            <?php endif; ?>
            <?php if (!empty($releaseResult['StreamAM'])): ?>
                <td style="text-align: center;">
                    <a href="<?php echo htmlspecialchars($releaseResult['StreamAM']); ?>" target="_blank">
                        <img class="streaming" src="media/applemusic.png" alt="Apple Music">
                    </a>
                </td>
            <?php endif; ?>
        </tr>
    </table>

    <table class="desktop-table">
        <tr>
            <?php if (!empty($releaseResult['MetaRYM'])): ?>
                <td style="text-align: center;">
                    <a href="<?php echo htmlspecialchars($releaseResult['MetaRYM']); ?>" target="_blank">
                        <img class="streaming" src="media/rym.png" alt="RateYourMusic">
                    </a>
                </td>
            <?php endif; ?>
            <?php if (!empty($releaseResult['MetaMB'])): ?>
                <td style="text-align: center;">
                    <a href="<?php echo htmlspecialchars($releaseResult['MetaMB']); ?>" target="_blank">
                        <img class="streaming" src="media/mb.png" alt="MusicBrainz">
                    </a>
                </td>
            <?php endif; ?>
        </tr>
    </table>

    <table class="desktop-table">
        <tr>
            <td><?php echo htmlspecialchars($releaseResult['ReleaseDate']); ?></td>
            <td><?php echo htmlspecialchars($releaseResult['ReleaseType']); ?></td>
            <td><?php echo $totalDurationFormatted; ?></td>
            <td><?php echo htmlspecialchars($releaseResult['Cat']); ?></td>
            <td><?php echo htmlspecialchars($releaseResult['ReleaseFormats']); ?></td>
        </tr>
    </table>

    <table class="desktop-table">
        <tr>
            <th>#</th>
            <th>Track</th>
            <th>Duration</th>
            <th>Listen</th>
        </tr>
        <?php while ($track = $trackResults->fetchArray(SQLITE3_ASSOC)): ?>
            <tr>
                <td><?php echo htmlspecialchars($track['TrackNumber']); ?></td>
                <td><?php echo htmlspecialchars($track['TrackTitle']); ?></td>
                <td><?php echo gmdate("i:s", $track['Duration']); ?></td>
                <td>
                    <?php
                    $audioPreview = 'media/previews/' . $cat . '/' . str_pad($track['TrackNumber'], 2, '0', STR_PAD_LEFT) . '.ogg';
                    if (file_exists($audioPreview)): ?>
                        <audio controls>
                            <source src="<?php echo htmlspecialchars($audioPreview); ?>" type="audio/ogg">
                            Your browser does not support the audio element.
                        </audio>
                    <?php else: ?>
                        <p>No Preview Available</p>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

    <!-- Duplicate Table Version with .mobile-table Class -->
    <table class="mobile-table">
        <tr>
            <td style="width: 300px; text-align: center;">
                <img src="media/releases/<?php echo htmlspecialchars($releaseResult['Cat']); ?>.jpg" class="titleImage" alt="Cover Art">
            </td>
        </tr>
    </table>

    <table class="mobile-table">
        <tr>
            <?php if (!empty($releaseResult['StreamBC'])): ?>
                <td style="text-align: center;">
                    <a href="<?php echo htmlspecialchars($releaseResult['StreamBC']); ?>" target="_blank">
                        <img class="streaming" src="media/bandcamp.png" alt="Bandcamp">
                    </a>
                </td>
            <?php endif; ?>
            <?php if (!empty($releaseResult['StreamS'])): ?>
                <td style="text-align: center;">
                    <a href="<?php echo htmlspecialchars($releaseResult['StreamS']); ?>" target="_blank">
                        <img class="streaming" src="media/spotify.png" alt="Spotify">
                    </a>
                </td>
            <?php endif; ?>
            <?php if (!empty($releaseResult['StreamAM'])): ?>
                <td style="text-align: center;">
                    <a href="<?php echo htmlspecialchars($releaseResult['StreamAM']); ?>" target="_blank">
                        <img class="streaming" src="media/applemusic.png" alt="Apple Music">
                    </a>
                </td>
            <?php endif; ?>
        </tr>
    </table>

    <table class="mobile-table">
        <tr>
            <?php if (!empty($releaseResult['MetaRYM'])): ?>
                <td style="text-align: center;">
                    <a href="<?php echo htmlspecialchars($releaseResult['MetaRYM']); ?>" target="_blank">
                        <img class="streaming" src="media/rym.png" alt="RateYourMusic">
                    </a>
                </td>
            <?php endif; ?>
            <?php if (!empty($releaseResult['MetaMB'])): ?>
                <td style="text-align: center;">
                    <a href="<?php echo htmlspecialchars($releaseResult['MetaMB']); ?>" target="_blank">
                        <img class="streaming" src="media/mb.png" alt="MusicBrainz">
                    </a>
                </td>
            <?php endif; ?>
        </tr>
    </table>

    <table class="mobile-table">
        <tr>
            <td><?php echo htmlspecialchars($releaseResult['ReleaseDate']); ?></td>
            <td><?php echo htmlspecialchars($releaseResult['ReleaseType']); ?></td>
            <td><?php echo $totalDurationFormatted; ?></td>
            <td><?php echo htmlspecialchars($releaseResult['Cat']); ?></td>
            <td><?php echo htmlspecialchars($releaseResult['ReleaseFormats']); ?></td>
        </tr>
    </table>

    <table class="mobile-table">
        <tr>
            <th>#</th>
            <th>Track</th>
            <th>Duration</th>
            <th>Listen</th>
        </tr>
        <?php
        // Reset trackResults pointer for the mobile table rendering
        $trackResults->reset();
        while ($track = $trackResults->fetchArray(SQLITE3_ASSOC)): ?>
            <tr>
                <td><?php echo htmlspecialchars($track['TrackNumber']); ?></td>
                <td><?php echo htmlspecialchars($track['TrackTitle']); ?></td>
                <td><?php echo gmdate("i:s", $track['Duration']); ?></td>
                <td>
                    <?php
                    $audioPreview = 'media/previews/' . $cat . '/' . str_pad($track['TrackNumber'], 2, '0', STR_PAD_LEFT) . '.ogg';
                    if (file_exists($audioPreview)): ?>
                        <audio controls>
                            <source src="<?php echo htmlspecialchars($audioPreview); ?>" type="audio/ogg">
                            Your browser does not support the audio element.
                        </audio>
                    <?php else: ?>
                        <p>No Preview Available</p>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

</main>

<footer>
    <p><?php echo date('Y'); ?> HELL INTERFACING</p>
</footer>
</body>
</html>
