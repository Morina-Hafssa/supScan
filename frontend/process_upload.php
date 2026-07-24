<?php
session_start();

// Check if file was uploaded
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['document'])) {
    $file = $_FILES['document'];

    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        header("Location: page1.php?error=" . urlencode("Erreur lors du téléchargement du fichier"));
        exit();
    }

    // Get file information
    $fileName = $file['name'];
    $fileSize = $file['size'];
    $fileTmpPath = $file['tmp_name'];
    $fileType = $file['type'];

    // Validate file size (max 10MB)
    if ($fileSize > 10 * 1024 * 1024) {
        header("Location: page1.php?error=" . urlencode("Le fichier est trop volumineux (max 10MB)"));
        exit();
    }

    // Validate file extension
    $allowedExtensions = ['pdf', 'doc', 'docx', 'txt', 'xls', 'xlsx', 'jpg', 'jpeg', 'png'];
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    if (!in_array($fileExtension, $allowedExtensions)) {
        header("Location: page1.php?error=" . urlencode("Type de fichier non autorisé"));
        exit();
    }

    // Create upload directory if it doesn't exist
    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Generate unique filename to avoid conflicts
    $uniqueFileName = uniqid() . '_' . $fileName;
    $destination = $uploadDir . $uniqueFileName;

    // Move uploaded file to destination
    if (move_uploaded_file($fileTmpPath, $destination)) {
        // File uploaded successfully - store in session for processing page
        $_SESSION['file_info'] = [
            'name' => $fileName,
            'size' => number_format($fileSize / 1048576, 2) . ' MB',
            'type' => $fileType,
            'path' => $destination,
            'extension' => $fileExtension
        ];

        // Redirect to processing page with animation
        header("Location: process_animation.php");
        exit();
    } else {
        header("Location: page1.php?error=" . urlencode("Erreur lors du déplacement du fichier"));
        exit();
    }
} else {
    // No file uploaded
    header("Location: page1.php");
    exit();
}
?>
