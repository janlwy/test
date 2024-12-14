<section>
    <?php if (empty($textes)): ?>
        <div class="info-message">
            Aucun texte sélectionné. Veuillez retourner à la liste et sélectionner des textes à lire.
        </div>
        <a href="?url=texte/list" class="btnBase theme">Retour à la liste</a>
    <?php else: ?>
        <?php if (isset($_SESSION['erreur'])): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($_SESSION['erreur']); ?>
                <?php unset($_SESSION['erreur']); ?>
            </div>
        <?php endif; ?>

        <div id="texte-viewer" class="texte-viewer">
            <div class="texte-container">
                <h2 id="texte-title"></h2>
                <div id="texte-content" class="texte-content"></div>
                <div class="texte-metadata">
                    <p>Créé le : <span id="texte-date"></span></p>
                </div>
            </div>
            
            <div class="texte-navigation">
                <button class="prev-texte">
                    <i class="material-icons md-36">navigate_before</i>
                </button>
                <button class="next-texte">
                    <i class="material-icons md-36">navigate_next</i>
                </button>
            </div>

            <div class="texte-list">
                <?php foreach ($textes as $texte): ?>
                    <div class="texte-item" 
                         data-id="<?php echo $texte->getId(); ?>"
                         data-title="<?php echo htmlspecialchars($texte->getTitle()); ?>"
                         data-content="<?php echo htmlspecialchars($texte->getContent()); ?>"
                         data-date="<?php echo date('d/m/Y', strtotime($texte->getCreatedAt())); ?>">
                        <h4><?php echo htmlspecialchars($texte->getTitle()); ?></h4>
                        <p><?php echo nl2br(htmlspecialchars(substr($texte->getContent(), 0, 100))); ?>...</p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="boutonAligne">
            <a href="?url=texte/list" class="btnBase theme">
                <i class="iconColor material-icons md-36">undo</i>
            </a>
            <a href="?url=compte/index#form-add" class="btnBase theme">
                <i class="iconColor material-icons md-36">note_add</i>
            </a>
        </div>
    <?php endif; ?>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const viewer = document.getElementById('texte-viewer');
    const texteTitle = document.getElementById('texte-title');
    const texteContent = document.getElementById('texte-content');
    const texteDate = document.getElementById('texte-date');
    const texteItems = document.querySelectorAll('.texte-item');
    const prevButton = document.querySelector('.prev-texte');
    const nextButton = document.querySelector('.next-texte');
    
    let currentIndex = 0;
    
    function showTexte(index) {
        const texte = texteItems[index];
        texteTitle.textContent = texte.dataset.title;
        texteContent.innerHTML = texte.dataset.content.replace(/\n/g, '<br>');
        texteDate.textContent = texte.dataset.date;
        
        texteItems.forEach(item => item.classList.remove('active'));
        texte.classList.add('active');
    }
    
    if (texteItems.length > 0) {
        showTexte(0);
        
        texteItems.forEach((item, index) => {
            item.addEventListener('click', () => {
                currentIndex = index;
                showTexte(currentIndex);
            });
        });
        
        prevButton.addEventListener('click', () => {
            currentIndex = (currentIndex - 1 + texteItems.length) % texteItems.length;
            showTexte(currentIndex);
        });
        
        nextButton.addEventListener('click', () => {
            currentIndex = (currentIndex + 1) % texteItems.length;
            showTexte(currentIndex);
        });
        
        document.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowLeft') {
                prevButton.click();
            } else if (e.key === 'ArrowRight') {
                nextButton.click();
            }
        });
    }
});
</script>
