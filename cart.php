<?php

require_once 'connec.php';
require_once 'index.html';
require_once 'header.php';

const BR = '<br> <br>';

$total = 0;
$insurancePrice = 5;

if (empty($_SESSION['cartItems'])) {
    $_SESSION['cartItems'] = [];
}

if (!empty($_GET)) {
    $idSession = $_GET['idSession'];
    if (isset($_GET['nbTickets'])) {
        $nbTickets = $_GET['nbTickets'];
    } else {
        $nbTickets = 1;
    }
    if (isset($_GET['insurance'])) {
        $insurance = $_GET['insurance'];
    } else {
        $insurance = FALSE;
    }
    if (isset($_GET['cancellation'])) {
        $cancellation = $_GET['cancellation'];
    } else {
        $cancellation = FALSE;
    }

    $_SESSION['cartItems'][$idSession] = [
    "session"=>$idSession,
    "nbTickets"=>$nbTickets,
    "insurance"=>$insurance,
    "cancellation"=>$cancellation
    ];
} ?>

<h1>Mon panier</h1>

<?php
if (isset($_SESSION['cartItems'])) { ?>
    <div class="container-fluid">
        <div class="row">
            <aside class="col-lg-9">
                <div class="card">
                    <div class="table-responsive">
                        <table class="table table-borderless table-shopping-cart">
                            <thead class="text-muted">
                                <tr class="small text-uppercase">
                                    <th scope="col">Evènement</th>
                                    <th scope="col">Catégorie</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">Ville</th>
                                    <th scope="col">Lieu</th>
                                    <th scope="col">Tarif</th>
                                    <th scope="col">Assurance anulation</th>
                                    <th scope="col">Quantité</th>
                                    <th scope="col">Valider les modifications</th>
                                    <th scope="col"> Supprimer la réservation</th>
                                </tr>
                                <?php 
                                foreach ($_SESSION['cartItems'] as $session => $sessionDetails) {
                                    $query = "SELECT capacity, Event.name as event, Genre.name as genre, Artist.name as artist, Venue.name as venue, City.name as city, date, price, idSession 
                                    FROM Session 
                                    JOIN Event ON Event_idEvent = idEvent JOIN Venue ON Venue_idVenue = idVenue JOIN City ON City_idCity = idCity JOIN Performance ON Performance.Event_idEvent = idEvent JOIN Artist ON Artist_idArtist = idArtist JOIN Genre ON Genre_idGenre = idGenre 
                                    WHERE idSession =" . $sessionDetails["session"];
                                    $statement = $pdo->query($query);
                                    $sessionInfo = $statement->fetchAll(PDO::FETCH_ASSOC);
                                    for($i = 0; $i < count($sessionInfo); $i++) { ?>
                                        <tbody>
                                            <tr>
                                                <td><?php echo $sessionInfo[$i]['event']; ?></td>
                                                <td><?php echo $sessionInfo[$i]['genre']; ?></td>
                                                <td><?php echo $sessionInfo[$i]['date']; ?></td>
                                                <td><?php echo $sessionInfo[$i]['city']; ?></td>
                                                <td><?php echo $sessionInfo[$i]['venue']; ?></td>
                                                <td><div class="price-wrap"><var class="price"><?php echo $sessionInfo[$i]['price'] .'€'; ?></var></div></td>
                                                <form action="refreshCart.php" method="get">
                                                    <input type="hidden" name="idSession" value="<?php echo $sessionDetails["session"]; ?>" />
                                                    <td><div class="form-group form-check">
                                                        <input type="checkbox" name="insurance" value="TRUE" class="form-check-input" id="exampleCheck1" <?php if ($sessionDetails["insurance"] == TRUE){ ?> checked <?php } ?>>
                                                    </div></td>
                                                    <td><select name="nbTickets">
                                                        <?php
                                                        $capacity = (int) $sessionInfo[$i]['capacity'];
                                                        for($j=1; $j<=$capacity && $j<=10; $j++) { ?>
                                                            <option <?php if ($sessionDetails["nbTickets"] == $j){ ?> selected="selected" <?php } ?> value=<?php echo $j ?>><?php echo $j ?></option>
                                                        <?php } ?>
                                                    </select></td>
                                                    <td><button class="btn btn-primary btn-sm btn-round" data-abc="true" type="submit">Valider</button></td>
                                                </form>
                                                <td class="text-right d-none d-md-block">
                                                    <form action="deleteFromCart.php" method="get">
                                                        <input type="hidden" name="idSession" value="<?php echo $sessionDetails["session"]; ?>" />
                                                        <button class="btn btn-danger btn-sm btn-round" data-abc="true" type="submit" name="cancellation" value="TRUE">Supprimer</button>
                                                    </form>
                                                </td>
                                                <?php $total += $sessionDetails["nbTickets"]*$sessionInfo[$i]['price'];
                                                if ($sessionDetails["insurance"]) {
                                                    $total += $insurancePrice;
                                                }
                                                ?>
                                            </tr>
                                        </tbody>
                                    <?php } ?>
                                <?php } ?>
                            </thead>
                        </table>
                    </div>
                </div>
            </aside>
            <aside class="col-lg-3">
                <div class="card">
                    <div class="card-body">
                        <dl class="dlist-align">
                            <dt>Total :</dt>
                            <dd class="text-right text-dark b ml-3"><strong><?php echo $total ?> €</strong></dd>
                        </dl>
                        <hr>
                        <form action="validateCart.php">
                            <button class="btn btn-out btn-primary btn-square btn-main" data-abc="true"> Confirmer la réservation </button>
                        </form>
                        <a href="index.php" class="btn btn-out btn-success btn-square btn-main mt-2" data-abc="true">Faire une autre réservation </a>
                    </div>
                </div>
            </aside>
        </div>
    </div>
<?php }