<?php
$exercice = 'Exercice1';
include 'header.php';
//include de validation du formulaire
include 'form_validation.php';
?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 alert alert-secondary">
            <?php
            //si post du formulaire complété sans erreurs, affichage du résultat
            if ($isSubmitted && count($errors) == 0) {
                //connexion à la base de données
                function connectDB() {
                    require_once 'param.php';
                    $dsn = 'mysql:dbname=' . DB . ';host=' . HOST . ';';
                    try {
                        $db = new PDO($dsn, USER, PASS);
                        return $db;
                    } catch (Exception $ex) {
                        die('La connexion à la bdd a échoué !!');
                    }
                }
                //fin fonction de test de connexion puis connexion
              
                $db = connectDB();
                $db->exec("SET CHARACTER SET utf8");
                //$id='\N';
                $nom=$db->quote($lastname);
                $prenom=$db->quote($firstname);
                $anniv=$db->quote($birthdate);
                $tel=$db->quote($phone);
                $email=$db->quote($mail);
                
                $query="INSERT IGNORE INTO `patients` VALUES (NULL, $nom, $prenom, $anniv, $tel, $email)";
                //si erreur :
                $nbLignes=$db->exec($query);
                if($nbLignes !=1){
                    $msg_error=$db->errorInfo();
                    echo $db->errorCode(). ' '. $msg_error[2];
                }
               
                ?>
                <p>Nom : <mark><?= $firstname ?></mark></p>
                <p>Prénom : <mark><?= $lastname ?></mark></p>
                <p>Date de naissance : <mark><?= $birthdate ?></mark> (avant conversion)</p>
                <p>Date de naissance : <mark><?= strftime('%d-%m-%Y', strtotime($birthdate)) ?></mark> (après conversion)</p>
                <p>Téléphone : <mark><?= $phone ?></mark></p>
                <p>Email : <mark><?= $mail ?></mark></p>
                <p>Le nouveau patient à bien été enregistré</p>
                <p>Vous allez être redirigé vers la page d'accueil dans 3 secondes</p>
                <?php
                sleep(3); // attends 3 secondes
                header('Location: index.php'); // redirige vers cible.php 
                exit();
                ?>
                <?php
            } else {
                //affichage du formulare ou réaffichage si erreurs dans les saisies
                ?>
                <form method="post" action="#" novalidate>
                    <div name="bloc1">
                        <fieldset>
                            <legend>Vos coordonnées</legend>
                            <div class="form-group">
                                <label for="lastname">Nom : </label>
                                <input type="text" class="form-control" name="lastname" id="lastname" placeholder="KIROUL" value="<?= $lastname ?>">
                                <span class="text-danger"><?= ($errors['lastname']) ?? '' ?></span>
                            </div>
                            <div class="form-group">
                                <label for="firstname">Prénom : </label>
                                <input type="text" class="form-control" name="firstname" id="firstname" placeholder="Pierre" value="<?= $firstname ?>">
                                <span class="text-danger"><?= ($errors['firstname']) ?? '' ?></span>
                            </div>
                            <div class="form-group">
                                <label for="birthdate">Date de naissance : </label>
                                <input type="date" class="form-control" name="birthdate" id="birthdate" min="1900-01-01" max="2020-01-01" value="<?= $birthdate ?>">
                                <span class="text-danger"><?= ($errors['birthdate']) ?? '' ?></span>
                            </div>
                            <div class="form-group">
                                <label for="phone">Téléphone : </label>
                                <input type="tel" class="form-control" name="phone" id="phone" placeholder="06.22.95.01.02" value="<?= $phone ?>">
                                <span class="text-danger"><?= ($errors['phone']) ?? '' ?></span>
                            </div>
                            <div class="form-group">
                                <label for="mail">Email : </label>
                                <input type="email" class="form-control" name="mail" id="mail" placeholder="pierre.kiroul@lamanu.fr" value="<?= $mail ?>">
                                <span class="text-danger"><?= ($errors['mail']) ?? '' ?></span>
                            </div>
                            <input type="submit"  name="submit" value="Envoyez" />
                            <input type="reset"  name="reset" value="Effacez" />
                        </fieldset>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php } 
// footer
include 'footer.php';
?>
