<?php

class ManyToMany
{
    public function manyToMany($pdo)
    {
        $productsSql = "SELECT * FROM products";
        $stmt = $pdo->prepare($productsSql);
        $stmt->execute();
        $products = $stmt->fetchAll(\PDO::FETCH_OBJ);
        $idProducts = [];
        foreach ($products as $product) {
            $idProducts[] = $product->id;
        }

        $formatProductId = implode(", ", $idProducts);

        $tagsSql = "SELECT * FROM tags INNER JOIN product_tag ON tags.id = product_tag.tag_id WHERE product_tag.product_id IN ($formatProductId)";
        $stmt = $pdo->prepare($tagsSql);
        $stmt->execute();
        $tags = $stmt->fetchAll(\PDO::FETCH_OBJ);

        $productIdTag = [];

        foreach ($tags as $tag) {
            $productIdTag[$tag->product_id][] = $tag;
        }

        foreach ($products as $product) {
            $product->tags = $productIdTag[$product->id];
        }

        echo "<pre>";
        print_r($products);
    }
}

