<?php

include 'class.Connection.php';
include 'class.PostModel.php';
include 'class.PostMapper.php';

$Post = new PostMapper( Connection::getInstance() );

$action = filter_input( INPUT_GET, 'action', FILTER_SANITIZE_SPECIAL_CHARS );
$category_id = filter_input( INPUT_GET, 'category_id', FILTER_SANITIZE_NUMBER_INT );

if ( $action == 'update' ) {
}


$posts = $Post->index( $category_id );


include 'template.html.php';
