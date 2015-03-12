<?php

include 'class.Connection.php';
include 'class.PostModel.php';
include 'class.PostMapper.php';

$Post = new PostMapper( Connection::getInstance() );

$action = filter_input( INPUT_GET, 'action', FILTER_SANITIZE_SPECIAL_CHARS );
$category_id = filter_input( INPUT_GET, 'category_id', FILTER_SANITIZE_NUMBER_INT );

$redir = FALSE;

if ( $action == 'insert' ) {
    $Post->insert();
    $redir = TRUE;
}

if ( $action == 'update' ) {
    $Post->update();
    $redir = TRUE;
}

if ( $action == 'destroy' ) {
    $Post->destroy();
    $redir = TRUE;
}

if ( $action == NULL || $action == 'list' ) {
    $posts = $Post->index( $category_id );
    $redir = FALSE;
}

if ( $redir ) {
    header( 'Location: ./' );
}

include 'template.html.php';

