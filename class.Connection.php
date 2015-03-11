<?php

include 'inc.dbcredentials.php';

class Connection {

    /**
     * @var PDO $_pdo A singleton PDO database connection instance.
     */
    protected static $_pdo;

    private function __construct() {
        self::_dbConnect();
    }

    protected function _dbConnect() {
        try {
            self::$_pdo = new PDO(
                sprintf( 'mysql:host=%s;dbname=%s', DB_HOSTNAME, DB_DATABASE ),
                                                    DB_USERNAME, DB_PASSWORD );

            self::$_pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
            self::$_pdo->setAttribute( PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_CLASS );
        }
        catch ( PDOException $err ) {
            var_dump( $err );
        }
    }

    /**
     * Creates and returns a PDO MySQL/MariaDB singleton instance.
     * @return PDO
     */
    public static function getInstance() {
        if ( ! self::$_pdo ) {
            new self();
        }
        return self::$_pdo;
    }
}
