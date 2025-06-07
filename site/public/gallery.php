<?php

/**
 * Show gallery of images. Upload/delete images.
 * TODO:
 * Split out code into functions. So every function takes care of uploading, deleting, etc. So, it's more functional, easy ot read and scalable. 
 */

use Twig\Error\LoaderError;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__  . '/../includes/config.php';


$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../templates');
$twig = new \Twig\Environment($loader, [
    'cache' =>  false,
]);

$content = '';
$uploadok = 1;

$img = isset($_POST['image']) ? basename($_POST['image']) : null;
$upload_file = isset($_FILES['image_file']) ?? null;

$max_size = 2 * 1024 * 1024; // 2 MB

$img_dir = 'gallery/';
$title = 'Gallery Images';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $img) {
    $path_to_image = $img_dir . $img;

    if (file_exists($path_to_image) && is_file($path_to_image)) {
        unlink($path_to_image);
    } else {
        $uploadok = 0;
        $content = "Something went wrong";
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image_file'])) {

    $upload_dir =  __DIR__ . '/gallery/';
    $file = $_FILES['image_file'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $content = "Error to upload image: " . $file['error'];
        $uploadok = 0;
    }

    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);

    if (!in_array($mimeType, $allowedTypes)) {
        $content = "Can't upload file. Only: JPG, PNG, GIF y WEBP";
    } elseif ($file['size'] > $max_size) {
        $content = "File too big. Máx size allowed: 2 MB.";
    } else {

        $original_name = pathinfo($file['name'], PATHINFO_FILENAME);
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $safe_name = preg_replace('/[^a-zA-Z0-9_-]/', '_', $original_name);
        $new_name = $safe_name . '_' . uniqid() . '.' . $extension;

        $dest = $upload_dir . $new_name;

        if (move_uploaded_file($file['tmp_name'], $dest)) {

            $uploadok = 1;
            $content = "Image upload successfully";
        } else {
            $uploadok = 0;
            $content = "Error uploading file";
        }
    }
}


$images = preg_grep('~\.(jpeg|jpg|png|JPEG|PNG|JPG)$~', scandir($img_dir));

try {
    echo $twig->render(
        'gallery.twig',
        [
            'title' => $title,
            'images' => $images,
            'upload' => $uploadok,
            'content' => $content
        ]
    );
} catch (LoaderError $e) {
    echo 'Error: ', $e;
}
