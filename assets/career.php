<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input
    $name = htmlspecialchars(trim($_POST["name"]));
    $mobile = htmlspecialchars(trim($_POST["mobile"]));
    $email = htmlspecialchars(trim($_POST["email"]));
    $designation = htmlspecialchars(trim($_POST["designation"]));

    // Check and upload file
    if (isset($_FILES["file"]) && $_FILES["file"]["error"] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES["file"]["tmp_name"];
        $fileName = $_FILES["file"]["name"];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExtensions = ['pdf', 'doc', 'docx'];

        if (in_array($fileExtension, $allowedExtensions)) {
            $uploadDir = './uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $newFileName = time() . '_' . preg_replace('/\s+/', '_', $fileName);
            $destPath = $uploadDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $destPath)) {
                // Prepare email
                $to = "mdhltechnologies@gmail.com";
                $subject = "New Career Application";
                $message = "You have received a new application:\n\n" .
                           "Name: $name\n" .
                           "Mobile: $mobile\n" .
                           "Email: $email\n" .
                           "Designation: $designation\n" .
                           "CV File: $newFileName\n" .
                           "Location: " . $_SERVER['HTTP_REFERER'];
                $headers = "From: noreply@yourdomain.com"; // Change this!

                // Send email
                if (mail($to, $subject, $message, $headers)) {
                    echo "<h3>Application sent successfully!</h3>";
                } else {
                    echo "<p>Mail sending failed.</p>";
                }
            } else {
                echo "<p>Error uploading CV.</p>";
            }
        } else {
            echo "<p>Invalid file format. Only PDF, DOC, DOCX allowed.</p>";
        }
    } else {
        echo "<p>Please attach your CV.</p>";
    }
} else {
    echo "<p>Invalid request.</p>";
}
?>