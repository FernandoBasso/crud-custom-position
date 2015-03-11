CREATE DATABASE crud_custom_position DEFAULT CHARACTER SET utf8;

USE crud_custom_position;

CREATE TABLE category (
    id INTEGER NOT NULL AUTO_INCREMENT,
    name VARCHAR( 64 ) NOT NULL,
    PRIMARY KEY( id )
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8;

INSERT INTO category ( id, name ) VALUES
( 1, 'News' ),
( 2, 'Lessons' ),
( 3, 'Videos' );

CREATE TABLE post (
    id INTEGER NOT NULL AUTO_INCREMENT,
    category_id INTEGER NOT NULL,
    title VARCHAR( 128 ) NOT NULL,
    pos INTEGER NOT NULL DEFAULT 0,
    FOREIGN KEY ( category_id ) REFERENCES category( id ),
    PRIMARY KEY( id )
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8;

INSERT INTO post ( id, category_id, title, pos ) VALUES
(  1, 2, 'Arch Linux', 1 ),
(  2, 1, 'Xfce 4.12 released!', 1 ),
(  3, 2, 'Gentoo', 2 ),
(  4, 2, 'Debian', 3 ),
(  5, 1, 'Steam releases indie bundle for Linux', 2 ),
(  6, 2, 'Fedora', 4 ),
(  7, 3, 'Vimcast Ex Commands', 1 ),
(  8, 3, 'Advanced Command Line Mode', 2 ),
(  9, 2, 'OpenBSD', 5 ),
( 10, 3, 'Tagging Like a Boss', 3 );

-- “Reset” table `post`:
DELETE FROM post WHERE TRUE;
ALTER TABLE post AUTO_INCREMENT = 1;
