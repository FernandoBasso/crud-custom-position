# Crud With Custom Position #

This is a solution to a situation I had at work. The basic idea is that
the user wants to define the position an entry on the database should
appear on the web page by providing a number.

## Add New Entry ##

In this case, if the new position is set to zero, we assume the user wants to
insert at the end. We then get the number or rows and set the position to
number of rows + 1.


## Lower to Upper Position ##

If user updates position 5 to 1 (it goes from a lower position to a higher
position), we first update the one that current has position 1 to 2, and also
increment the other positions stopping at the item that has the position 1 less
than the one we are changing. Finally we update the item that the user is
explicitly changing by its id.

    // Updates from $newpos up to before $oldpos.
    $sql = "UPDATE post SET
      pos = pos + 1
      WHERE post.pos >= $newpos
      AND post.pos < $oldpos;";


    // Change the pos the explicitly repositioned post itself.
      $sql = "UPDATE post SET
      pos = {$newpos}
      WHERE post.id = {$pk};";


## Upper to Lower Position ##


Update from current item pos + 1 up to the one in the position that the changed
one will occupy. If user changes item in position 4 to position 1, decrement
from position 2 to the one that current is in position 4. Then, update the item
that the user is explicitly changing by its id.

    // Updates from $pos + 1 to the one that will be changed explicitly.
    $sql = "UPDATE post SET
      pos = pos - 1
      WHERE post.pos = $newpos + 1
      AND post.pos <= $oldpos;

    // Change the position of the explicitly repositioned post itself.
    $sql = "UPDATE post SET
      pos = $newpos
      WHERE id = {$pk};";

## Remove Entry ##

Simply decrement the position of all the entries whose position are greater
than the position of the element being removed.

