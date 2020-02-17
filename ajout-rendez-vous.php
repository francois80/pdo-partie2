<?php
$exercice = 'Exercice1';
include 'header.php';

function connectDB() {
    require_once 'param.php';

    $dsn = 'mysql:dbname=' . DB . ';host=' . HOST . ';';
    try {
        $db = new PDO($dsn, USER, PASS);
        return $db;
    } catch (Exception $ex) {
        var_dump($ex);
        die('La connexion à la bdd a échoué !!');
    }
}




$db = connectDB();
$db->exec("SET CHARACTER SET utf8");
$query = 'SELECT id, lastname, firstname FROM `patients` ORDER BY lastname ASC';
$usersQueryState = $db->query($query);
$usersList = $usersQueryState->fetchAll(PDO::FETCH_ASSOC);

 //contrôle du rendez-vous
$id = $dateRdv = $hourRdv = "";
$regexHour = "/^[0-9]{2}:[0-9]{2}$/";
$regexDate = "/^((?:19|20)[0-9]{2})-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/";
//on teste si le formulaire est coorext après envoi
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
   
    $id = $_POST['listepatients'];
    if(isset($_POST['dateRdv'])){
        $dateRdv = trim(htmlspecialchars($_POST['dateRdv']));
        if (empty($dateRdv)) {
            $errors['dateRdv'] = 'Veuillez renseigner votre date de rendez-vous souhaitée';
        } elseif (!preg_match($regexDate, $dateRdv)) {
            $errors['dateRdv'] = 'Le format valide est aaaa-mm-jj !';
        }
        //contrôle de l'heure
        $hourRdv = trim(htmlspecialchars($_POST['hourRdv']));
        if (empty($hourRdv)) {
            $errors['hourRdv'] = 'Veuillez renseigner votre horaire de rendez-vous souhaité';
        } elseif (!preg_match($regexHour, $hourRdv)) {
            $errors['hourRdv'] = 'Le format horaire est 00:00 !';
        }
    }
    //s'il n'y a pas d'erreur, on enregistre le rendez-vous dans la base
     if(empty($errors['dateRdv']) && empty($errors['hourRdv'])){
        $db = connectDB();
        $db->exec("SET CHARACTER SET utf8");
    
        $rdv=$db->quote($dateRdv. ' '. $hourRdv. ':00.000');
     //requête insertion
        $query="INSERT IGNORE INTO `appointments` VALUES (NULL, $rdv, $id)";
        //si erreur :
        $nbLignes=$db->exec($query);
        if($nbLignes !=1){
            $msg_error=$db->errorInfo();
            echo $db->errorCode(). ' '. $msg_error[2];
        }
        //après l'enregistrment dans la bdd, on redirige sur la page d'accueil au bout de 3secondes
        sleep(3); // attends 3 secondes
        header('Location: index.php'); // redirige vers cible.php 
        exit();
     
 }
}



?>

<h1>Prenez rendez-vous</h1>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 alert alert-secondary">
            <form method="post" action="#" novalidate>
                <fieldset>
                    <legend>Vos coordonnées</legend>
                    <div class="form-group">
                        <label for="listepatients">Sélectionnez : </label>
                        <select name="listepatients" id="listepatients">
                            <?php foreach ($usersList as $user): ?>
                                <option value="<?= $user['id'] ?>" <?php if($user['id'] == $id){echo ' selected="selected"';} ?>><?= $user['lastname'] ?> <?= $user['firstname'] ?></option>
                                <?php
                            endforeach;
                            ?>
                        </select>
                    </div>
                        <div class="form-group">
                            <label for="dateRdv">Date du rendez-vous : </label>
                            <input type="date" class="form-control" name="dateRdv" id="dateRdv" min="1900-01-01" max="2020-01-01" value="<?= $dateRdv ?>">
                            <span class="text-danger"><?= ($errors['dateRdv']) ?? '' ?></span>
                        </div>
                        <div class="form-group">
                            <label for="hourRdv">Heure du rendez-vous : </label>
                            <input type="text" class="form-control" name="hourRdv" id="hourRdv" min="09:00" max="19:00" placeholder="00:00" value="<?= $hourRdv ?>">
                            <span class="text-danger"><?= ($errors['hourRdv']) ?? '' ?></span>
                        </div>
                        <input type="submit"  name="submit" value="Envoyez" />
                        <input type="reset"  name="reset" value="Effacez" />
                </fieldset>
            </form>
        </div>
    </div>
</div>
<p><a href="ajout-patient.php">nouveau patient ?</a></p>

<?php
// footer
include 'footer.php';
?>