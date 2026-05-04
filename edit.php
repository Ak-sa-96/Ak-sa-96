<?php include 'db.php';

// CHECK ID
if(!isset($_GET['id']) || empty($_GET['id'])){
    die("Invalid ID");
}

$id = intval($_GET['id']);

// FETCH DATA
$result = $conn->query("SELECT * FROM slider WHERE id=$id");

if($result->num_rows == 0){
    die("No record found");
}

$data = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
<title>Edit Slide</title>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

<style>
body {
    padding: 30px;
    background: #f8f9fa;
}
.form-box {
    max-width: 500px;
    margin: auto;
    background: white;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
}
img.preview {
    width: 100%;
    height: 200px;
    object-fit: cover;
    margin-bottom: 10px;
    border-radius: 6px;
}
.error { color: red; }
.success { color: green; }
</style>

</head>
<body>

<div class="form-box">

<h4 class="mb-3">Edit Slide</h4>

<form method="POST" enctype="multipart/form-data">

    <input class="form-control mb-2" 
           name="tab_name" 
           value="<?= htmlspecialchars($data['tab_name']) ?>" 
           placeholder="Tab Name" required>

    <input class="form-control mb-2" 
           name="title" 
           value="<?= htmlspecialchars($data['title']) ?>" 
           placeholder="Title" required>

    <textarea class="form-control mb-2" 
              name="description" 
              placeholder="Description" required><?= htmlspecialchars($data['description']) ?></textarea>

    <label class="form-label">Current Image</label>
    <img src="upload/<?= $data['image'] ?>" class="preview">

    <input type="file" class="form-control mb-3" name="image">

    <button name="update" class="btn btn-primary w-100">
        Update Slide
    </button>

</form>

</div>

<?php
if(isset($_POST['update'])){

    $tab   = trim($_POST['tab_name']);
    $title = trim($_POST['title']);
    $desc  = trim($_POST['description']);

    $errors = [];

    // ===== VALIDATION =====
    if(empty($tab)){
        $errors[] = "Tab name is required";
    }

    if(empty($title)){
        $errors[] = "Title is required";
    }

    if(empty($desc)){
        $errors[] = "Description is required";
    }

    // Image validation (optional)
    if(!empty($_FILES['image']['name'])){
        $allowedTypes = ['jpg','jpeg','png','gif'];
        $fileName = $_FILES['image']['name'];
        $fileTmp  = $_FILES['image']['tmp_name'];
        $fileSize = $_FILES['image']['size'];

        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if(!in_array($ext, $allowedTypes)){
            $errors[] = "Only JPG, PNG, GIF allowed";
        }

        if($fileSize > 2 * 1024 * 1024){
            $errors[] = "Image must be less than 2MB";
        }
    }

    // ===== SHOW ERRORS =====
    if(!empty($errors)){
        foreach($errors as $err){
            echo "<p class='error'>$err</p>";
        }
    } else {

        $img = $data['image'];

        // upload new image
        if(!empty($_FILES['image']['name'])){
            $newName = time() . "_" . $_FILES['image']['name'];
            move_uploaded_file($_FILES['image']['tmp_name'], "upload/".$newName);
            $img = $newName;
        }

        // ===== SAFE QUERY =====
        $stmt = $conn->prepare("UPDATE slider SET tab_name=?, title=?, description=?, image=? WHERE id=?");
        $stmt->bind_param("ssssi", $tab, $title, $desc, $img, $id);
        $stmt->execute();

        echo "<p class='success'>Updated successfully!</p>";

        header("refresh:1;url=index.php");
    }
}
?>

</body>
</html>