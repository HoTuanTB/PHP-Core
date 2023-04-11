<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            /*margin: 0;*/
            /*padding: 0;*/
            /*display: flex;*/
            /*justify-content: center;*/
            /*align-items: center;*/
            /*height: 100vh;*/
            /*background-color: #f2f2f2;*/
        }

        form {
            background-color: #fff;
            padding: 20px 30px;
            border-radius: 5px;
            box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.2);
        }

        input[type='file'] {
            margin-bottom: 10px;
        }

        input[type='submit'] {
            background-color: #4CAF50;
            color: white;
            padding: 8px 16px;
            border: none;
            cursor: pointer;
            border-radius: 3px;
            font-size: 14px;
        }

        input[type='submit']:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
<?php

var_dump($data);
?>
<div style="display: flex">
    <form action="/app/controllers/UploadController/upload" method="POST" enctype="multipart/form-data">.
        <p>Select image to upload:</p>
        <input name="file" type="file" id="upload-file" onchange="displayImage(event)">
        <img style="width: 1000px; height: auto" id="image-preview">
        <br>
        <input type="submit" value="Upload Image" name="submit">
    </form>




</div>

</body>

<script>
    function displayImage(event) {
        var input = event.target;
        var reader = new FileReader();
        reader.onload = function () {
            var dataURL = reader.result;
            var imagePreview = document.getElementById("image-preview");
            imagePreview.src = dataURL;
            imagePreview.style.display = "block";
        };
        reader.readAsDataURL(input.files[0]);
    }
</script>

</html>
