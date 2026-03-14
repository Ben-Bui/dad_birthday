<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Happy Birthday Dad!</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
    <div class="site-wrapper">
        <!-- Header -->
        <header class="main-header">
            <div class="container">
                <h1>🎂 HAPPY BIRTHDAY DAD! 🎂</h1>
                <p class="header-subtitle">We hope you have a wonderful birthday</p>
            </div>
        </header>

        <!-- Main Navigation Tabs -->
        <nav class="main-nav">
            <div class="container">
                <ul class="nav-tabs">
                    <li class="nav-tab active" data-tab="about">📖 About Dad</li>
                    <li class="nav-tab" data-tab="wishes">💝 Birthday Wishes</li>
                    <li class="nav-tab" data-tab="gallery">📸 Gallery</li>
                </ul>
            </div>
        </nav>

        <!-- Main Content Area -->
        <main class="main-content">
            <div class="container">
                
            <!-- ABOUT DAD TAB -->
            <div id="tab-about" class="tab-pane active">
                <div class="about-section">
                    <div class="about-header">
                        <h2>About Dad</h2>
                        <div class="age-badge"> Who is he?</div>
                    </div>
                    
                    <div class="about-content">
                        <?php
                        $about_text = file_exists("wishes/about.txt") 
                            ? file_get_contents("wishes/about.txt") 
                            : "A wonderful dad to 5 amazing kids: Ben, Nhím, Mint, Aca, and Adam. This page celebrates his special day!";
                        echo "<p class='about-text'>$about_text</p>";
                        ?>
                    </div>

                    <!-- Collapsible Sections - Dynamic from about folder -->
                    <div class="about-collapsible-container">
                        
                        <?php
                        // Function to load about section content
                        function loadAboutSection($num) {
                            $file = "about/section{$num}.txt";
                            return file_exists($file) ? file_get_contents($file) : '';
                        }
                        
                        // TRY to load from config if it exists, otherwise use defaults
                        $sections = [];
                        
                        // Check if config file exists
                        if (file_exists('config/sections.php')) {
                            include_once 'config/sections.php';
                            if (isset($about_sections) && !empty($about_sections)) {
                                // Use the config sections
                                foreach ($about_sections as $id => $section) {
                                    if ($section['active']) {
                                        $sections[$id] = [
                                            'icon' => isset($section['icon']) ? $section['icon'] : '📝',
                                            'title' => $section['title']
                                        ];
                                    }
                                }
                            }
                        }
                        
                        // If no config sections, use defaults
                        if (empty($sections)) {
                            $sections = [
                                1 => ['icon' => '📜', 'title' => 'Xuất thân và con đường ban đầu'],
                                2 => ['icon' => '❤️', 'title' => 'Đời sống tình cảm và hôn nhân'],
                                3 => ['icon' => '👨‍👧‍👦', 'title' => 'Vai trò người cha'],
                                4 => ['icon' => '🏡', 'title' => 'Cách ông nhìn về gia đình'],
                                5 => ['icon' => '🌟', 'title' => 'Hình dung chung về con người ông'],
                                6 => ['icon' => '🔍', 'title' => 'Phân tích tính cách sâu hơn']
                            ];
                        }
                        
                        $first = true;
                        foreach ($sections as $num => $section):
                            $content = loadAboutSection($num);
                            if (empty($content)) continue; // Skip empty sections
                        ?>
                        
                        <div class="collapsible-section">
                            <button class="collapsible-header <?php echo $first ? 'active' : ''; ?>">
                                <span class="header-icon"><?php echo $section['icon']; ?></span>
                                <span class="header-title"><?php echo htmlspecialchars($section['title']); ?></span>
                                <span class="header-toggle"><?php echo $first ? '−' : '+'; ?></span>
                            </button>
                            <div class="collapsible-content" style="display: <?php echo $first ? 'block' : 'none'; ?>;">
                                <div class="section-content">
                                    <?php echo nl2br(htmlspecialchars($content)); ?>
                                </div>
                            </div>
                        </div>
                        
                        <?php 
                            $first = false;
                        endforeach; 
                        ?>
                    </div>

                    <!-- Dad's Photo Gallery Preview -->
                    <div class="dad-gallery-preview">
                        <h3>Moments with Dad</h3>
                        <div class="gallery-preview-grid">
                            <?php
                            $dad_images = glob("images/dad/*.{jpg,jpeg,png,gif}", GLOB_BRACE);
                            $dad_videos = glob("images/dad/*.{mp4,webm,mov}", GLOB_BRACE);
                            $dad_media = array_merge($dad_images, $dad_videos);
                            $dad_media = array_slice($dad_media, 0, 6);
                            
                            foreach ($dad_media as $media) {
                                $extension = strtolower(pathinfo($media, PATHINFO_EXTENSION));
                                $video_extensions = ['mp4', 'webm', 'mov'];
                                
                                echo "<div class='preview-item'>";
                                if (in_array($extension, $video_extensions)) {
                                    echo "<video src='$media' class='preview-media' muted></video>";
                                    echo "<span class='video-badge'>🎥</span>";
                                } else {
                                    echo "<img src='$media' class='preview-media'>";
                                }
                                echo "</div>";
                            }
                            ?>
                        </div>
                        <p class='gallery-hint'>View all in <span class='gallery-link-trigger' data-tab='gallery'>Gallery →</span></p>
                    </div>
                </div>
            </div>

                <!-- WISHES TAB (with kid sub-tabs) -->
                <div id="tab-wishes" class="tab-pane">
                    <div class="wishes-section">
                        <h2>Birthday Wishes</h2>
                        
                        <!-- Kid Sub-Navigation -->
                        <div class="kid-subnav">
                            <ul class="kid-tabs">
                                <li class="kid-tab active" data-kid="ben">Ben</li>
                                <li class="kid-tab" data-kid="nhim">Nhím</li>
                                <li class="kid-tab" data-kid="mint">Mint</li>
                                <li class="kid-tab" data-kid="aca">Aca & Adam</li>
                            </ul>
                        </div>

                        <!-- Kid Wish Content -->
                        <div class="kid-wish-container">
                            <?php
                            $kids = [
                                'ben' => 'Ben',
                                'nhim' => 'Nhím', 
                                'mint' => 'Mint',
                                'aca' => 'Aca & Adam'
                            ];
                            
                            foreach ($kids as $kid_key => $kid_name) {
                                $active_class = ($kid_key === 'ben') ? 'active-kid' : '';
                                $wish_text = file_exists("wishes/{$kid_key}.txt") 
                                    ? file_get_contents("wishes/{$kid_key}.txt") 
                                    : "Happy Birthday Dad! Love, " . $kid_name;
                                
                                echo "<div id='kid-{$kid_key}' class='kid-wish-content {$active_class}'>";
                                
                                // Check for media in kid's folder
                                $kid_folder = ($kid_key === 'aca') ? 'aca' : $kid_key;
                                $kid_images = glob("images/{$kid_folder}/*.{jpg,jpeg,png,gif}", GLOB_BRACE);
                                $kid_videos = glob("images/{$kid_folder}/*.{mp4,webm,mov}", GLOB_BRACE);
                                
                                // Display first image or video
                                if (!empty($kid_images)) {
                                    echo "<img src='{$kid_images[0]}' alt='{$kid_name}' class='kid-featured-image'>";
                                } elseif (!empty($kid_videos)) {
                                    echo "<video controls class='kid-featured-video'><source src='{$kid_videos[0]}'></video>";
                                } else {
                                    $initial = strtoupper(substr($kid_key, 0, 1));
                                    echo "<div class='kid-placeholder'><span class='initial'>{$initial}</span></div>";
                                }
                                
                                echo "<div class='kid-message'>";
                                echo "<h3>From {$kid_name}</h3>";
                                echo "<p class='wish-message'>{$wish_text}</p>";
                                echo "</div>";
                                
                                // Show additional media if available
                                $all_media = array_merge($kid_images, $kid_videos);
                                if (count($all_media) > 1) {
                                    echo "<div class='kid-media-grid'>";
                                    echo "<h4>More from {$kid_name}</h4>";
                                    echo "<div class='media-thumbnails'>";
                                    foreach (array_slice($all_media, 1, 3) as $media) {
                                        $ext = strtolower(pathinfo($media, PATHINFO_EXTENSION));
                                        if (in_array($ext, ['mp4', 'webm', 'mov'])) {
                                            echo "<video src='$media' class='media-thumb' muted></video>";
                                        } else {
                                            echo "<img src='$media' class='media-thumb'>";
                                        }
                                    }
                                    echo "</div>";
                                    echo "</div>";
                                }
                                
                                echo "</div>";
                            }
                            ?>
                        </div>
                    </div>
                </div>

            <!-- GALLERY TAB (Instagram style) - ONLY Dad's photos -->
            <div id="tab-gallery" class="tab-pane">
                <div class="gallery-section">
                    <h2>Dad's Gallery</h2>
                    <p class="gallery-subtitle">Special moments with dad</p>
                    
                    <div class="gallery-grid">
                        <?php
                        // Get ONLY files from the dad folder
                        $dad_media = glob("images/dad/*.{jpg,jpeg,png,gif,mp4,webm,mov}", GLOB_BRACE);
                        
                        if (!empty($dad_media)) {
                            foreach ($dad_media as $media) {
                                $extension = strtolower(pathinfo($media, PATHINFO_EXTENSION));
                                $video_extensions = ['mp4', 'webm', 'mov'];
                                
                                echo "<div class='gallery-item' data-src='{$media}'>";
                                if (in_array($extension, $video_extensions)) {
                                    echo "<video src='{$media}' class='gallery-media' muted loop></video>";
                                    echo "<span class='media-badge'>🎥</span>";
                                } else {
                                    echo "<img src='{$media}' class='gallery-media'>";
                                }
                                echo "<div class='media-caption'>Dad's memories</div>";
                                echo "</div>";
                            }
                        } else {
                            echo "<p class='no-media'>No gallery images yet. Add photos to the images/dad/ folder.</p>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="site-footer">
            <div class="container">
                <p>Made with infinite love, Ben Bui</p>
                <p class="footer-date"><?php echo date('F j, Y'); ?></p>
                <p style="margin-top: 10px; font-size: 0.9em;">
                    <a href="edit.php" style="color: #ffd700; text-decoration: none;">🔒 Admin</a>
                </p>
            </div>
        </footer>

        <!-- Lightbox Modal for Gallery -->
        <div id="lightbox" class="lightbox">
            <span class="close-lightbox">&times;</span>
            <div class="lightbox-content">
                <div class="lightbox-media-container"></div>
                <div class="lightbox-caption"></div>
            </div>
            <div class="lightbox-nav">
                <button class="lightbox-prev">❮</button>
                <button class="lightbox-next">❯</button>
            </div>
        </div>

        <script>
        // Main tab switching
        document.querySelectorAll('.nav-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                document.querySelectorAll('.nav-tab').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
                
                this.classList.add('active');
                const tabId = this.dataset.tab;
                document.getElementById(`tab-${tabId}`).classList.add('active');
            });
        });

        // Kid sub-tab switching
        document.querySelectorAll('.kid-tab').forEach(tab => {
            tab.addEventListener('click', function() {
                document.querySelectorAll('.kid-tab').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.kid-wish-content').forEach(c => c.classList.remove('active-kid'));
                
                this.classList.add('active');
                const kid = this.dataset.kid;
                document.getElementById(`kid-${kid}`).classList.add('active-kid');
            });
        });

        // Gallery link from About tab
        document.querySelectorAll('.gallery-link-trigger').forEach(link => {
            link.addEventListener('click', function() {
                const targetTab = this.dataset.tab;
                document.querySelector(`.nav-tab[data-tab="${targetTab}"]`).click();
            });
        });

        // Gallery lightbox functionality
        const lightbox = document.getElementById('lightbox');
        const lightboxMedia = document.querySelector('.lightbox-media-container');
        const lightboxCaption = document.querySelector('.lightbox-caption');
        const galleryItems = document.querySelectorAll('.gallery-item');
        let currentIndex = 0;

        galleryItems.forEach((item, index) => {
            item.addEventListener('click', function() {
                currentIndex = index;
                showLightbox(this);
            });
        });

        function showLightbox(item) {
            const src = item.dataset.src;
            const caption = item.querySelector('.media-caption').textContent;
            const isVideo = item.querySelector('video') !== null;
            
            lightboxMedia.innerHTML = '';
            if (isVideo) {
                const video = document.createElement('video');
                video.src = src;
                video.controls = true;
                video.autoplay = true;
                lightboxMedia.appendChild(video);
            } else {
                const img = document.createElement('img');
                img.src = src;
                lightboxMedia.appendChild(img);
            }
            
            lightboxCaption.textContent = caption;
            lightbox.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        document.querySelector('.close-lightbox').addEventListener('click', () => {
            lightbox.classList.remove('active');
            document.body.style.overflow = '';
            lightboxMedia.innerHTML = '';
        });

        document.querySelector('.lightbox-prev').addEventListener('click', () => {
            currentIndex = (currentIndex - 1 + galleryItems.length) % galleryItems.length;
            showLightbox(galleryItems[currentIndex]);
        });

        document.querySelector('.lightbox-next').addEventListener('click', () => {
            currentIndex = (currentIndex + 1) % galleryItems.length;
            showLightbox(galleryItems[currentIndex]);
        });

        // Close lightbox with Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                document.querySelector('.close-lightbox').click();
            } else if (e.key === 'ArrowLeft') {
                document.querySelector('.lightbox-prev').click();
            } else if (e.key === 'ArrowRight') {
                document.querySelector('.lightbox-next').click();
            }
        });

        // Hover play for video thumbnails
        document.querySelectorAll('.preview-media, .media-thumb, .gallery-media').forEach(media => {
            if (media.tagName === 'VIDEO') {
                media.addEventListener('mouseenter', () => media.play());
                media.addEventListener('mouseleave', () => {
                    media.pause();
                    media.currentTime = 0;
                });
            }
        });
        
        // Collapsible sections for About page
        document.querySelectorAll('.collapsible-header').forEach(header => {
            header.addEventListener('click', function() {
                const content = this.nextElementSibling;
                const toggle = this.querySelector('.header-toggle');
                
                if (content.style.display === 'block') {
                    content.style.display = 'none';
                    toggle.textContent = '+';
                    this.classList.remove('active');
                } else {
                    content.style.display = 'block';
                    toggle.textContent = '−';
                    this.classList.add('active');
                }
            });
        });
        </script>
    </div>
</body>
</html>