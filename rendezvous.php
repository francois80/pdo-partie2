<?php
$exercice = 'Exercices';
include 'header.php';
//include de validation du formulaire
include 'form_validation.php';
$dateRdv = $hourRdv = "";
$regexHour = "/^[0-9]{2}:[0-9]{2}$/";
$regexDate = "/^((?:19|20)[0-9]{2})-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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
}
if (empty($_GET['id']) && isset($_GET['id'])) {
    echo 'Pas de patient sélectionné !!';
} else {
    $id = $_GET['id'];

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
    $query ="SELECT `lastname`, `firstname`,`birthdate`,`phone`, `mail`, `dateHour` from `patients`, `appointments` WHERE `patients`.`id` = $id AND `appointments`.`idPatients` = $id";
    $usersQueryState = $db->query($query);
    $usersList = $usersQueryState->fetchAll(PDO::FETCH_ASSOC);

    ?>
    <h1>Rendez-vous du patient</h1>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 alert alert-secondary">
                <?php
                //si post du formulaire complété sans erreurs, affichage du résultat
                if ($isSubmitted && count($errors) == 0) {
                    $db->exec("SET CHARACTER SET utf8");
                    $id = $db->quote($id);
                    $nom = $db->quote($lastname);
                    $prenom = $db->quote($firstname);
                    $anniv = $db->quote($birthdate);
                    $tel = $db->quote($phone);
                    $email = $db->quote($mail);
                    $modifRdv = $dateRdv. ' '. $hourRdv. ':00';
                    $modifRdv = $db->quote($modifRdv);
                    
                    //insertion après modification
                    $query2 = "UPDATE `appointments` SET `dateHour`= $modifRdv WHERE `idPatients`= CONVERT($id, SIGNED INTEGER)";
                    //si erreur :
                    $nbLignes = $db->exec($query2);
                    if ($nbLignes != 1) {
                        $msg_error = $db->errorInfo();
                        echo $db->errorCode() . ' /// '. $msg_error[2];
                       
                    }
                     sleep(3); // attends 3 secondes
                    header('Location: index.php'); // redirige vers cible.php 
                    exit();
                } else {
                    foreach ($usersList as $user) {
                        $dateRDV = explode(' ', $user['dateHour']);
                      
                        
                  ?>
                        <form method="post" action="#" novalidate>
                            <div name="bloc1">
                                <fieldset>
                                    <legend>Ses coordonnées</legend>
                                    <div class="form-group">
                                        <label for="lastname">Nom : </label>
                                        <input type="text" class="form-control" name="lastname" id="lastname" placeholder="KIROUL" value="<?= $user['lastname'] ?>">
                                        <span class="text-danger"><?= ($errors['lastname']) ?? '' ?></span>
                                    </div>
                                    <div class="form-group">
                                        <label for="firstname">Prénom : </label>
                                        <input type="text" class="form-control" name="firstname" id="firstname" placeholder="Pierre" value="<?= $user['firstname'] ?>">
                                        <span class="text-danger"><?= ($errors['firstname']) ?? '' ?></span>
                                    </div>
                                    <div class="form-group">
                                        <label for="birthdate">Date de naissance : </label>
                                        <input type="date" class="form-control" name="birthdate" id="birthdate" min="1900-01-01" max="2020-01-01" value="<?= $user['birthdate'] ?>">
                                        <span class="text-danger"><?= ($errors['birthdate']) ?? '' ?></span>
                                    </div>
                                    <div class="form-group">
                                        <label for="phone">Téléphone : </label>
                                        <input type="tel" class="form-control" name="phone" id="phone" placeholder="06.22.95.01.02" value="<?= $user['phone'] ?>">
                                        <span class="text-danger"><?= ($errors['phone']) ?? '' ?></span>
                                    </div>
                                    <div class="form-group">
                                        <label for="mail">Email : </label>
                                        <input type="email" class="form-control" name="mail" id="mail" placeholder="pierre.kiroul@lamanu.fr" value="<?= $user['mail'] ?>">
                                        <span class="text-danger"><?= ($errors['mail']) ?? '' ?></span>
                                    </div>
                                    <div class="form-group">
                                        <label for="dateRdv">Date du rendez-vous : </label>
                                        <input type="date" class="form-control" name="dateRdv" id="dateRdv" min="1900-01-01" max="2020-01-01" value="<?= $dateRDV[0] ?>">
                                        <span class="text-danger"><?= ($errors['dateRdv']) ?? '' ?></span>
                                    </div>
                                    <div class="form-group">
                                        <label for="hourRdv">Heure du rendez-vous : </label>
                                        <input type="text" class="form-control" name="hourRdv" id="hourRdv" min="09:00" max="19:00" placeholder="00:00" value="<?= substr($dateRDV[1],0,5) ?>">
                                        <span class="text-danger"><?= ($errors['hourRdv']) ?? '' ?></span>
                                    </div>
                                    <input type="submit"  name="submit" value="Modifiez" />
                                    <input type="reset"  name="reset" value="Effacez" />
                                </fieldset>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <p></p>
            <p><a href="liste-patient.php">Liste des patients</a></p>
            <?php
        } // fin du foreach
    }//fin else du rechargement de page
}   //
// footer
include 'footer.php';
?>