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
$query = 'SELECT * FROM `patients`';
$usersQueryState = $db->query($query);
$usersList = $usersQueryState->fetchAll(PDO::FETCH_ASSOC);

if (isset($_GET['suppr']) && $_GET['suppr'] == '1'){
    $idSupprim = $_GET['id'];
    $QuerySup = "DELETE `patients`, `appointments` FROM `patients` LEFT JOIN `appointments` ON (`patients`.`id` = `appointments`.`idPatients`) WHERE `patients`.`id` = $idSupprim";
    $userDelete = $db->exec($QuerySup);
    $server = $_SERVER['PHP_SELF'];
    //rechargement de la page après effacement
    header("Refresh: 1;url= $server");
    
}

// Attempt search query execution
try{
    if(isset($_REQUEST["term"])){
        // create prepared statement
        $sql = "SELECT * FROM `patients` WHERE `lastname` LIKE :term";
        $stmt = $db->prepare($sql);
        $term = $_REQUEST["term"] . '%';
        // bind parameters to statement
        $stmt->bindParam(":term", $term);
        // execute the prepared statement
        $stmt->execute();
        if($stmt->rowCount() > 0){
            while($row = $stmt->fetch()){
                echo "<p>" . $row["lastname"] . "</p>";
            }
        } else{
            echo "<p>No matches found</p>";
        }
    }  
} catch(PDOException $e){
    die("ERROR: Could not able to execute $sql. " . $e->getMessage());
}
 
// Close statement
unset($stmt);
 
// Close connection
unset($db);

?>
<style type="text/css">
    body{
        font-family: Arail, sans-serif;
    }
    /* Formatting search box */
    .search-box{
        width: 300px;
        position: relative;
        display: inline-block;
        font-size: 14px;
    }
    .search-box input[type="search"]{
        height: 32px;
        padding: 5px 10px;
        border: 1px solid #CCCCCC;
        font-size: 14px;
    }
    .result{
        position: absolute;        
        z-index: 999;
        top: 100%;
        left: 0;
    }
    .search-box input[type="search"], .result{
        width: 100%;
        box-sizing: border-box;
    }
    /* Formatting result items */
    .result p{
        margin: 0;
        padding: 7px 10px;
        border: 1px solid #CCCCCC;
        border-top: none;
        cursor: pointer;
    }
    .result p:hover{
        background: #f2f2f2;
    }
</style>
<script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
    $('.search-box input[type="search"]').on("keyup input", function(){
        /* Get input value on change */
        var inputVal = $(this).val();
        var resultDropdown = $(this).siblings(".result");
        if(inputVal.length){
            $.get("<?= $_SERVER['PHP_SELF'] ?>", {term: inputVal}).done(function(data){
                // Display the returned data in browser
                resultDropdown.html(data);
            });
        } else{
            resultDropdown.empty();
        }
    });
    
    // Set search input value on click of result item
    $(document).on("click", ".result p", function(){
        $(this).parents(".search-box").find('input[type="search"]').val($(this).text());
        $(this).parent(".result").empty();
    });
});
</script>
<p></p>
<div class="container">
<form>
  <div class="search-box">
    <input type="search" id="maRecherche" autocomplete="off" name="q" placeholder="Rechercher sur le site…" size="30">
    <button>Rechercher</button>
    <div class="result"></div>
  </div>
</form>
</div>

<h1>Liste des patients</h1>
<div class="container">
<table class="tableau">
    <thead>
        <tr>
            <th>Id</th>
            <th>Nom</th>
            <th>Prénom</th>
            <th></th>
        </tr>
    </thead>
    <TBODY>
        <?php
        foreach ($usersList as $user): ?>
      <tr>
            
            <td><?= $user['id'] ?></td>
            <td><a href="profil-patient.php?id=<?= $user['id'] ?>"><?= $user['lastname'] ?></a></td>
            <td><?= $user['firstname'] ?></td>
            <td><a href="<?=$_SERVER['PHP_SELF'] ?>?id=<?= $user['id'] ?>&amp;suppr=1"><span class="rouge delete" id="del_<?= $user['id'] ?>">supprimer</span></a></td>
        </tr>
        <?php
    endforeach;
    ?>
    </TBODY>
</table>
    
</div>

<p></p>
<p><a href="ajout-patient.php">Ajouter un patient</a></p>
<?php 
// footer
include 'footer.php';
?>