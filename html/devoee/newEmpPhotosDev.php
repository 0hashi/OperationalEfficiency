<?php
# Paul Ohashi
# Trans Cable International
# Script: newEmpPhotosDev.php
# 
# This script displays employee photos on the monitor in the breakroom, and runs on tci-bt-it04
#
$directory = "pics/TeamMemberPictures/Old Photos";

$extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

$rawFiles = array_filter(scandir($directory), function ($file) use ($directory, $extensions) {
    $path = "$directory/$file";
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    return is_file($path) && in_array($ext, $extensions);
});

$files = [];

foreach ($rawFiles as $file) {
    $filename = pathinfo($file, PATHINFO_FILENAME);

    // Match: "Firstname Lastname 12"
    if (preg_match('/^(.*?)(?:\s+(\d+))$/', $filename, $matches)) {
        $name = trim($matches[1]);
        $id   = (int)$matches[2];
    } else {
        $name = $filename;
        $id   = null;
    }

    $files[] = [
        'file' => $file,
        'name' => $name,
        'id'   => $id
    ];
}

// Custom sort: ID first (numeric), then names
usort($files, function ($a, $b) {
    if ($a['id'] !== null && $b['id'] !== null) {
        return $a['id'] <=> $b['id'];
    }
    if ($a['id'] !== null) return -1;
    if ($b['id'] !== null) return 1;
    return strcasecmp($a['name'], $b['name']);
});
?>



<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="refresh" content="60">
<title>TCI Employees</title>
<style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 20px;

        /* 🔹 Full-page background image settings */
        background-image: url('pics/PlantPanoramic.jpg'); /* change path as needed */
        background-size: cover;          /* fills entire window */
        background-repeat: no-repeat;    /* prevents tiling */
        background-position: center;     /* centers image */
        background-attachment: fixed;    /* stays static when scrolling */

        color: #222;
    }
    h1 {
    text-align: center;
    margin-bottom: 24px;
    font-size: 42px;
    font-weight: 800;
    letter-spacing: 1px;

    color: #0066ff; /* PO: TCI blue */
    text-shadow:
        -2px -2px 0 #fff,
         2px -2px 0 #fff,
        -2px  2px 0 #fff,
         2px  2px 0 #fff,
         0  6px 14px rgba(0,0,0,0.4);
	}
    .gallery {
        display: grid;
        grid-template-columns: repeat(12, 1fr); /* PO: 10 columns */
        gap: 8px;
        justify-items: center;
    }
    .gallery-item {
    width: 70px; /* PO: Width of the photo frame. Should be greater than ".gallery img -> Width" */
    padding: 16px; /* PO: Padding for left, right, top and bottom of frame around the photo */
    background: linear-gradient(145deg, #1a1a1a, #0066ff);
    border-radius: 12px;
    box-shadow: 0 8px 18px rgba(0,0,0,0.5);
	}
    .gallery img {
    width: 60px; /* PO: Width of the photo */
    height: 100px; /* PO: Height of the photo */
    object-fit: cover;
    border: 1px solid #ccc;
    background: #0066ff;  /* PO: Interior frame color */
    padding: 4px;
	}
    .gallery img:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    }
    .name {
    margin-top: 6px;
    font-size: 14px;
    font-weight: 600;
    /* color: #0b4ea2; PO: TCI-style blue */
    color: #000000; /* PO: TCI-style black */
    text-align: center;
    text-shadow:
        -1px -1px 0 #fff,
         1px -1px 0 #fff,
        -1px  1px 0 #fff,
         1px  1px 0 #fff;
    word-break: break-word;
	}
    @media (max-width: 1200px) {
        .gallery { grid-template-columns: repeat(5, 1fr); }
    }
    @media (max-width: 768px) {
        .gallery { grid-template-columns: repeat(3, 1fr); }
    }
    @media (max-width: 480px) {
        .gallery { grid-template-columns: repeat(2, 1fr); }
    }
	
	
	
	/* ===== Header Lockup ===== */
.header {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 24px;
    margin-bottom: 30px;
    animation: glowPulse 2.5s ease-out;  /* Glow fades out after 2.5 seconds */
	animation: glowPulse 4s ease-in-out infinite; /* Over-rides the line above */
}

.logo {
    height: 80px;
    border-radius: 12px;              /* rounds corners */
    overflow: hidden;                 /* clips image corners */
    background: #ffffff;              /* optional: clean edge */
    padding: 6px;                     /* optional frame */
    filter: drop-shadow(0 0 8px rgba(255,255,255,0.6));
}

/* ===== Title Styling ===== */
h1 {
    margin: 0;
    font-size: 42px; /* PO: Font size for whatever text is between <h1></h1> */
    font-weight: 800; /* PO: Font thickness */
    letter-spacing: 1px;
    color: #0b4ea2;

    text-shadow:
        -2px -2px 0 #fff,
         2px -2px 0 #fff,
        -2px  2px 0 #fff,
         2px  2px 0 #fff,
         0  6px 14px rgba(0,0,0,0.45);
}

/* ===== Date / Time Banner ===== */
#datetime {
    margin-top: 6px;
    font-size: 16px;
    font-weight: 600;
    text-align: center;
    color: #ffffff;

    text-shadow:
        0 0 4px rgba(11,78,162,0.9),
        0 0 10px rgba(11,78,162,0.6);
}

/* ===== Animated Glow on Refresh ===== */

@keyframes glowPulse { /* Over-rides the block above and keeps the glow persistent. Comment entire block fade out the glow */
    0%, 100% {
        filter: drop-shadow(0 0 6px rgba(11,78,162,0.6));
    }
    50% {
        filter: drop-shadow(0 0 18px rgba(11,78,162,0.95));
    }
}

</style>
</head>
<body>
    <div class="header">
    <img src="../pics/TransCableLogo.png" alt="TCI Logo" class="logo">
    <div class="title-block">
        <h1>Meet the TCI Team</h1>
        <div id="datetime"></div>
    </div>
	</div>
    <div class="gallery">
        <?php foreach ($files as $entry): ?>
            <div class="gallery-item">
                <strong>
                <img src="<?= htmlspecialchars("$directory/{$entry['file']}") ?>" alt="<?= htmlspecialchars($entry['name']) ?>">
				<div class="name"><?= htmlspecialchars($entry['name']) ?></div></strong>
            </div>
        <?php endforeach; ?>
    </div>
	
	
	<script>
function updateDateTime() {
    const now = new Date();
    const options = {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    };
    document.getElementById('datetime').textContent =
        now.toLocaleDateString(undefined, options);
}

updateDateTime();
setInterval(updateDateTime, 60000); // update every minute
</script>

</body>
</html>
