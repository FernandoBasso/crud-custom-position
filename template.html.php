<!DOCTYPE html>
<html>
<head>
  <meta charset='utf-8'>
  <meta name='viewport' content='width=device-width'>
  <title>CRUD Custom Posotion</title>
  <link rel='stylesheet' type='text/css' href='styles.css'>
</head>
<body>

<nav>
    <ul class='cf'>
        <li><a href='/'>All</a></li>
        <li><a href='?category_id=1'>News</a></li>
        <li><a href='?category_id=2'>Lessons</a></li>
        <li><a href='?category_id=3'>Videos</a></li>
    </ul>
</nav>

<section class='list'>
<?php
if ( isset( $posts ) ):
    foreach ( $posts AS $post ): ?>
        <form method='POST' action='./?action=update'>
            <input type='hidden' name='category_id' value='<?= $post->category_id; ?>'>
            <input type='text' name='id' value='<?= $post->id; ?>' readonly>
            <input type='text' name='pos' value='<?= $post->pos; ?>'>
            <input type='text' name='title' value='<?= $post->title; ?>'>
            <input type='submit' value='Update'>
            <a href='?action=destroy&id=<?= $post->id; ?>'>Remove</a>
        </form>
<?php
    endforeach;
endif;
?>

    </section>

<p>Insert a new one:</p>
<form method='POST' action='./?action=insert' class='insert'>
    <div class='wrap'>
        <label for='pos'>Position:</label>
        <input type='text' id='pos' name='pos' value='0'>
        <label for='category'>Category:</label>
        <select id='category_id' name='category_id'>
            <option value='1'>News</option>
            <option value='2'>Lessons</option>
            <option value='3'>Videos</option>
        </select>
    </div>
    <div class='wrap'>
        <label for='title'>Title:</label>
        <input type='text' id='title' name='title'>
    </div>
    <input type='submit' value='Save'>
</form>

</body>
</html>
