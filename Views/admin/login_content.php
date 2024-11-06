<?php
$error = $datas['error'] ?? '';
$datas['hideNav'] = true; // Pour masquer la navigation standard
$datas['isAdmin'] = true; // Pour ajuster les chemins CSS
?>

<div class="formContainer">
    <h2>Connexion Administration</h2>
    
    <?php if ($error): ?>
        <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="post" class="formCentered">
        <div class="rowEspace">
            <div class="collabel">
                <label>Nom d'utilisateur</label>
            </div>
            <div class="colinput">
                <input type="text" name="username" class="inputMonEspace" required>
            </div>
        </div>
        
        <div class="rowEspace">
            <div class="collabel">
                <label>Mot de passe</label>
            </div>
            <div class="colinput">
                <input type="password" name="password" class="inputMonEspace" required>
            </div>
        </div>

        <div class="rowEspace" style="margin-top: 20px;">
            <div class="colinput">
                <button type="submit" class="btnBase blue">Se connecter</button>
            </div>
        </div>
    </form>
</div>
