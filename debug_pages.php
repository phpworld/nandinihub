<?php
// Simple debug script to check pages table
$mysqli = new mysqli('localhost', 'root', '', 'nandini');

if ($mysqli->connect_error) {
    die('Connection failed: ' . $mysqli->connect_error);
}

echo "Recent pages in database:\n";
echo "========================\n";

$result = $mysqli->query('SELECT id, title, is_active, show_in_header, show_in_footer, header_order, footer_order FROM pages ORDER BY id DESC LIMIT 5');

if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo "ID: {$row['id']}\n";
        echo "Title: {$row['title']}\n";
        echo "Active: {$row['is_active']}\n";
        echo "Show in Header: {$row['show_in_header']}\n";
        echo "Show in Footer: {$row['show_in_footer']}\n";
        echo "Header Order: {$row['header_order']}\n";
        echo "Footer Order: {$row['footer_order']}\n";
        echo "------------------------\n";
    }
} else {
    echo "Error: " . $mysqli->error . "\n";
}

$mysqli->close();
?>
