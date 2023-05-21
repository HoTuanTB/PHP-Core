<!DOCTYPE html>
<html>
<head>
    <title>index</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <h2 class="mt-5">Update Product : <?php echo $product['id'] ?> </h2>
    <img style="width: 200px" src=" <?php echo $product['image'] ?>">
    <br>
    <form method="post" action="" enctype="multipart/form-data">
        <div class="form-group">
            <label>Name</label>
            <input name="name" type="text" value="<?php echo $product['name'] ?>" class="form-control">
        </div>
        <div class="form-group">
            <label>Decription</label>
            <input name="decription" value="<?php echo $product['decription'] ?>" type="text" class="form-control">
        </div>
        <div class="form-group">
            <label>Price</label>
            <input name="price" value="<?php echo $product['price'] ?>" type="text" class="form-control">
        </div>
        <div class="form-group">
            <label>Image</label>
            <input name="image" type="file" class="form-control">
        </div>
        <button type="submit" class="btn btn-info">Update</button>
    </form>
</div>
</body>
</html>