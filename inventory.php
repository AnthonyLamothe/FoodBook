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
    <meta charset="utf-8" name="viewport" content="width=device-width" />
    <style>
        <?php require 'styles/inventory.css'; ?>
        <?php require 'styles/must-have.css'; ?>
        <?php require 'scripts/body-scripts.php'; ?>
        <?php require 'scripts/db.php'; ?>
        <?php require 'scripts/filter.php'; ?>
    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <?php RenderFavicon(); ?>
</head>

<div> 
    <?php 
        if($_SERVER['REQUEST_METHOD'] == 'POST')
            AddAnimation();
    ?>
    <div class="header-banner hide-mobile">
        <a href="index.php"><?php echo file_get_contents("utilities/foodbook-logo.svg"); ?></a>
        <?php AddSearchBar(); ?>
        <div class="svg-wrapper">
            <a href="personal-recipes.php" class="svg-button list-button"> <?php echo file_get_contents("utilities/book.svg"); ?> </a>
            <a href="groceries-list.php" class="svg-button list-button"> <?php echo file_get_contents("utilities/list.svg"); ?> </a>
            <a href="inventory.php" class="svg-button inventory-button"> <?php echo file_get_contents("utilities/food.svg"); ?> </a>
            <?php 
                if(!empty($_SESSION['idUser'])){
                    echo '<a href="edit-profil.php" class="svg-button login-button"> '.file_get_contents("utilities/account.svg").'</a>';
                    echo '<form method="post"><button type="submit" name="buttonDeconnecter" class="svg-button login-button logout-button" value="buttonDeconnecter" />'.file_get_contents("utilities/logout.svg").'</form>';
                }
                else{
                    echo '<a href="login.php" class="svg-button login-button"> '.file_get_contents("utilities/account.svg").'</a>';
                }
            ?>
        </div>
    </div>

    <!-- mobile header -->
    <div class="header-mobile-banner hide-desktop">
        <a href="index.php"><?php echo file_get_contents("utilities/foodbook-logo.svg"); ?></a>
        <button class="menu-icon" onclick="ShowMenu()"><?php echo file_get_contents("utilities/menu.svg"); ?></button>
    </div>

    <div class="mobile-popup-menu hide-desktop" id="mobile-popup-menu">
        <div class="mobile-svg-wrapper hide-desktop">
            <?php AddSearchBar(); ?>

            <a href="personal-recipes.php" class="svg-button list-button"> 
                <?php echo file_get_contents("utilities/book.svg"); ?> 
                <span class="header-text">Vos recettes</span>
            </a>
            <a href="groceries-list.php" class="svg-button list-button"> 
                <?php echo file_get_contents("utilities/list.svg"); ?> 
                <span class="header-text">Liste d'épicerie</span>
            </a>
            <a href="inventory.php" class="svg-button inventory-button"> 
                <?php echo file_get_contents("utilities/food.svg"); ?> 
                <span class="header-text">Inventaire</span>
            </a>
            <div class="form-exit" onclick='HideMenu()'> <?php echo file_get_contents("utilities/x-symbol.svg"); ?> </div>
            <?php 
                if(!empty($_SESSION['idUser'])){
                    echo '<a href="edit-profil.php" class="svg-button login-button"> 
                        '.file_get_contents("utilities/account.svg");
                        echo "
                        <span class='header-text'>" . User($_SESSION['idUser'])[2] . " " . User($_SESSION['idUser'])[1] . "</span>
                    </a>";
                    echo '<form method="post">
                    <button type="submit" name="buttonDeconnecter" class="svg-button login-button logout-button" value="buttonDeconnecter" />
                        '.file_get_contents("utilities/logout.svg").'
                        <span class="header-text">Se déconnecter</span>
                    </form>';
                }
                else{
                    echo '<a href="login.php" class="svg-button login-button logout-button"> 
                    '.file_get_contents("utilities/account.svg").'
                    <span class="header-text">Se connecter</span>
                    </a>';
                }
            ?>
        </div>
    </div>
</div>

<body> 

        <div class="wrapper">
            <?php 
                if(!($_SERVER['REQUEST_METHOD'] == 'POST')){
                    $tabInfoSpace = InfoLocation($_SESSION['idUser']); 
                    $numInfoSpace = count($tabInfoSpace);
                    if ($numInfoSpace <= 0){
                        echo '
                        <script>
                            window.onload = () => { 
                                document.getElementById("add_new_location").style.display = "block"; 
                                document.getElementById("error_no_space").style.display = "block";
                                document.getElementById("add_new_location").style.display = "block";
                            }
                        </script>';
                    }
                    else {
                        echo '
                            <script>
                                window.onload = () => { document.getElementById("add_new_location").style.display = "block"; }
                            </script>';
                        echo '
                        <form method="post" class="inventory-space-form">
                            <div class="space-grid">';
                            foreach($tabInfoSpace as $space){
                                echo "<button class='space-div' type='submit' name='buttonSpace' value='$space[0]'> $space[1] <div class='space-div-arrow'>". file_get_contents("utilities/caret.svg") ."</div> </button>";
                            }
                        echo '</div>
                        </form>';
                    }
                }else if(!empty($_POST['option-delete-place'])){
                    DeleteLocation(intval($_POST['idEmplacementDelete']));
                    ChangePage("inventory.php");
                }else if(!empty($_POST['option-delete'])){
                    DeleteIngredientInventory(intval($_SESSION['idUser']),intval($_POST['idIngredientDelete']),intval($_POST['idEmplacementDelete']));
                    ChangePage("inventory.php");
                }else if(!empty($_POST['qteChosen'])){
                    echo ModifyIngredientInventory(intval($_SESSION['idUser']),intval($_POST['idIngredient']),intval($_POST['qteChosen']),intval($_POST['idEmplacement']));
                    echo "<script>window.location.href = window.location.href;</script>";
                }else if(!empty($_POST['ingredient-input'])){
                    AddIngredientInventory(intval($_SESSION['idUser']),intval($_POST['ingredient-input']),intval($_POST['number-input']),intval($_POST['place-input']));
                    echo "<script>window.location.href = window.location.href;</script>";
                }else if(!empty($_POST['buttonSpace'])){
                    $spaceChosen = $_POST['buttonSpace'];
                    $tabInventaire = UserInventoryInfo($_SESSION['idUser']);
                    echo '<form><div class="item-wrapper"><div class="return-button">'.GenerateButtonTertiary("Retourner aux emplacements", "inventory.php").'</div></form>';
                    echo "<div class='button button-primary form-ingredient-button' onclick='ShowFormItems()'>Ajouter un ingrédient</div>";
                    echo '<table class="form-ingredient-wrapper">';
                    $nbIngredient = 0;
                    foreach($tabInventaire as $ingredientInventaire){
                        $ingredientInfo = SingleIngredientInfo($ingredientInventaire[2]);
                        if($ingredientInventaire[3] == $_POST['buttonSpace']){
                            $nbIngredient = $nbIngredient + 1;
                            echo "<tr> 
                                <td class='table-name'>$ingredientInfo[1] </td>
                                <form  method='post' class='form-ingredient-option'> 
                                    <td class='table-number'><input type='number' name='qteChosen' min='1' value='$ingredientInventaire[0]'></td>
                                    <input type='hidden' name='idIngredient' value='$ingredientInventaire[2]'>
                                    <input type='hidden' name='idEmplacement' value='$spaceChosen'>
                                    <td class='table-modify'><button type='submit' class='modify-button'>" . file_get_contents('utilities/notebook.svg') . "</button></td>
                                </form>
                                <form method='post' class='form-ingredient-option'>
                                   <td class='table-remove'> <button type='submit' class='x-button' name='option-delete' value='1'>" . file_get_contents('utilities/x-symbol.svg') . "</button></td>
                                    <input type='hidden' name='idIngredientDelete' value='$ingredientInventaire[2]'>
                                    <input type='hidden' name='idEmplacementDelete' value='$spaceChosen'>
                                </form>
                                </tr>";
                        }
                    }
                    echo '</table>';
                    if($nbIngredient == 0){
                        echo "
                            <form method='post'>
                                <button type='submit' class='danger-option' name='option-delete-place' value='1'>Retirer cet emplacement</button>
                                <input type='hidden' name='idEmplacementDelete' value='$spaceChosen'>
                            </form>";
                    }
                }else if(!empty($_POST['addLocation'])) {
                    $tabInfoSpace = InfoLocation($_SESSION['idUser']); 
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
                        AddLocation($newLocation,'',$_SESSION['idUser']);
                        ChangePage("inventory.php");
                    }
                    else {
                        echo "Vous avez déjà un emplacement nommé ainsi, il n'a donc pas été ajouté.";
                        echo '<form><div class="item-wrapper"><div class="return-button">'.GenerateButtonTertiary("Retour", "inventory.php").'</div></form>';
                    }
                }
            ?>
            <div class="neutral_message" id="error_no_location">Pour visionner et classer vos items, veuillez créer un emplacement.</div>
            <div class="neutral_message" id="error_no_space">Vous n'avez pas d'emplacement pour le moment.</div>
            <div class='add-new-location' id="add_new_location" onclick='ShowFormEmplacement()'>Ajouter un emplacement</div>
        </div>
        <div class="inventory-form" id="inventory-location-form">
            <div class="transparent-background">
                <form method="post" class="form-content">
                    <div class="form-exit-inventory" onclick='HideFormEmplacement()'> <?php echo file_get_contents("utilities/x-symbol.svg"); ?> </div>
                    <div class="infos-emplacement">Vous avez actuellement <?php echo $numInfoSpace; ?>/10 emplacements</div>
                    <?php 
                        $tabInfoSpace = InfoLocation($_SESSION['idUser']); 
                        if(count($tabInfoSpace) < 10){
                            echo '
                                <input type="text" class="inventory-text-input" name="location-name" placeholder="Nom de l\'emplacement" maxlength="30">
                                <input type="submit" class="button button-primary" name="addLocation" value="Ajouter l\'emplacement">
                            ';
                        }
                    ?>
                </form>
            </div>
        </div>

        <div class="inventory-form" id="inventory-items-form">
                <div class="transparent-background">
                    <div class="items-form-content">
                        <div class="form-exit-add-new-item" onclick='HideFormItems()'> <?php echo file_get_contents("utilities/x-symbol.svg"); ?> </div>
                        <?php
                            // Formulaire de tri
                            if ($_POST['filter'])
                            echo '<script>document.getElementById("inventory-items-form").style.display = "block";</script>';
                            $idEmplacement = $_POST['buttonSpace'];
                            $tabTypeIngredient = TypeIngredientInfo();
                            $nameSearched = $_POST['name-input'];
                            echo "<form class='display-filter-section' method='post'>";
                            echo "<input class='inventory-text-input' name='name-input' type='text' placeholder='Nom ingredient' value=$nameSearched >";
                            echo '<select name="type-input">';
                            echo '<option value="">Tout les types</option>';
                            foreach ($tabTypeIngredient as $typeIngredient)
                                echo "<option value=$typeIngredient[0]>$typeIngredient[1]</option>";
                            echo '</select>';
                            echo '<select name="order-input">';
                            echo '<option value="ASC">Croissant</option>';
                            echo '<option value="DESC">Décroissant</option>';
                            echo '</select>';
                            echo '<input type="hidden" value="set" name="filter">';
                            echo "<input type='hidden' value='$idEmplacement' name='buttonSpace'>";
                            echo '<div class="buttons-wrapper"><input class="button button-primary" type="submit" value="Chercher"></div>';
                            echo "</form>";
                        ?>
                        <div class="items-form">
                            <?php
                            // Trier les informations
                            $tabIngredient = AllIngredientInfo($_POST['order-input']); // [1] == nom
                            $tabIngredient = FilterIngredient($tabIngredient, $_POST['name-input'], $_POST['type-input']);
                            //Aficher les informations
                            foreach ($tabIngredient as $singleIngredient) {
                                // Empeche un ingredient d'apparaitre dans la liste de choix si il existe deja dans la liste
                                $isAlreadyInList = false;
                                foreach ($tabInventaire as $infoInventaire) {
                                    if ($infoInventaire[3] == $singleIngredient[0]) {
                                        $isAlreadyInList = true;
                                    }
                                }
                                if (!$isAlreadyInList) {
                                    echo "
                                        <div class='inventory-item' onclick='ShowFormItemQuantity($singleIngredient[0])'> <span>$singleIngredient[1]</span> </div>
                                        <form method='post' class='inventory-item-form' id='inventory-item-form-$singleIngredient[0]'>
                                            <div class='items-form-overlay'>
                                                <div class='form-exit-item' onclick='HideFormItemQuantity($singleIngredient[0])'>";
                                    echo file_get_contents('utilities/x-symbol.svg');
                                    echo " </div>
                                                <span class='inventory-items-form-title'>Combien voulez vous ajouter de cet item : $singleIngredient[1] </span>
                                                <input type='number' name='number-input' min='1' max='100' placeholder='Cb' value = 0> <br>
                                                <input type='hidden' name='place-input' value='$idEmplacement'>
                                                <input type='hidden' value='$spaceChosen' name='buttonSpace'>
                                                <button type='submit' class='button button-secondary' name='ingredient-input' value='$singleIngredient[0]'>Ajouter</button><br>
                                            </div>
                                        </form>";
                                }
                            }
                            ?>
                        </div>
                        <div class="items-form-submit">
                            <form> <?php GenerateButtonPrimary("Ajouter un nouvel ingredient", "add-new-ingredient.php") ?></form>
                        </div>
                    </div>
                </div>
            </div>
    </div>
    <?php GenerateFooter(); ?>
</body>

<script>
    function ShowAddNewLocation(){
        document.getElementById("add_new_location").style.display = "block"
    }
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

    function ShowFormItemQuantity(id) {
        document.getElementById("inventory-item-form-" + id).style.display = "flex";
    }

    function HideFormItemQuantity(id) {
        document.getElementById("inventory-item-form-" + id).style.display = "none";
    }
    function ShowMenu() {
        document.getElementById("mobile-popup-menu").style.display = "flex";
    }

    function HideMenu() {
        document.getElementById("mobile-popup-menu").style.display = "none";
    }
</script>