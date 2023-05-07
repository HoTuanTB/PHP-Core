<?php

class BeLongsTo
{
    public function beLongsTo($pdo)
    {
        $productsSql = "SELECT * FROM products";
        $stmt = $pdo->prepare($productsSql);
        $stmt->execute();
        $products = $stmt->fetchAll(\PDO::FETCH_OBJ);

        $arrayCategoryId = [];
        foreach ($products as $product) {
            $arrayCategoryId[] = $product->category_id;
        }

        $formatCategoryId = implode(", ", $arrayCategoryId);

        $categoriesSql = "SELECT * FROM category where id in ($formatCategoryId) ";
        $stmt = $pdo->prepare($categoriesSql);
        $stmt->execute();
        $categories = $stmt->fetchAll(\PDO::FETCH_OBJ);
        foreach ($products as $product) {
            $categoryId = $product->category_id;
            $product->category = $categories[$categoryId];
        }
        echo "<pre>";
        print_r($products);
    }
}