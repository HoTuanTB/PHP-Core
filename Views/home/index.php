<!DOCTYPE html>
<html>
<head>
    <title>index</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
          integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
</head>
<body>
<div class="container">
    <h2 class="mt-5">List Products</h2>
    <div>
        <a href="/product/add" class="btn btn-primary mt-3">Create</a>
    </div>
    <div class="row mt-3">
        <table class="table table-striped mt-3">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Name</th>
                <th scope="col">Price</th>
                <th scope="col">Description</th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($productsInfo as $key => $product) {
                ?>
                <tr>
                    <th scope="row"><?php echo $key + 1 ?></th>
                    <td><?php echo $product->name ?></td>
                    <td><?php echo $product->price ?></td>
                    <td><?php echo $product->decription ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>