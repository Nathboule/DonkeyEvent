<?php

require_once 'connec.php';
require_once 'index.html';

session_start();

const BR = '<br> <br>';

// Create connection to database
$pdo = new \PDO(DSN, USER, PASS, [
    PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC
]);
?>

<?php

$query = "SELECT * FROM Event";
$statement = $pdo->query($query);
$events = $statement->fetchAll(); 
?>

<ul>
<form method="GET" action="product.php" name="product">
    <?php foreach($events as $event): ?>
        <input type="hidden" name="idEvent" value="<?php echo $event["idEvent"]; ?>" />
    <li><?php echo $event['name'] . BR . $event['description']; ?></li>
    <button type="submit">En savoir plus</button>
    <?php endforeach ?>
</form>
</ul>