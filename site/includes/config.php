<?php
# Database configuration
$servername = "db";
$db = 'server_db';
$username = "root";
$password = "admin";
$charset = 'utf8mb4';
$dsn = "mysql:host=$servername;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_EMULATE_PREPARES   => false, // Disable emulation mode for "real" prepared statements
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Disable errors in the form of exceptions
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Make the default fetch be an associative array
];

try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (Exception $e) {
    echo "Can't connect to Database: " . $e;
}


class readFeed
{
    private string $url;

    public function __construct(string $url)
    {
        $this->url = $url;
        libxml_use_internal_errors(false);
    }
    public function getFeed(): SimpleXMLElement|string
    {
        $xml = simplexml_load_file($this->url);
        return $xml ?: "Can't load feeds at this moment";
    }
}
