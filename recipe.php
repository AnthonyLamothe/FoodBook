<?php 
    session_start(); 
    if(array_key_exists('buttonDeconnecter', $_POST)) {
        session_destroy();
        header('Location: index.php');
    }

?>

<head>
    <title> Recette Foodbook</title>
    
    <meta charset="utf-8" name="viewport" content="width=device-width" />
    
    <style>
        <?php require 'styles/recipe.css'; ?>
        <?php require 'scripts/db.php'; ?>
        <?php require 'styles/must-have.css'; ?>
        <?php require 'scripts/body-scripts.php'; ?>
    </style>
    <?php 
        if(!($_SERVER['REQUEST_METHOD'] === 'POST')){
            $recette =  ShowSingleRecipe($_GET['id'])[0];
            $infoRecette = InfoRecipeByID($recette[0])[0];
        } 
        else{
            $recette = ShowSingleRecipe($_POST['id'])[0];
            $infoRecette = InfoRecipeByID($recette[0])[0];
        } 
    ?>
    <?php RenderFavicon(); ?>
</head>

<bod>
    <div class="header-banner">
        <a href="index.php"><?php echo file_get_contents("utilities/foodbook-logo.svg"); ?></a>
        <div class="banner-title"> <?php echo $recette[2];?> </div>

        <div class="searchbar">
            <input type="text" class="searchbar-input" placeholder="type something"></input>
            <div class="search-icon"><?php echo file_get_contents("utilities/search.svg"); ?></div>
        </div>

        <div class="svg-wrapper">
            <a href="personal-recipes.php" class="svg-button list-button"> <?php echo file_get_contents("utilities/book.svg"); ?> </a>
            <a href="groceries-list.php" class="svg-button list-button"> <?php echo file_get_contents("utilities/list.svg"); ?> </a>
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
        <div class="recipe-container">
            <div class="recipe-header">
                <div class="recipe-image">
                    <img src='<?=$infoRecette[5]?>' title='<?=$recette[2]?>'>
                </div>
        
                <div class="recipe-title">
                    <?= $recette[2] ?>
                </div>
            </div>
            <div class="recipe-ingredients">
                <div class="checkboxes-container">
                    <!-- <div>Ingrédients : </div>
                    <input class="checkbox" type="checkbox" id="ingrédient1" name="ingrédient1" value="ingrédient1">
                    <label for="ingrédient1"> ingrédient 1</label><br>
                    <input class="checkbox" type="checkbox" id="ingrédient2" name="ingrédient2" value="ingrédient2">
                    <label for="ingrédient2">ingrédient 2</label><br>
                    <input class="checkbox" type="checkbox" id="ingrédient3" name="ingrédient3" value="ingrédient3">
                    <label for="ingrédient3" class="strike-through"> ingrédient 3</label><br> -->
    
                    <?php
                        // $nbIngredients <= 0
                        if (true) {
                            echo '<div class="neutral-message" style="display: flex"> Vous n\'avez présentement aucun ingrédient dans votre recette </div>';
                        }
                        else {
                            // add all ingredients already added here
                        }
                    ?>
    
                    <!-- template pour un ingrédient -->
                    <div class="recipe-ingredient">
                        <div class="recipe-remove-item"> <?php echo file_get_contents("utilities/x-symbol.svg"); ?> </div>
                        <div>1/2 tasse</div>
                        <div>Ingrédient</div>
                    </div>
                    <?php 
                        if($_SESSION['idUser'] == $recette[1]) // || $_SESSION['idUser'] == table Admin
                            echo "<div class='button button-primary add-new-ingredient' id='add_new_ingredient' onclick='ShowFormAddNewIngredient()'>Ajouter un ingrédient</div>";
                    ?>
                </div>
            </div>
    
            <div class="recipe-steps">
                <div class="checkboxes-container">
                    <?php 
                        // $nbSteps <= 0
                        if (true) {
                            echo '<div class="neutral-message" style="display: flex"> Vous n\'avez présentement aucune étape dans votre recette </div>';
                        }
                        else {
                            // add all steps already added here
                        }
                    ?>
                    <?php 
                        foreach($tabEtape as $etape){
                            echo "<div class='recipe-step'>";
                            if($_SESSION['idUser'] == $recette[1])
                            {
                                echo "<div class='recipe-remove-item'>file_get_contents('utilities/x-symbol.svg')</div>";
                            }
                            echo "<div>Étape</div></div>";
                        }
                    ?>
                    <!-- template pour une étape -->
                    <?php 
                        if($_SESSION['idUser'] == $recette[1])
                            echo "<div class='button button-primary add-new-step' id='add_new_step' onclick='ShowFormAddNewStep()'>Ajouter une étape</div>";
                    ?>
                </div>
            </div>   
    
            <a href="<?= $infoRecette[6] ?>" class="recipe-video">
                Vidéo tutoriel
            </a>
    
            <div class="recipe-save-like">
                <div class="interractible-svg">
                <?php echo file_get_contents("utilities/like.svg"); ?>
                </div>
                    
                <div class="interractible-svg">
                <?php echo file_get_contents("utilities/floppy-disk.svg"); ?>
                </div>
            </div>
    
            <div class="publisher-info">
                <?= $infoRecette[2] ?>
            </div>
    
            <div class="recipe-comments">
                Section commentaires

                <div>Username: le commentaire</div>
                <div>Username: le commentaire</div>
                <div>Username: le commentaire</div>
               

                <?php
                    $tabCommentaire = []; //Faire méthode pour aller chercher tous les commentaire d'une recette avec l'id de la recette
                
                    foreach($tabCommentaire as $commentaire)
                    {
                        //Faire afficher le nom de la personne qui a mis le commentaire et le commentaire lui-même (se fier au div ci-dessus)
                        
                    }
                    if($recette[1] != $_SESSION["idUser"]) //Vérifier que le user n'est pas le propriétaire de la recette visitée
                    {
                        echo '<div onclick="ShowFormAddComments()" class="button button-secondary">Ajouter un commentaire</div>';
                    }
                 ?>
            </div>

            <div class="neutral_message" id="comment-field-empty">Vous devez remplir le/les champs obligatoires.</div>
            <div class="comments-form" id="comments-form">
                <div class="transparent-background">
                    <form method="post" class="form-content">
                        <div class="form-exit" onclick='HideFormAddComments()'> <?php echo file_get_contents("utilities/x-symbol.svg"); ?> </div>
                        <?php 
                                echo '
                                    <input type="text" class="searchbar-input" name="comment-value" placeholder="Votre commentaire..." maxlength="100">
                                    <input type="submit" class="button button-primary" name="addComments" value="Ajouter">
                                ';
                        ?>
                    </form>
                </div>
            </div>

            <?php
                if(isset($_POST["addComments"]))
                {
                    if(empty($_POST["comment-value"]))
                    {
                        echo '<script>window.onload = () => { document.getElementById("comment-field-empty").style.display = "block"; }</script>';
                    }
                    else{
                        //Appeler méthode AddComments(idRecette, idUser, leCommetnaire);
                        //Revenir à la page
                    }
                }
            ?>
        </div>
    </div>
    
    <?php GenerateFooter(); ?>
</body>

<script defer>
    function ShowFormAddComments()
    {
        document.getElementById("comments-form").style.display = "block";
    }

    function HideFormAddComments()
    {
        document.getElementById("comments-form").style.display = "none";
    }

</script>