<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes, shrink-to-fit=no">
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
            <button class="tab-link" data-kid="aca">Aca và Adam</button>
        </div>

        <div class="content-area">
            <?php
            $kids = ['ben', 'nhim', 'mint', 'aca', 'adam'];
            
            foreach ($kids as $kid) {
                $active_class = ($kid === 'ben') ? 'active' : '';
                $wish_text = file_exists("wishes/{$kid}.txt") 
                    ? file_get_contents("wishes/{$kid}.txt") 
                    : "Happy Birthday Dad! Love, " . ucfirst($kid);
        
                echo "<div id='tab-{$kid}' class='tab-content {$active_class}'>";
                echo "<div class='kid-card'>";
                echo "<h2>" . ucfirst($kid) . "</h2>";
                
                // Check for file in multiple formats
                $image_formats = ['jpg', 'jpeg', 'png', 'gif'];
                $video_formats = ['mp4', 'webm', 'mov', 'avi'];
                $pdf_formats = ['pdf'];
                $file_found = false;
                
                // Check for images first
                foreach ($image_formats as $format) {
                    $file_path = "images/{$kid}.{$format}";
                    if (file_exists($file_path)) {
                        $file_found = true;
                        echo "<img src='{$file_path}' alt='Art from {$kid}' class='kid-art'>";
                        break;
                    }
                }
                
                // If no image, check for videos
                if (!$file_found) {
                    foreach ($video_formats as $format) {
                        $file_path = "images/{$kid}.{$format}";
                        if (file_exists($file_path)) {
                            $file_found = true;
                            echo "<video controls class='kid-video'>";
                            echo "<source src='{$file_path}' type='video/{$format}'>";
                            echo "Your browser doesn't support video playback.";
                            echo "</video>";
                            break;
                        }
                    }
                }
                
                // check for PDF
                if (!$file_found) {
                    foreach ($pdf_formats as $format) {
                        $file_path = "images/{$kid}.{$format}";
                        if (file_exists($file_path)) {
                            $file_found = true;
                            echo "<embed src='{$file_path}' type='application/pdf' width='100%' height='400px' class='kid-art-pdf'>";
                            break;
                        }
                    }
                }
                
                // placeholder
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

        // 1. CONFETTI EXPLOSION when clicking tabs
    document.querySelectorAll('.tab-link').forEach(button => {
        button.addEventListener('click', function(e) {
            // Create confetti
            for (let i = 0; i < 20; i++) {
                createConfetti(e.clientX, e.clientY);
            }
        });
    });

    function createConfetti(x, y) {
        const colors = ['#ffd700', '#ff6b6b', '#1e3c72', '#2a5298', '#ff8c00', '#ff1493'];
        const confetti = document.createElement('div');
        confetti.style.position = 'fixed';
        confetti.style.left = x + 'px';
        confetti.style.top = y + 'px';
        confetti.style.width = '10px';
        confetti.style.height = '10px';
        confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
        confetti.style.borderRadius = '50%';
        confetti.style.pointerEvents = 'none';
        confetti.style.zIndex = '9999';
        confetti.style.animation = `confettiFall ${1 + Math.random()}s linear forwards`;
        document.body.appendChild(confetti);
        
        setTimeout(() => confetti.remove(), 2000);
    }

    // Add keyframe animation for confetti
    const style = document.createElement('style');
    style.textContent = `
        @keyframes confettiFall {
            0% { transform: translate(0, 0) rotate(0deg); opacity: 1; }
            100% { transform: translate(${Math.random() * 200 - 100}px, 100vh) rotate(${Math.random() * 360}deg); opacity: 0; }
        }
    `;
    document.head.appendChild(style);

    // 2. BALLOON POP when clicking images
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('kid-art') || e.target.classList.contains('art-placeholder')) {
            // Pop sound simulation (vibration if mobile)
            if (navigator.vibrate) navigator.vibrate(50);
            
            // Create pop effect
            const pop = document.createElement('div');
            pop.style.position = 'fixed';
            pop.style.left = e.clientX - 30 + 'px';
            pop.style.top = e.clientY - 30 + 'px';
            pop.style.width = '60px';
            pop.style.height = '60px';
            pop.style.borderRadius = '50%';
            pop.style.backgroundColor = 'rgba(255, 215, 0, 0.5)';
            pop.style.transform = 'scale(0)';
            pop.style.animation = 'pop 0.3s ease-out forwards';
            pop.style.pointerEvents = 'none';
            pop.style.zIndex = '9999';
            document.body.appendChild(pop);
            
            setTimeout(() => pop.remove(), 300);
        }
    });

    // Add pop animation
    style.textContent += `
        @keyframes pop {
            0% { transform: scale(0); opacity: 1; }
            100% { transform: scale(3); opacity: 0; }
        }
    `;

    // 3. RANDOM BIRTHDAY FACTS on hover
    const birthdayFacts = [
        "🎂 You're the best dad ever!",
        "🎈 Another year wiser!",
        "🎁 Time to party!",
        "🥳 Happy Birthday!",
        "✨ Make a wish!",
        "🎉 Let's celebrate!",
        "❤️ Loved by all of us!"
    ];

    let factTimeout;
    document.querySelector('.container').addEventListener('mouseenter', () => {
        factTimeout = setTimeout(() => {
            const fact = birthdayFacts[Math.floor(Math.random() * birthdayFacts.length)];
            const tooltip = document.createElement('div');
            tooltip.style.position = 'fixed';
            tooltip.style.top = '20px';
            tooltip.style.right = '20px';
            tooltip.style.backgroundColor = '#ffd700';
            tooltip.style.color = '#1e3c72';
            tooltip.style.padding = '15px 25px';
            tooltip.style.borderRadius = '50px';
            tooltip.style.fontWeight = 'bold';
            tooltip.style.boxShadow = '0 10px 20px rgba(0,0,0,0.2)';
            tooltip.style.animation = 'slideInRight 0.5s ease';
            tooltip.style.zIndex = '10000';
            tooltip.textContent = fact;
            tooltip.id = 'birthdayFact';
            document.body.appendChild(tooltip);
            
            setTimeout(() => {
                const factElement = document.getElementById('birthdayFact');
                if (factElement) factElement.remove();
            }, 3000);
        }, 2000);
    });

    document.querySelector('.container').addEventListener('mouseleave', () => {
        clearTimeout(factTimeout);
    });

    style.textContent += `
        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
    `;


    // 6. SURPRISE EGG (click the footer 3 times)
    let clickCount = 0;
    document.querySelector('footer').addEventListener('click', () => {
        clickCount++;
        if (clickCount === 3) {
            // Easter egg - play happy birthday song simulation
            const notes = ['🎵', '🎶', '🎵', '🎶', '🎵'];
            let i = 0;
            const interval = setInterval(() => {
                const note = document.createElement('div');
                note.textContent = notes[i % notes.length];
                note.style.position = 'fixed';
                note.style.left = '50%';
                note.style.top = '50%';
                note.style.transform = 'translate(-50%, -50%)';
                note.style.fontSize = '100px';
                note.style.opacity = '0.8';
                note.style.animation = 'notePop 0.5s ease forwards';
                note.style.zIndex = '10001';
                document.body.appendChild(note);
                
                setTimeout(() => note.remove(), 500);
                i++;
                
                if (i >= 10) clearInterval(interval);
            }, 200);
            
            // Show secret message
            setTimeout(() => {
                alert('🎉 SURPRISE! You found the secret! Happy Birthday Dad! 🎉');
            }, 2000);
            
            clickCount = 0;
        }
    });

    style.textContent += `
        @keyframes notePop {
            0% { transform: translate(-50%, -50%) scale(0); opacity: 0; }
            70% { transform: translate(-50%, -50%) scale(1.2); opacity: 1; }
            100% { transform: translate(-50%, -50%) scale(1); opacity: 0; }
        }
    `;

    // 7. MAGIC CURSOR TRAIL
    document.addEventListener('mousemove', (e) => {
        if (Math.random() > 0.9) { // Only create trail sometimes
            const trail = document.createElement('div');
            trail.textContent = ['✨', '⭐', '🌟'][Math.floor(Math.random() * 3)];
            trail.style.position = 'fixed';
            trail.style.left = e.clientX + 'px';
            trail.style.top = e.clientY + 'px';
            trail.style.fontSize = '20px';
            trail.style.pointerEvents = 'none';
            trail.style.animation = 'fadeOut 0.5s forwards';
            trail.style.zIndex = '9997';
            document.body.appendChild(trail);
            
            setTimeout(() => trail.remove(), 500);
        }
    });

    style.textContent += `
        @keyframes fadeOut {
            0% { opacity: 1; transform: scale(1); }
            100% { opacity: 0; transform: scale(2); }
        }
    `;

    // 8. SHAKE EFFECT ON MOBILE
    if (window.DeviceMotionEvent) {
        window.addEventListener('devicemotion', (event) => {
            const acceleration = event.accelerationIncludingGravity;
            if (acceleration && (Math.abs(acceleration.x) > 15 || Math.abs(acceleration.y) > 15)) {
                // Phone shaken - trigger confetti
                for (let i = 0; i < 30; i++) {
                    createConfetti(
                        Math.random() * window.innerWidth,
                        Math.random() * window.innerHeight
                    );
                }
            }
        });
    }

    // 10. BIRTHDAY CANDLES
    function addCandles() {
        const candles = document.createElement('div');
        candles.style.position = 'absolute';
        candles.style.bottom = '10px';
        candles.style.left = '0';
        candles.style.right = '0';
        candles.style.display = 'flex';
        candles.style.justifyContent = 'center';
        candles.style.gap = '10px';
        candles.style.pointerEvents = 'none';
        
        for (let i = 0; i < 5; i++) {
            const candle = document.createElement('div');
            candle.innerHTML = '🕯️';
            candle.style.fontSize = '30px';
            candle.style.animation = `flicker ${0.5 + Math.random()}s infinite alternate`;
            candles.appendChild(candle);
        }
        
        document.querySelector('.container').appendChild(candles);
    }

    style.textContent += `
        @keyframes flicker {
            0% { opacity: 0.8; transform: scale(1); }
            100% { opacity: 1; transform: scale(1.1); text-shadow: 0 0 10px #ffd700; }
        }
    `;

    addCandles();

    </script>
</body>
</html>