<?php
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_errors.log');

require_once __DIR__ . '/config.php';

session_start();
$error = '';

// ============ DEFINE FUNCTIONS FIRST ============
function loadAboutContent($section_id) {
    $filename = ABOUT_DIR . 'section' . $section_id . '.txt';
    return file_exists($filename) ? file_get_contents($filename) : '';
}

function getNextSectionId($sections) {
    return empty($sections) ? 1 : max(array_keys($sections)) + 1;
}

function saveSections($sections) {
    $file_path = __DIR__ . '/config/sections.php';
    $content = "<?php\n// Section configurations\n\$about_sections = " . var_export($sections, true) . ";\n\n?>";
    
    error_log("Attempting to save to: " . $file_path);
    
    $result = file_put_contents($file_path, $content);
    
    if ($result === false) {
        error_log("FAILED to write to sections.php");
        return false;
    } else {
        error_log("Successfully wrote " . $result . " bytes to sections.php");
        return true;
    }
}

function getSortedSections($sections) {
    $sorted = $sections;
    uasort($sorted, function($a, $b) {
        return $a['order'] <=> $b['order'];
    });
    return $sorted;
}
// ============ END FUNCTIONS ============

// Load sections from config file - or use defaults if not exists
$config_file = __DIR__ . '/config/sections.php';

if (file_exists($config_file)) {
    // Load existing sections
    include_once $config_file;
    // Make sure $about_sections is set
    if (!isset($about_sections) || !is_array($about_sections)) {
        $about_sections = [];
    }
} else {
    // Use defaults if no config file exists
    $about_sections = [
        1 => ['title' => 'Xuất thân và con đường ban đầu', 'active' => true, 'order' => 1],
        2 => ['title' => 'Đời sống tình cảm và hôn nhân', 'active' => true, 'order' => 2],
        3 => ['title' => 'Vai trò người cha', 'active' => true, 'order' => 3],
        4 => ['title' => 'Cách ông nhìn về gia đình', 'active' => true, 'order' => 4],
        5 => ['title' => 'Hình dung chung về con người ông', 'active' => true, 'order' => 5],
        6 => ['title' => 'Phân tích tính cách sâu hơn', 'active' => true, 'order' => 6]
    ];
    // Save defaults to config file
    saveSections($about_sections);
}

// Debug to see what we loaded
error_log("Loaded " . count($about_sections) . " sections from config");

// Handle login
if (isset($_POST['login'])) {
    if ($_POST['password'] === EDIT_PASSWORD) {
        $_SESSION['logged_in'] = true;
    } else {
        $error = 'Incorrect password';
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: edit.php');
    exit;
}

// Handle saving content
if (isset($_POST['save_content']) && isset($_SESSION['logged_in'])) {
    $section_id = $_POST['section_id'];
    $content = $_POST['content'];
    
    $filename = ABOUT_DIR . 'section' . $section_id . '.txt';
    file_put_contents($filename, $content);
    $_SESSION['message'] = 'Content saved successfully!';
    
    header('Location: edit.php');
    exit;
}

// Handle adding new section
if (isset($_POST['add_section']) && isset($_SESSION['logged_in'])) {
    $new_id = getNextSectionId($about_sections);
    
    $about_sections[$new_id] = [
        'title' => $_POST['new_title'],
        'active' => true,
        'order' => count($about_sections) + 1
    ];
    
    if (saveSections($about_sections)) {
        file_put_contents(ABOUT_DIR . 'section' . $new_id . '.txt', '');
        $_SESSION['message'] = '✅ New section added successfully!';
    } else {
        $_SESSION['message'] = '❌ Failed to add section - check permissions';
    }
    
    header('Location: edit.php');
    exit;
}

// Handle updating section settings
if (isset($_POST['update_settings']) && isset($_SESSION['logged_in'])) {
    foreach ($_POST['sections'] as $id => $data) {
        if (isset($about_sections[$id])) {
            $about_sections[$id]['title'] = $data['title'];
            $about_sections[$id]['active'] = isset($data['active']) ? true : false;
            $about_sections[$id]['order'] = (int)$data['order'];
        }
    }
    
    $about_sections = getSortedSections($about_sections);
    
    if (saveSections($about_sections)) {
        $_SESSION['message'] = '✅ Section settings updated!';
    } else {
        $_SESSION['message'] = '❌ Failed to update settings';
    }
    
    header('Location: edit.php');
    exit;
}

// Handle deleting section
if (isset($_GET['delete']) && isset($_SESSION['logged_in'])) {
    $delete_id = $_GET['delete'];
    
    if (isset($about_sections[$delete_id])) {
        $content_file = ABOUT_DIR . 'section' . $delete_id . '.txt';
        if (file_exists($content_file)) unlink($content_file);
        
        unset($about_sections[$delete_id]);
        
        $order = 1;
        foreach ($about_sections as &$section) {
            $section['order'] = $order++;
        }
        
        if (saveSections($about_sections)) {
            $_SESSION['message'] = '✅ Section deleted!';
        } else {
            $_SESSION['message'] = '❌ Failed to delete section';
        }
    }
    
    header('Location: edit.php');
    exit;
}

$isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'content';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit About Dad</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/editstyle.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
    <?php if (!$isLoggedIn): ?>
        <!-- Login Form -->
        <div class="login-form">
            <h2>🔒 Admin Login</h2>
            <?php if ($error): ?><div class="error"><?php echo $error; ?></div><?php endif; ?>
            <form method="POST">
                <input type="password" name="password" placeholder="Enter password" required>
                <button type="submit" name="login">Login</button>
            </form>
        </div>
    <?php else: ?>
        <!-- Admin Interface -->
        <div class="admin-container">
            <div class="admin-header">
                <h1>✏️ About Dad - Admin Panel</h1>
                <p>Manage sections and content</p>
                <a href="?logout=1" class="logout-btn">Logout</a>
            </div>
            
            <div class="admin-nav">
                <a href="?tab=content" class="<?php echo $active_tab == 'content' ? 'active' : ''; ?>">📝 Edit Content</a>
                <a href="?tab=settings" class="<?php echo $active_tab == 'settings' ? 'active' : ''; ?>">⚙️ Section Settings</a>
                <a href="?tab=add" class="<?php echo $active_tab == 'add' ? 'active' : ''; ?>">➕ Add Section</a>
                <a href="?tab=media" class="<?php echo $active_tab == 'media' ? 'active' : ''; ?>">📷 Media Manager</a>
                <a href="index.php" target="_blank">👁️ View Site</a>
            </div>
            
            <div class="admin-content">
                <?php if (isset($_SESSION['message'])): ?>
                    <div class="success"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
                <?php endif; ?>
                
                <!-- Content Tab -->
                <div class="tab-content <?php echo $active_tab == 'content' ? 'active' : ''; ?>">
                    <h2>Edit Section Content</h2>
                    <?php
                    $sorted = getSortedSections($about_sections);
                    foreach ($sorted as $id => $section):
                        if (!$section['active']) continue;
                    ?>
                    <div class="section-card">
                        <div class="section-header">
                            <div>
                                <span class="order-badge"><?php echo $section['order']; ?></span>
                                <span class="section-title"><?php echo htmlspecialchars($section['title']); ?></span>
                            </div>
                        </div>
                        <form method="POST">
                            <textarea name="content" class="content-textarea"><?php echo htmlspecialchars(loadAboutContent($id)); ?></textarea>
                            <input type="hidden" name="section_id" value="<?php echo $id; ?>">
                            <button type="submit" name="save_content" class="save-btn">💾 Save</button>
                        </form>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Settings Tab -->
                <div class="tab-content <?php echo $active_tab == 'settings' ? 'active' : ''; ?>">
                    <h2>Section Settings</h2>
                    <form method="POST">
                        <table class="settings-table">
                            <thead>
                                <tr><th>Order</th><th>Title</th><th>Active</th><th>Actions</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($about_sections as $id => $section): ?>
                                <tr>
                                    <td><input type="number" name="sections[<?php echo $id; ?>][order]" value="<?php echo $section['order']; ?>" min="1"></td>
                                    <td><input type="text" name="sections[<?php echo $id; ?>][title]" value="<?php echo htmlspecialchars($section['title']); ?>" style="width:300px"></td>
                                    <td><input type="checkbox" name="sections[<?php echo $id; ?>][active]" <?php echo $section['active'] ? 'checked' : ''; ?>></td>
                                    <td><a href="?delete=<?php echo $id; ?>" class="delete-btn" onclick="return confirm('Delete this section?')">🗑️ Delete</a></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <button type="submit" name="update_settings" class="save-btn">💾 Update Settings</button>
                    </form>
                </div>
                
                <!-- Add Section Tab -->
                <div class="tab-content <?php echo $active_tab == 'add' ? 'active' : ''; ?>">
                    <div class="add-section-form">
                        <h3>➕ Add New Section</h3>
                        <form method="POST">
                            <div class="form-group">
                                <label>Section Title:</label>
                                <input type="text" name="new_title" required placeholder="Enter section title">
                            </div>
                            <button type="submit" name="add_section" class="save-btn">➕ Add Section</button>
                        </form>
                    </div>
                </div>
                
                <!-- Media Manager Tab -->
                <div class="tab-content <?php echo $active_tab == 'media' ? 'active' : ''; ?>">
                    <div class="media-section">
                        <h2>📷 Media Manager</h2>
                        <p>Upload photos and videos for dad's gallery</p>
                        
                        <?php
                        // Handle file upload
                        $upload_message = '';
                        if (isset($_POST['upload_media']) && isset($_SESSION['logged_in'])) {
                            $target_dir = __DIR__ . '/images/dad/';
                            
                            // Create directory if it doesn't exist
                            if (!file_exists($target_dir)) {
                                mkdir($target_dir, 0777, true);
                            }
                            
                            $file = $_FILES['media_file'];
                            $file_name = basename($file['name']);
                            $target_file = $target_dir . $file_name;
                            $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
                            
                            // Allowed file types
                            $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'mp4', 'webm', 'mov'];
                            
                            if (in_array($file_type, $allowed_types)) {
                                if (move_uploaded_file($file['tmp_name'], $target_file)) {
                                    $upload_message = '<div class="success">✅ File uploaded successfully: ' . htmlspecialchars($file_name) . '</div>';
                                } else {
                                    $upload_message = '<div class="error">❌ Error uploading file</div>';
                                }
                            } else {
                                $upload_message = '<div class="error">❌ Invalid file type. Allowed: ' . implode(', ', $allowed_types) . '</div>';
                            }
                        }
                        
                        // Handle file deletion
                        if (isset($_GET['delete_file']) && isset($_SESSION['logged_in'])) {
                            $file_to_delete = basename($_GET['delete_file']);
                            $file_path = __DIR__ . '/images/dad/' . $file_to_delete;
                            
                            if (file_exists($file_path) && is_file($file_path)) {
                                if (unlink($file_path)) {
                                    $upload_message = '<div class="success">✅ File deleted: ' . htmlspecialchars($file_to_delete) . '</div>';
                                } else {
                                    $upload_message = '<div class="error">❌ Error deleting file</div>';
                                }
                            }
                        }
                        
                        echo $upload_message;
                        ?>
                        
                        <!-- Upload Form -->
                        <div class="upload-form" style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 30px;">
                            <h3>📤 Upload New Media</h3>
                            <form method="POST" enctype="multipart/form-data">
                                <div style="display: flex; gap: 20px; align-items: flex-end; flex-wrap: wrap;">
                                    <div style="flex: 1;">
                                        <label style="display: block; margin-bottom: 5px; font-weight: 600;">Select File:</label>
                                        <input type="file" name="media_file" required style="padding: 10px; border: 2px solid #e0e0e0; border-radius: 5px; width: 100%;">
                                    </div>
                                    <div>
                                        <button type="submit" name="upload_media" class="save-btn" style="background: #1e3c72;">📤 Upload</button>
                                    </div>
                                </div>
                                <p style="margin-top: 10px; color: #666; font-size: 0.9em;">
                                    Allowed: JPG, JPEG, PNG, GIF, MP4, WEBM, MOV
                                </p>
                            </form>
                        </div>
                        
                        <!-- Media Gallery -->
                        <h3>📸 Current Gallery Files</h3>
                        <?php
                        $media_files = glob(__DIR__ . '/images/dad/*.{jpg,jpeg,png,gif,mp4,webm,mov}', GLOB_BRACE);
                        
                        if (empty($media_files)) {
                            echo '<p class="no-media">No media files yet. Upload some photos or videos!</p>';
                        } else {
                            // Separate images and videos for display
                            $images = [];
                            $videos = [];
                            
                            foreach ($media_files as $file) {
                                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                                if (in_array($ext, ['mp4', 'webm', 'mov'])) {
                                    $videos[] = $file;
                                } else {
                                    $images[] = $file;
                                }
                            }
                            
                            // Display images
                            if (!empty($images)) {
                                echo '<h4>📷 Images</h4>';
                                echo '<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 15px; margin-bottom: 30px;">';
                                foreach ($images as $image) {
                                    $file_name = basename($image);
                                    $file_url = 'images/dad/' . $file_name;
                                    echo '<div style="border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden; background: white;">';
                                    echo '<img src="' . $file_url . '" style="width: 100%; height: 120px; object-fit: cover;">';
                                    echo '<div style="padding: 8px;">';
                                    echo '<div style="font-size: 0.8em; overflow: hidden; text-overflow: ellipsis;">' . htmlspecialchars($file_name) . '</div>';
                                    echo '<div style="margin-top: 5px;">';
                                    echo '<a href="?tab=media&delete_file=' . urlencode($file_name) . '" class="delete-btn" style="font-size: 0.8em; padding: 3px 8px;" onclick="return confirm(\'Delete this file?\')">🗑️ Delete</a>';
                                    echo '</div>';
                                    echo '</div>';
                                    echo '</div>';
                                }
                                echo '</div>';
                            }
                            
                            // Display videos
                            if (!empty($videos)) {
                                echo '<h4>🎥 Videos</h4>';
                                echo '<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px;">';
                                foreach ($videos as $video) {
                                    $file_name = basename($video);
                                    $file_url = 'images/dad/' . $file_name;
                                    echo '<div style="border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden; background: white;">';
                                    echo '<video src="' . $file_url . '" style="width: 100%; height: 120px; object-fit: cover;" muted></video>';
                                    echo '<div style="padding: 8px;">';
                                    echo '<div style="font-size: 0.8em; overflow: hidden; text-overflow: ellipsis;">' . htmlspecialchars($file_name) . '</div>';
                                    echo '<div style="margin-top: 5px;">';
                                    echo '<a href="?tab=media&delete_file=' . urlencode($file_name) . '" class="delete-btn" style="font-size: 0.8em; padding: 3px 8px;" onclick="return confirm(\'Delete this file?\')">🗑️ Delete</a>';
                                    echo '</div>';
                                    echo '</div>';
                                    echo '</div>';
                                }
                                echo '</div>';
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</body>
</html>