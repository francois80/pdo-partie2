<?php
$exercice = 'Exercices';
include 'header.php';
//include de validation du formulaire
include 'form_validation.php';

if (empty($_GET['id']) && isset($_GET['id']) && empty($_GET['modif']) && isset($_GET['modif'])) {
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
    $afficheRDV = true;
    $db = connectDB();
    $db->exec("SET CHARACTER SET utf8");
    //$query = "SELECT * FROM `patients`, `appointments` WHERE `patients`.`id`=$id AND `appointments`.`idPatients` = $id;
    $query ="SELECT `lastname`, `firstname`,`birthdate`,`phone`, `mail`, `dateHour` from `patients`, `appointments` WHERE `patients`.`id` = $id AND `appointments`.`idPatients` = $id";
    $usersQueryState = $db->query($query);
    $usersList = $usersQueryState->fetchAll(PDO::FETCH_ASSOC);
    if(empty($usersList)){
        $query ="SELECT `lastname`, `firstname`,`birthdate`,`phone`, `mail` from `patients` WHERE `patients`.`id` = $id";
        $usersQueryState = $db->query($query);
        $usersList = $usersQueryState->fetchAll(PDO::FETCH_ASSOC);
        $afficheRDV = false;
    }
    ?>
    <h1>Profil du patient</h1>
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
                    //insertion après modification
                    $query = "UPDATE `patients` SET `lastname`=$nom, `firstname`=$prenom, `birthdate`=$anniv, `phone`=$tel, `mail`=$email WHERE `id`= CONVERT($id, SIGNED INTEGER)";
                    //si erreur :
                    $nbLignes = $db->exec($query);
                    if ($nbLignes != 1) {
                        $msg_error = $db->errorInfo();
                        echo $db->errorCode() . ' // ' . $msg_error[2];
                    }
                    sleep(3); // attends 3 secondes
                    header('Location: index.php'); // redirige vers cible.php 
                    exit();

                } else {
                    foreach ($usersList as $user) {
                        //si des rdv sont inscrits dans la base
                        //si on a des rendez vous dans la table, on découpe la chaine qui contient la date et les heures
                        if($afficheRDV){
                            $dateRDV = explode(' ', $user['dateHour']);
                        }
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
                                   <?php
                                   //Si on a des redez vous dans la table, on les affichent
                                   if($afficheRDV){ ?>
                                    <div class="form-group">
                                        <label for="dateRdv">Date du rendez-vous : </label>
                                        <input type="date" class="form-control" name="dateRdv" id="dateRdv" min="1900-01-01" max="2020-01-01" value="<?= $dateRDV[0] ?>" disabled="">
                                        <span class="text-danger"><?= ($errors['dateRdv']) ?? '' ?></span>
                                    </div>
                                    <div class="form-group">
                                        <label for="hourRdv">Heure du rendez-vous : </label>
                                        <input type="text" class="form-control" name="hourRdv" id="hourRdv" min="09:00" max="19:00" placeholder="00:00" value="<?= substr($dateRDV[1],0,5) ?>" disabled="">
                                        <span class="text-danger"><?= ($errors['hourRdv']) ?? '' ?></span>
                                    </div>
                                   <?php } ?>
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