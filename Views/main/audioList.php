<section class="audio-section">


    <?php if (isset($_SESSION['message'])): ?>
        <div class="success-message">
            <?php echo htmlspecialchars($_SESSION['message'], ENT_QUOTES, 'UTF-8'); ?>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['erreur'])): ?>
        <div class="error-message">
            <?php echo htmlspecialchars($_SESSION['erreur'], ENT_QUOTES, 'UTF-8'); ?>
        </div>
        <?php 
            error_log("Erreur affichée dans audioList: " . $_SESSION['erreur']);
            unset($_SESSION['erreur']); 
        ?>
    <?php endif; ?>
    <br><br>
    <div class="boutonAligne">
        <a href="?url=compte/index#form-add" class="btnBase theme">
            <i class="iconColor material-icons md-36">library_add</i>
            <span class="spanIconText">Ajouter</span>
        </a>
        <a href="?url=audio/player" class="btnBase theme">
            <i class="iconColor material-icons md-36">radio</i>
            <span class="spanIconText">Lecteur</span>
        </a>
        <a href="?url=compte/index#form-add" class="btnBase theme">
            <i class="iconColor material-icons md-36">mic</i>
            <span class="spanIconText">Enregistrer</span>
        </a>
    </div>

    <?php echo $audioList; ?>

    <form id="selection-form" action="?url=audio/saveSelection" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo $session->get('csrf_token'); ?>">
        <div class="button-container">
            <button type="submit" class="btnBase orange" id="play-selected">
                <i class="material-icons">play_arrow</i> Lire la sélection
            </button>
        </div>
    </form>
</section>
