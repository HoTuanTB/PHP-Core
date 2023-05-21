<!DOCTYPE html>
<html>
<head>
    <title>index</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
          integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
</head>
<body>
<div class="container">


    <?php if (isset($message)) { ?>


        <div class="alert alert-primary" role="alert">
            <?php echo $message ?>
        </div>


    <?php } ?>


    <h2 class="mt-5">List Products</h2>
    <div>
        <a href="/product/add" class="btn btn-primary mt-3">Create</a>
    </div>
    <div class="row mt-3">
        <table style="text-align: center" class="table table-striped mt-3">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">ID</th>
                <th scope="col">Name</th>
                <th scope="col">Description</th>
                <th scope="col">Price</th>
                <th scope="col">Image</th>
                <th scope="col">Update</th>
                <th scope="col">Delete</th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($productsInfo as $key => $product) {
                ?>
                <tr>
                    <th scope="row"><?php echo $key + 1 ?></th>
                    <td><?php echo $product->id ?></td>
                    <td><?php echo $product->name ?></td>
                    <td><?php echo $product->decription ?></td>
                    <td><?php echo $product->price ?></td>
                    <td><img style="width: 50px" src=" <?php echo $product->image ?>"></td>
                    <td><a href="/product/update?id=<?php echo $product->id ?>" class="btn btn btn-info">Update</a></td>
                    <td><a href="/product/delete?id=<?php echo $product->id ?>" class="btn btn btn-danger">Delete</a>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>