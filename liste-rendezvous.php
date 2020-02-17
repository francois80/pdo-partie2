<?php
$exercice = 'Exercices';
include 'header.php';

function connectDB(){
    require_once  'param.php';
   
    $dsn = 'mysql:dbname='. DB. ';host='. HOST. ';';
    try{
        $db = new PDO($dsn, USER, PASS);
        return $db;
    } catch (Exception $ex) {
        var_dump($ex);
        die('La connexion à la bdd a échoué !!');
    }
}
$db = connectDB();

$db->exec("SET CHARACTER SET utf8");
$query ='SELECT `patients`.`id`, `lastname`, `firstname`, DATE_FORMAT(`dateHour`, \'%d/%m/%Y %H:%i:%s\') `dateHour` from `patients`, `appointments` WHERE `patients`.`id` = `appointments`.`idPatients`';
$usersQueryState = $db->query($query);
$usersList = $usersQueryState->fetchAll(PDO::FETCH_ASSOC);

if (isset($_GET['suppr']) && $_GET['suppr'] == '1'){
    $idSupprim = $_GET['id'];
    $horaire = date("Y-d-m H:i:s" ,strtotime($_GET['horaire']));
    
    $QuerySup = "DELETE FROM `appointments` WHERE `idPatients` = $idSupprim AND `dateHour`=' $horaire'";
    $userDelete = $db->exec($QuerySup);
    $server = $_SERVER['PHP_SELF'];
    header("Refresh: 1;url= $server");
    
}
?>
<h1>Liste des rendez-vous</h1>
<div class="container">
<table class="tableau">
    <thead>
    <tr>
        <th>Nom</th>
        <th>Prénom</th>
        <th>Rendez-vous</th>
        <th></th>
        </tr>
    </thead>
    <TBODY>
        <?php
        foreach ($usersList as $user): ?>
      <tr>
           
            <td><a href="rendezvous.php?id=<?= $user['id'] ?>"><?= $user['lastname'] ?></a></td>
            <td><?= $user['firstname'] ?></td>
            <td><?= $user['dateHour'] ?></td>
            <td><a href="<?=$_SERVER['PHP_SELF'] ?>?id=<?= $user['id'] ?>&amp;suppr=1&amp;horaire=<?= $user['dateHour'] ?>"><span class="rouge delete" id="del_<?= $user['id'] ?>">supprimer</span></a></td>
           
        </tr>
        <?php
    endforeach;
    ?>
    </TBODY>
</table>
</div>
<p></p>
<p><a href="ajout-rendez-vous.php">Ajouter un rendez-vous</a></p>

<?php 
// footer
include 'footer.php';
?>