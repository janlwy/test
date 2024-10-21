
<?php
// Informations de connexion à la base de données
$host = "localhost";
$dbname = "cda_projet";
$username = "root";
$password = "";


// On vérifie que le visiteur a correctement envoyé le formulaire
if (isset($_POST['creation']) && $_POST['creation'] == 'Creation') {
   // On teste l'existence des variables et on vérifie qu'elle ne sont pas vides
   if ((isset($_POST['pseudo']) && !empty($_POST['pseudo'])) && (isset($_POST['mdp1']) && !empty($_POST['mdp1']))
      && (isset($_POST['mdp2']) && !empty($_POST['mdp2']))
   ) {
      // Si les variables existent, on vérifie que les deux mots de passe sont différents
      if ($_POST['mdp1'] != $_POST['mdp2']) {
         $erreur = 'Les 2 password sont differents.';
         echo $erreur;
         echo "<br/><a href='createUserForm.php'>Retour au formulaire</a>";
         exit();
      } else {
         // Si les deux mots de passe sont identiques, on se connecte à la bdd avec PDO
         try {
            $connexion = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // On parcourt la bdd et on range les éventuels login identiques existants dans un tableau
            $sql = 'SELECT count(*) FROM users WHERE id=:pseudo';
            $req = $connexion->prepare($sql);
            $req->bindValue(':pseudo', $_POST['pseudo'], PDO::PARAM_STR);
            $req->execute();
            $data = $req->fetch(PDO::FETCH_NUM);
            // Si aucun autre login identique existe, on inscrit ce nouveau login
            if ($data[0] == 0) {
               $sql = 'INSERT INTO users (id, md5) VALUES( :pseudo, :mdp1)';
               $req = $connexion->prepare($sql);
               $req->bindValue(':pseudo', $_POST['pseudo'], PDO::PARAM_STR);
               $req->bindValue(':mdp1', md5($_POST['mdp1']), PDO::PARAM_STR);
               $req->execute();
               $erreur = 'inscription reussie !';
               echo $erreur;
               echo "<br/><a href='connectForm.php'>Retour connexion</a>";
               exit();
            }
            // Sinon on n'inscrit pas ce login
            else {
               $erreur = 'Echec de l\'inscription !<br/>Un membre possede deja ce pseudo !';
               echo $erreur;
               echo "<br/><a href='createUserForm.php'>Retour au formulaire</a>";
               exit();
            }
         } catch (PDOException $e) {
            echo "Erreur de connexion : " . $e->getMessage();
            exit();
         }
      }
   }
   // Si au moins un des champs est vide
   else {
      $erreur = 'Echec de l\'inscription !<br/>Au moins un des champs est vide !';
      echo $erreur;
      echo "<br/><a href='createUserForm.php'>Retour au formulaire</a>";
      exit();
   }
}
