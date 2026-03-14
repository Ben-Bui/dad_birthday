
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
    
    // Debug
    error_log("Attempting to save to: " . $file_path);
    
    $result = file_put_contents($file_path, $content);
    
    if ($result === false) {
        error_log("FAILED to write to sections.php");
        $_SESSION['debug_error'] = "Failed to write to config file. Check permissions.";
    } else {
        error_log("Successfully wrote " . $result . " bytes to sections.php");
    }
    
    return $result !== false;
}

function getSortedSections($sections) {
    $sorted = $sections;
    uasort($sorted, function($a, $b) {
        return $a['order'] <=> $b['order'];
    });
    return $sorted;
}
// ============ END FUNCTIONS ============

// Load sections with VISUAL DEBUGGING
$about_sections = [];
$config_file = __DIR__ . '/config/sections.php';

// Create debug array to show on page
$debug_info = [];

// Check if config directory exists
$config_dir = __DIR__ . '/config/';
$debug_info['config_dir_exists'] = file_exists($config_dir) ? 'YES' : 'NO';
$debug_info['config_dir_writable'] = is_writable($config_dir) ? 'YES' : 'NO';

// Check if config file exists
$debug_info['config_file_exists'] = file_exists($config_file) ? 'YES' : 'NO';
if (file_exists($config_file)) {
    $debug_info['config_file_writable'] = is_writable($config_file) ? 'YES' : 'NO';
    $debug_info['config_file_size'] = filesize($config_file) . ' bytes';
    
    // Include the file
    include_once $config_file;
    $debug_info['after_include'] = isset($about_sections) ? 'SET' : 'NOT SET';
    $debug_info['section_count'] = isset($about_sections) ? count($about_sections) : 0;
}

// If no sections, create defaults
if (!isset($about_sections) || !is_array($about_sections) || empty($about_sections)) {
    $debug_info['using_defaults'] = 'YES';
    $about_sections = [
        1 => ['title' => 'Xuất thân và con đường ban đầu', 'active' => true, 'order' => 1],
        2 => ['title' => 'Đời sống tình cảm và hôn nhân', 'active' => true, 'order' => 2],
        3 => ['title' => 'Vai trò người cha', 'active' => true, 'order' => 3],
        4 => ['title' => 'Cách ông nhìn về gia đình', 'active' => true, 'order' => 4],
        5 => ['title' => 'Hình dung chung về con người ông', 'active' => true, 'order' => 5],
        6 => ['title' => 'Phân tích tính cách sâu hơn', 'active' => true, 'order' => 6]
    ];
    
    // Try to save defaults
    $save_result = saveSections($about_sections);
    $debug_info['save_defaults_result'] = $save_result ? 'SUCCESS' : 'FAILED';
}

$debug_info['final_section_count'] = count($about_sections);

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
        $_SESSION['message'] = 'New section added successfully!';
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
        $_SESSION['message'] = 'Section settings updated!';
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
            $_SESSION['message'] = 'Section deleted!';
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
    <!-- DEBUG INFO - Remove after fixing -->
    <div style="background: #f0f0f0; border: 2px solid red; padding: 20px; margin: 20px; font-family: monospace;">
        <h3 style="color: red;">🔍 DEBUG INFORMATION</h3>
        <table border="1" cellpadding="5">
            <?php foreach ($debug_info as $key => $value): ?>
            <tr>
                <td><strong><?php echo $key; ?></strong></td>
                <td><?php echo $value; ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
        
        <h4>Current Sections in Memory:</h4>
        <pre><?php print_r($about_sections); ?></pre>
        
        <h4>Content Files in /about/ folder:</h4>
        <pre><?php
        $about_files = glob(ABOUT_DIR . '*.txt');
        print_r($about_files);
        ?></pre>
    </div>
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
            </div>
        </div>
    <?php endif; ?>
</body>
</html>