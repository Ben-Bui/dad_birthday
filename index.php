<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>Happy Birthday Dad!</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>🎂 Happy Birthday Dad! 🎂</h1>
            <p class="subtitle">With love from all of us</p>
        </header>

        <div class="tabs">
            <button class="tab-link active" data-kid="ben">Ben</button>
            <button class="tab-link" data-kid="nhim">Nhím</button>
            <button class="tab-link" data-kid="mint">Mint</button>
            <button class="tab-link" data-kid="aca">Aca</button>
            <button class="tab-link" data-kid="adam">Adam</button>
        </div>

        <div class="content-area">
            <?php
            $kids = ['ben', 'nhim', 'mint', 'aca', 'adam'];
            
            foreach ($kids as $kid) {
                $active_class = ($kid === 'ben') ? 'active' : '';
                $wish_text = file_exists("wishes/{$kid}.txt") 
                    ? file_get_contents("wishes/{$kid}.txt") 
                    : "Happy Birthday Dad! Love, " . ucfirst($kid);
                
                // ALL KIDS NOW USE THE SAME FORMAT - including Ben!
                echo "<div id='tab-{$kid}' class='tab-content {$active_class}'>";
                echo "<div class='kid-card'>";
                echo "<h2>" . ucfirst($kid) . "</h2>";
                
                // Check for image in multiple formats
                $image_formats = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
                $file_found = false;
                
                foreach ($image_formats as $format) {
                    $image_path = "images/{$kid}.{$format}";
                    if (file_exists($image_path)) {
                        $file_found = true;
                        
                        // Handle PDF differently
                        if ($format == 'pdf') {
                            echo "<embed src='{$image_path}' type='application/pdf' width='100%' height='400px' class='kid-art-pdf'>";
                        } else {
                            // It's an image
                            echo "<img src='{$image_path}' alt='Art from {$kid}' class='kid-art'>";
                        }
                        break; // Stop checking once we find a file
                    }
                }
                
                // If no file found, show placeholder
                if (!$file_found) {
                    $initial = strtoupper(substr($kid, 0, 1));
                    echo "<div class='art-placeholder'>{$initial}'s Art</div>";
                }
                
                echo "<p class='wish-text'>{$wish_text}</p>";
                
                echo "</div>";
                echo "</div>";
            }
            ?>
        </div>
        
        <footer>
            <p>Made with love, Ben Bui</p>
        </footer>
    </div>

    <script>
    // Simple tab switching
    document.querySelectorAll('.tab-link').forEach(button => {
        button.addEventListener('click', () => {
            // Remove active class from all tabs and contents
            document.querySelectorAll('.tab-link').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
            
            // Add active class to clicked tab
            button.classList.add('active');
            
            // Show corresponding content
            const kid = button.dataset.kid;
            document.getElementById(`tab-${kid}`).classList.add('active');
        });
    });
    </script>
</body>
</html>