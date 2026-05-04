<?php include 'db.php'; ?>

<form method="POST" enctype="multipart/form-data">
    Title: <input type="text" name="title" required><br>
    Description: <textarea name="description" required></textarea><br>
    Image: <input type="file" name="image" required><br>
    <button type="submit" name="submit">Add Slide</button>
</form>

<?php
if (isset($_POST['submit'])) {

    $title = trim($_POST['title']);
    $desc  = trim($_POST['description']);

    // ================= VALIDATION =================
    $errors = [];

    // Title validation
    if (empty($title)) {
        $errors[] = "Title is required";
    }

    // Description validation
    if (empty($desc)) {
        $errors[] = "Description is required";
    }

    // Image validation
    if (empty($_FILES['image']['name'])) {
        $errors[] = "Image is required";
    } else {
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        $fileName = $_FILES['image']['name'];
        $fileTmp  = $_FILES['image']['tmp_name'];

        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedTypes)) {
            $errors[] = "Only JPG, PNG, GIF allowed";
        }
    }

    // ================= SHOW ERRORS =================
    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<p style='color:red;'>$error</p>";
        }
    } else {

        // unique filename (important)
        $newName = time() . "_" . $fileName;

        move_uploaded_file($fileTmp, "uploads/" . $newName);

        // ================= SAFE QUERY =================
        $stmt = $conn->prepare("INSERT INTO slides (title, description, image) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $title, $desc, $newName);
        $stmt->execute();

        echo "<p style='color:green;'>Slide Added Successfully!</p>";
    }
}
?>