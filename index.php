<?php include 'db.php';

/* ================= ADD ================= */
if(isset($_POST['add'])){
    $tab = $_POST['tab_name'];
    $title = $_POST['title'];
    $desc = $_POST['description'];

    $img = $_FILES['image']['name'];
    move_uploaded_file($_FILES['image']['tmp_name'], "upload/".$img);

    $conn->query("INSERT INTO slider(tab_name,title,description,image)
    VALUES('$tab','$title','$desc','$img')");

    header("Location:index.php");
    exit;
}

/* ================= UPDATE ================= */
if(isset($_POST['update'])){
    $id = $_POST['edit_id'];
    $tab = $_POST['tab_name'];
    $title = $_POST['title'];
    $desc = $_POST['description'];

    $conn->query("UPDATE slider SET 
        tab_name='$tab',
        title='$title',
        description='$desc'
        WHERE id=$id");

    header("Location:index.php");
    exit;
}

/* ================= FETCH ================= */
$result = $conn->query("SELECT * FROM slider ORDER BY tab_name");

$tabs = [];
while($row = $result->fetch_assoc()){
    $tabs[$row['tab_name']][] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Final Slider Project</title>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<style>
body { padding:20px; }
.slider { display:none; }

.carousel-item {
    padding:20px;
    background:#f5f5f5;
}

#main-image {
    width:100%;
    aspect-ratio:1/1;
    object-fit:cover;
}

/* MOBILE */
@media(max-width:768px){
    .desktop-tabs { display:none; }
    .mobile-accordion { display:block; }
    .col-md-4 { display:none; }

    .carousel-item {
        height:300px;
        color:white;
        background-size:cover;
    }
}

@media(min-width:769px){
    .mobile-accordion { display:none; }
}

.mobile-accordion { display:none; }

@media(max-width:768px){
    .desktop-tabs { display:none; }
    .mobile-accordion { display:block; }
    .col-md-4 { display:none; } /* hide column 3 */
}
</style>

</head>
<body>

<div class="container">

<!-- ADD BUTTON -->
<div class="mb-3 text-end">
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addModal">
        + Add Slide
    </button>
</div>

<div class="row">

<!-- COLUMN 1 -->
<div class="col-md-3 desktop-tabs">
<?php foreach($tabs as $tab => $slides): 
$safeTab = str_replace(' ','_', $tab);
?>
<button class="btn btn-primary w-100 mb-2 tab-btn" data-tab="<?= $safeTab ?>">
    <?= $tab ?>
</button>
<?php endforeach; ?>
</div>

<div class="mobile-accordion d-md-none mb-3">

<div class="accordion" id="mobileAccordion">

<?php $i=0; foreach($tabs as $tab => $slides): 
$safeTab = str_replace(' ','_', $tab);
?>

<div class="accordion-item">

<h2 class="accordion-header">
<button class="accordion-button <?= $i!=0?'collapsed':'' ?>" 
        data-bs-toggle="collapse" 
        data-bs-target="#mob-<?= $safeTab ?>">
    <?= htmlspecialchars($tab) ?>
</button>
</h2>

<div id="mob-<?= $safeTab ?>" 
     class="accordion-collapse collapse <?= $i==0?'show':'' ?>">

<div class="accordion-body">

<!-- MOBILE SLIDER -->
<div id="mob-carousel-<?= $safeTab ?>" class="carousel slide">

<div class="carousel-inner">

<?php $first=true; foreach($slides as $slide): ?>
<div class="carousel-item <?= $first?'active':'' ?>"
     style="background-image:url('upload/<?= $slide['image'] ?>');
            background-size:cover;
            background-position:center;
            height:300px;">

<div class="p-3" style="background:rgba(0,0,0,0.5); color:white; height:100%;">
    <h5><?= htmlspecialchars($slide['title']) ?></h5>
    <p><?= htmlspecialchars($slide['description']) ?></p>
</div>

</div>
<?php $first=false; endforeach; ?>

</div>

<button class="carousel-control-prev" type="button"
data-bs-target="#mob-carousel-<?= $safeTab ?>" data-bs-slide="prev">
<span class="carousel-control-prev-icon"></span>
</button>

<button class="carousel-control-next" type="button"
data-bs-target="#mob-carousel-<?= $safeTab ?>" data-bs-slide="next">
<span class="carousel-control-next-icon"></span>
</button>

</div>

</div>
</div>

</div>

<?php $i++; endforeach; ?>

</div>
</div>

<!-- COLUMN 2 -->
<div class="col-md-5">

<?php foreach($tabs as $tab => $slides): 
$safeTab = str_replace(' ','_', $tab);
?>

<div id="carousel-<?= $safeTab ?>" class="carousel slide slider">

<div class="carousel-inner">
<?php $first=true; foreach($slides as $slide): ?>
<div class="carousel-item <?= $first?'active':'' ?>" data-image="<?= $slide['image'] ?>">

<div class="p-3 text-start">

<h4><?= htmlspecialchars($slide['title']) ?></h4>
<p><?= htmlspecialchars($slide['description']) ?></p>

<div class="p-3 md-5">
    <a href="edit.php?id=<?= $slide['id'] ?>" class="btn btn-sm btn-primary">
        Edit
    </a>

    <a href="delete.php?id=<?= $slide['id'] ?>" class="btn btn-sm btn-danger">
        Delete
    </a>
</div>

</div>

</div>
<?php $first=false; endforeach; ?>
</div>

<button class="carousel-control-prev" type="button"
data-bs-target="#carousel-<?= $safeTab ?>" data-bs-slide="prev">
<span class="carousel-control-prev-icon"></span>
</button>

<button class="carousel-control-next" type="button"
data-bs-target="#carousel-<?= $safeTab ?>" data-bs-slide="next">
<span class="carousel-control-next-icon"></span>
</button>

</div>

<?php endforeach; ?>

</div>

<!-- COLUMN 3 -->
<div class="col-md-4">
<img id="main-image" src="">
</div>

</div>
</div>

<!-- ADD MODAL -->
<div class="modal fade" id="addModal">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header">
        <h5>Add Slide</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <form method="POST" enctype="multipart/form-data">

      <div class="modal-body">
        <input class="form-control mb-2" name="tab_name" placeholder="Tab Name" required>
        <input class="form-control mb-2" name="title" placeholder="Title" required>
        <textarea class="form-control mb-2" name="description"></textarea>
        <input type="file" class="form-control" name="image" required>
      </div>

      <div class="modal-footer">
        <button name="add" class="btn btn-success">Save</button>
      </div>

      </form>

    </div>
  </div>
</div>

<!-- EDIT MODAL -->
<div class="modal fade" id="editModal">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header">
        <h5>Edit Slide</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <form method="POST">

      <input type="hidden" name="edit_id" id="edit_id">

      <div class="modal-body">
        <input class="form-control mb-2" id="edit_tab" name="tab_name" required>
        <input class="form-control mb-2" id="edit_title" name="title" required>
        <textarea class="form-control mb-2" id="edit_desc" name="description"></textarea>
      </div>

      <div class="modal-footer">
        <button name="update" class="btn btn-primary">Update</button>
      </div>

      </form>

    </div>
  </div>
</div>

<script>
$(document).ready(function(){

    $('.slider').hide();

    $('.tab-btn').click(function(){

        let tab = $(this).data('tab');

        $('.slider').hide();

        let active = $('#carousel-' + tab);
        active.show();

        
        let carousel = bootstrap.Carousel.getOrCreateInstance(active[0]);
        carousel.to(0);
        active[0].offsetHeight;

        let img = active.find('.carousel-item.active').data('image');
        if(img){
            $('#main-image').attr('src','upload/'+img);
        }
    });

    $('.carousel').on('slid.bs.carousel', function () {
        let img = $(this).find('.carousel-item.active').data('image');
        if(img){
            $('#main-image').attr('src','upload/'+img);
        }
    });

    $('.tab-btn').first().trigger('click');

    $('.carousel-item').each(function(){
        let img = $(this).data('image');
        $(this).css('background-image','url(upload/'+img+')');
    });

});
</script>

</body>
</html>