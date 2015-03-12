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

    public function insert() {
        // echo '<pre>'; print_r($_REQUEST); echo '</pre>';
        $pos = filter_input( INPUT_POST, 'pos', FILTER_SANITIZE_NUMBER_INT );
        $category_id = filter_input( INPUT_POST, 'category_id', FILTER_SANITIZE_NUMBER_INT );
        $title = filter_input( INPUT_POST, 'title', FILTER_SANITIZE_SPECIAL_CHARS );

        if ( $pos > 0 ) {
            return $this->insertBefore( $pos, $title, $category_id );
        }
        else {
            return $this->insertEnd( $title, $category_id );
        }
    }

    function update() {

        $id = filter_input( INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT );
        $category_id = filter_input( INPUT_POST, 'category_id', FILTER_SANITIZE_NUMBER_INT );
        $newpos = filter_input( INPUT_POST, 'pos', FILTER_SANITIZE_NUMBER_INT );
        $title = filter_input( INPUT_POST, 'title', FILTER_SANITIZE_SPECIAL_CHARS );

        $sql = 'SELECT pos FROM post WHERE id = :id;';
        try {
            $sth = self::$_pdo->prepare( $sql );
            $sth->bindParam( ':id', $id, PDO::PARAM_INT );
            $sth->execute();
            $oldpos = $sth->fetch( PDO::FETCH_OBJ )->pos;
        }
        catch ( PDOException $err ) {
            var_dump( $err );
        }

        //
        // TODO: We still lack a method (or other approach) for when
        // the user changes other values but not the position.
        //

        // If we are moving the post "upwards" or "downwards".
        if ( $newpos < $oldpos ) {
            $this->_moveUpToPos( $newpos, $oldpos, $category_id, $id );
        }
        else {
            $this->_moveDownToPos( $newpos, $oldpos, $category_id, $id );
        }
    }

    //
    // Moves from a lower position to a higher position.
    // Increments the ones below the new position stopping at the
    // position where it originally was.
    //
    protected function _moveUpToPos( $newpos, $oldpos, $category_id, $id ) {

        // Updates from $newpos up to before $oldpos.
        $sql = 'UPDATE post SET
              pos = pos + 1
            WHERE category_id = :cat
            AND pos >= :newpos
            AND pos < :oldpos;';
        try {
            $sth = self::$_pdo->prepare( $sql );
            $sth->bindParam( ':newpos', $newpos, PDO::PARAM_INT );
            $sth->bindParam( ':oldpos', $oldpos, PDO::PARAM_INT );
            $sth->bindParam( ':cat', $category_id, PDO::PARAM_INT );
            $status = $sth->execute();
        }
        catch ( PDOException $err ) {
            var_dump( $err );
        }

        // Change the of pos the explicitly repositioned post itself.
        $sql = 'UPDATE post SET
            pos = :newpos
            WHERE id = :id;';

        try {
            $sth = self::$_pdo->prepare( $sql );
            $sth->bindParam( ':newpos', $newpos, PDO::PARAM_INT );
            $sth->bindParam( ':id', $id, PDO::PARAM_INT );
            $sth->execute();
            echo "sth: $newpos, $id "; var_dump( $sth );
        }
        catch ( PDOException $err ) {
            var_dump( $err );
        }
        return TRUE;
    }

    //
    // Moves from an upper position to a lower position.
    // Decrements the ones above the new position stopping at the
    // position where it originally was.
    //
    protected function _moveDownToPos( $newpos, $oldpos, $category_id, $id ) {

        // Updates from $pos + 1 to the one that will be changed explicitly.
        $sql = 'UPDATE post SET
            pos = pos - 1
            WHERE category_id = :cat
            AND pos <= :newpos
            AND pos > :oldpos;';
        try {
            $sth = self::$_pdo->prepare( $sql );
            $sth->bindParam( ':newpos', $newpos, PDO::PARAM_INT );
            $sth->bindParam( ':oldpos', $oldpos, PDO::PARAM_INT );
            $sth->bindParam( ':cat', $category_id, PDO::PARAM_INT );
            $sth->execute();
        }
        catch ( PDOException $err ) {
            var_dump( $err );
        }

        // Change the position of the explicitly repositioned post itself.
        $sql = 'UPDATE post SET
            pos = :newpos
            WHERE id = :id;';

        try {
            $sth = self::$_pdo->prepare( $sql );
            $sth->bindParam( ':newpos', $newpos, PDO::PARAM_INT );
            $sth->bindParam( ':id', $id, PDO::PARAM_INT );
            $sth->execute();
            echo $sql;
            echo "$newpos, $id";
            return TRUE;
        }
        catch ( PDOException $err ) {
            var_dump( $err );
        }
    }

    protected function insertEnd( $title, $category_id ) {

        // Calculates where the next position will be.
        $pos = self::$_pdo->query( 'SELECT COUNT(*) AS newpos FROM post;')
             ->fetch( PDO::FETCH_OBJ )->newpos + 1;

        // Inserts it, setting to the latest position.
        $sql = 'INSERT INTO post ( pos, title, category_id ) VALUES ( :pos, :title, :cat );';
        try {
            $sth = self::$_pdo->prepare( $sql );
            $sth->bindParam( ':pos', $pos, PDO::PARAM_INT );
            $sth->bindParam( ':title', $title, PDO::PARAM_STR );
            $sth->bindParam( ':cat', $category_id, PDO::PARAM_INT );
            $sth->execute();
            return TRUE;
        }
        catch ( PDOException $err ) {
            var_dump( $err );
        }
    }

    protected function insertBefore( $pos, $title, $category_id ) {

        // This will leave a gap in the ordering.
        $sql = "UPDATE post SET
            pos = pos + 1
            WHERE category_id >= :cat
            AND pos >= :pos;";

        $sth = self::$_pdo->prepare( $sql );
        $sth->bindParam( ':pos', $pos, PDO::PARAM_INT );
        $sth->bindParam( ':cat', $category_id, PDO::PARAM_INT );
        $sth->execute();

        // We now fill that gap.
        $sql = "INSERT INTO post ( pos, title, category_id ) VALUES ( :pos, :title, :cat );";
        try {
            $sth = self::$_pdo->prepare( $sql );
            $sth->bindParam( ':pos', $pos, PDO::PARAM_INT );
            $sth->bindParam( ':title', $title, PDO::PARAM_STR );
            $sth->bindParam( ':cat', $category_id, PDO::PARAM_INT );
            $sth->execute();
            return TRUE;
        }
        catch ( PDOException $err ) {
            var_dump( $err );
        }
    }


    public function destroy() {

        $id = filter_input( INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT );

        // Stores the pos of the one being destroyed.
        $sql = 'SELECT pos FROM post WHERE id = :id;';
        try {
            $sth = self::$_pdo->prepare( $sql );
            $sth->bindParam( ':id', $id, PDO::PARAM_INT );
            $sth->execute();
            $pos = $sth->fetch( PDO::FETCH_OBJ )->pos;
        }
        catch ( PDOException $err ) {
            var_dump( $err );
        }

        $sql = 'DELETE FROM post WHERE id = :id;';
        try {
            $sth = self::$_pdo->prepare( $sql );
            $sth->bindParam( ':id', $id, PDO::PARAM_INT );
            $sth->execute();
        }
        catch ( PDOException $err ) {
            var_dump( $err );
        }

        // $pos is clean because we just retrieved it from DB.
        $sql = "UPDATE post SET
            pos = pos - 1
            WHERE pos > {$pos};";

        self::$_pdo->query( $sql );

        return TRUE;
    }
}


