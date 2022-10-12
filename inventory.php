<?php 
    session_start(); 
    if(array_key_exists('buttonDeconnecter', $_POST)) {
        session_destroy();
        header('Location: index.php');
    }

    if(empty($_SESSION['idUser']))
    {
        echo '<script>window.location.href = "login.php";</script>';
    }
?>

<head>
    <title>Inventaire</title>
    <meta charset="utf-8">
    <style>
        <?php require 'styles/inventory.css'; ?>
        <?php require 'styles/must-have.css'; ?>
        <?php require 'styles/ui-kit.css'; ?>
        <?php require 'scripts/body-scripts.php'; ?>
        <?php require 'scripts/db.php'; ?>
    </style>
    <?php RenderFavicon(); ?>
</head>

<?php

?>

<body> 
    <div class="header-banner">
        <a href="index.php"><?php echo file_get_contents("utilities/foodbook-logo.svg"); ?></a>
        <div class="banner-title"> Inventaire </div>
        <div class="svg-wrapper">
            <a href="login.php" class="svg-button list-button"> <?php echo file_get_contents("utilities/list.svg"); ?> </a>
            <a href="inventory.php" class="svg-button inventory-button"> <?php echo file_get_contents("utilities/food.svg"); ?> </a>
            <?php 
                if(!empty($_SESSION['idUser'])){
                    echo '<a href="edit-profil.php" class="svg-button login-button"> '.file_get_contents("utilities/account.svg").'</a>';
                    echo '<form method="post"><button type="submit" name="buttonDeconnecter" class="svg-button login-button" value="buttonDeconnecter" />'.file_get_contents("utilities/logout.svg").'</form>';
                }
                else{
                    echo '<a href="login.php" class="svg-button login-button"> '.file_get_contents("utilities/account.svg").'</a>';
                }
            ?>
        </div>
    </div> 

    <div class="wrapper">
        <div class="inventory-wrapper">
            <?php 
                if(!($_SERVER['REQUEST_METHOD'] === 'POST')){
                    echo '
                    <script>
                        window.onload = () => { document.getElementById("add_new_location").style.display = "block"; }
                    </script>';
                    $tabInfoSpace = InfoLocation(); 
                    $numInfoSpace = count($tabInfoSpace);
                    //Vérfie la quantité d'emplacements de l'utilisateur, et affiche un message
                    // lorsque ce nombre est <= 0
                    if ($numInfoSpace <= 0){
                        echo '
                        <script>
                            window.onload = () => { document.getElementById("error_no_space").style.display = "block"; }
                        </script>';
                    }
                    else {
                        echo '
                        <form method="post">
                            <div class="space-grid">';
                            foreach($tabInfoSpace as $space){
                                echo "<button class='space-div' type='submit' name='buttonSpace' value='$space[0]'>
                                    $space[1]
                                    <div class='space-div-arrow'>". file_get_contents("utilities/caret.svg") ."</div>
                                </button>";
                            }
                        echo '</div>
                        </form>';
                    }
                }else if(!empty($_POST['addLocation'])) {
                    $tabInfoSpace = InfoLocation(); 
                    $newLocation = $_POST['location-name'];
                    $locationAlreadyExists = false;

                    foreach ($tabInfoSpace as $location) {
                        if (strtolower($location[1]) == strtolower($newLocation)) {
                            $locationAlreadyExists = true;
                            break;
                        }
                    }

                    if (!$locationAlreadyExists) {
                        echo "emplacement ajouté!";
                        // Pour le deuxième paramètre de la méthode AddLocation, laisser vide pour l'instant (pas de svg encore)
                        //AddLocation($newLocation, "");
                        ChangePage("inventory.php");
                    }
                    else {
                        echo "Vous avez déjà un emplacement nommé ainsi, il n'a donc pas été ajouté.";
                        echo '<form><div class="item-wrapper"><div class="return-button">'.GenerateButtonTertiary("Retour", "inventory.php").'</div></form>';
                    }
                }else if(!empty($_POST['qteChosen'])){
                    echo ModifyIngredientInventory(intval($_SESSION['idUser']),intval($_POST['idIngredient']),intval($_POST['qteChosen']),intval($_POST['idEmplacement']));
                    echo "<script>window.location.href = window.location.href;</script>";
                }else if(!empty($_POST['ingredient-input'])){
                    AddIngredientInventory(intval($_SESSION['idUser']),intval($_POST['ingredient-input']),intval($_POST['number-input']),intval($_POST['place-input']));
                    echo "<script>window.location.href = window.location.href;</script>";
                }else if(!empty($_POST['buttonSpace'])){
                    $spaceChosen = $_POST['buttonSpace'];
                    $tabInventaire = UserInventoryInfo($_SESSION['idUser']);
                    echo '<form><div class="item-wrapper"><div class="return-button">'.GenerateButtonTertiary("Retour", "inventory.php").'</div></form>';
                    echo "<div class='button button-primary' onclick='ShowFormItems()'>Ajouter un ingredient</div>";
                    echo '<ul>';
                    foreach($tabInventaire as $ingredientInventaire){
                        $ingredientInfo = SingleIngredientInfo($ingredientInventaire[2]);
                        if($ingredientInventaire[3] == $_POST['buttonSpace']){
                            echo "<li>$ingredientInfo[1] <form  method='post'> <input type='number' name='qteChosen' min='1' value='$ingredientInventaire[0]'><input type='hidden' name='idIngredient' value='$ingredientInventaire[2]'><input type='hidden' name='idEmplacement' value='$spaceChosen'><button type='submit'>Modifier</button></form></li>";
                        }
                    }
                    echo '</ul>';
                }
            ?>
            <div class="neutral_message" id="error_no_location">Pour visionner et classer vos items, veuillez créer un emplacement.</div>
            <div class="neutral_message" id="error_no_space">Vous n'avez pas d'emplacement pour le moment.</div>
            <div class='add-new-location' id="add_new_location" onclick='ShowFormEmplacement()'>Ajouter un emplacement</div>
            <div class="inventory-form" id="inventory-location-form">
                <div class="transparent-background">
                    <form method="post" class="form-content">
                        <div class="form-exit" onclick='HideFormEmplacement()'> <?php echo file_get_contents("utilities/x-symbol.svg"); ?> </div>
                        <div class="infos-emplacement">Vous avez actuellement <?php echo $numInfoSpace; ?>/20 emplacements</div>
                        <input type="text" class="searchbar-input" name="location-name" placeholder="Nom de l'emplacement" maxlength="30">
                        <input type="submit" class="button button-primary" name="addLocation" value="Ajouter l'emplacement">
                    </form>
                </div>
            </div>

            <div class="inventory-form" id="inventory-items-form">
                <div class="transparent-background">
                    <div class="items-form-content">
                        <div class="form-exit" onclick='HideFormItems()'> <?php echo file_get_contents("utilities/x-symbol.svg"); ?> </div>

                        <?php
                            $tabIngredient = AllIngredientInfo(); // [1] == nom
                            echo '<div>';
                            foreach($tabIngredient as $singleIngredient){
                                echo "<div>";
                                echo '<form method="post">';
                                echo "<button type='submit' name='ingredient-input' value=$singleIngredient[0]>$singleIngredient[1]</button><br>";
                                echo '<input type="number" name="number-input" min="1" max="100" placeholder="Cb" value = 0>';
                                echo "<input type='hidden' name='place-input' value=$idEmplacement>";
                                echo '</form>';
                                echo '</div>';
                            }
                            echo '</div>';
                        ?>
                        <!-- <form>
                            <div class="item-wrapper dark-background">
                                <a href="add-new-ingredient.php" class='button button-secondary'>Ajouter un nouvel ingredient</a>
                            </div>
                        </form> -->

                    </div>    
                </div>
            </div>


        </div>
    </div>

    <?php GenerateFooter(); ?>
</body>

<script>
    function ShowFormEmplacement() {
        document.getElementById("inventory-location-form").style.display = "block";
    }

    function HideFormEmplacement() {
        document.getElementById("inventory-location-form").style.display = "none";
    }

    function ShowFormItems() {
        document.getElementById("inventory-items-form").style.display = "block";
    }

    function HideFormItems() {
        document.getElementById("inventory-items-form").style.display = "none";
    }
</script>