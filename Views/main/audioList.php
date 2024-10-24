 <section>
     <span class="br"></span>
     <ul id="audio-list">
         <?php foreach ($audios as $audio): ?>
             <li data-id="<?php echo $audio['id']; ?>"><?php echo $audio['title']; ?> - <?php echo $audio['artist']; ?></li>
         <?php endforeach; ?>
     </ul>
     <script>
         var audios = <?php echo json_encode($audios); ?>;
     </script>
     <button id="play-selected">Lire la sélection</button>
 </section>
