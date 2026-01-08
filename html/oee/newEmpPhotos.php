<?php
// Directory containing the photos (relative or absolute path)
$directory = "pics/TeamMemberPictures"; // Location of employee photos

// Image file extensions
$extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

// Scan $directory for employee image files
$files = array_filter(scandir($directory), function($file) use ($directory, $extensions) {
    $path = "$directory/$file";
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    return is_file($path) && in_array($ext, $extensions);
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

    color: #0066ff; /* TCI blue */
    text-shadow:
        -2px -2px 0 #fff,
         2px -2px 0 #fff,
        -2px  2px 0 #fff,
         2px  2px 0 #fff,
         0  6px 14px rgba(0,0,0,0.4);
	}
    .gallery {
        display: grid;
        grid-template-columns: repeat(10, 1fr); /* 10 columns */
        gap: 8px;
        justify-items: center;
    }
    .gallery-item {
    width: 132px;
    padding: 6px;
    background: linear-gradient(145deg, #1a1a1a, #000);
    border-radius: 12px;
    box-shadow: 0 8px 18px rgba(0,0,0,0.5);
	}
    .gallery img {
    width: 120px;
    height: 200px;
    object-fit: cover;
    border: 1px solid #ccc;
    background: #0066ff;  /*Interior frame color */
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
    color: #0b4ea2; /* TCI-style blue */
    color: #000000; /* TCI-style blue */
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
    animation: glowPulse 2.5s ease-out;
}

.logo {
    height: 80px;
    filter: drop-shadow(0 0 8px rgba(255,255,255,0.6));
}

/* ===== Title Styling ===== */
h1 {
    margin: 0;
    font-size: 42px;
    font-weight: 800;
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
@keyframes glowPulse {
    0% {
        opacity: 0;
        filter: drop-shadow(0 0 0 rgba(11,78,162,0));
    }
    60% {
        opacity: 1;
        filter: drop-shadow(0 0 20px rgba(11,78,162,0.9));
    }
    100% {
        filter: drop-shadow(0 0 6px rgba(11,78,162,0.6));
    }
}

</style>
</head>
<body>
    <div class="header">
    <img src="../pics/TransCableLogoModified.png" alt="TCI Logo" class="logo">
    <div class="title-block">
        <h1>Meet the TCI Team</h1>
        <div id="datetime"></div>
    </div>
	</div>
    <div class="gallery">
        <?php foreach ($files as $file): 
            $name = pathinfo($file, PATHINFO_FILENAME); // get filename without extension
        ?>
            <div class="gallery-item">
                <strong>
                <img src="<?= htmlspecialchars("$directory/$file") ?>" alt="<?= htmlspecialchars($name) ?>">
                <div class="name"><?= htmlspecialchars($name) ?></div></strong>
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
