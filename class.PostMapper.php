<?php

class PostMapper {

    protected static $_pdo;

    public function __construct( PDO $pdo ) {
        self::$_pdo = $pdo;
    }

    public function index( $category_id ) {
        $sql = 'SELECT id, category_id, title, pos
                    FROM post';

        if ( $category_id != NULL ) {
            $sql .= ' WHERE category_id = :cat_id;';
        }

        try {
            $sth = self::$_pdo->prepare( $sql );

            if ( $category_id != NULL ) {
                $sth->bindParam( ':cat_id', $category_id, PDO::PARAM_INT );
            }

            $sth->execute();
            $sth->setFetchMode( PDO::FETCH_CLASS, 'PostModel' );
            return $sth->fetchAll();
        }
        catch ( PDOException $err ) {
            var_dump( $err->getMessage() );
        }
    }
}
