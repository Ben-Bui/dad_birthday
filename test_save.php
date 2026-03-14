<?php
echo "<h1>Simple Save Test</h1>";

$config_file = __DIR__ . '/config/sections.php';
$config_dir = __DIR__ . '/config/';

echo "<h2>Checking Paths</h2>";
echo "Config dir: $config_dir<br>";
echo "Config file: $config_file<br>";

echo "<h2>Permissions</h2>";
echo "Config dir exists: " . (file_exists($config_dir) ? 'YES' : 'NO') . "<br>";
if (file_exists($config_dir)) {
    echo "Config dir writable: " . (is_writable($config_dir) ? 'YES' : 'NO') . "<br>";
}

echo "<h2>Write Test</h2>";
$test_data = ['test' => 'This is a test', 'time' => time()];
$content = "<?php\n\$test_array = " . var_export($test_data, true) . ";\n?>";

$result = file_put_contents($config_dir . 'test_write.php', $content);
if ($result !== false) {
    echo "✅ SUCCESS: Wrote " . $result . " bytes to test_write.php<br>";
    echo "File exists: " . (file_exists($config_dir . 'test_write.php') ? 'YES' : 'NO') . "<br>";
    
    // Try to read it back
    include $config_dir . 'test_write.php';
    echo "Read back test_array: ";
    print_r($test_array);
    
    // Clean up
    unlink($config_dir . 'test_write.php');
    echo "<br>✅ Test file deleted";
} else {
    echo "❌ FAILED: Could not write to file<br>";
    echo "Error: " . error_get_last()['message'];
}
?>