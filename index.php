<?php
require_once 'config.php';
require_once 'library.php';
require_once 'templates/header.php';
?>

<div class="section" style="border-bottom: 1px solid #ccc;">
    <div class="container">
        <div class="mb-5">
            <h2 style="text-align: center;">Ricerca competenze</h2>
        </div>

        <div class="mb-3">
            <label for="searchterms" class="form-label">Ricerca libera del testo</label>
            <input type="text" class="form-control" id="searchterms" aria-describedby="searchterms_help">
            <div id="searchterms_help" class="form-text">Le parole indicate verranno cercate nel curriculum, negli abstract delle pubblicazioni e nelle parole
                chiavi dei docenti</div>
        </div>

        <p class="h2">Risultati</p>

        <ul class="list-group" id='researchers_list'>
        </ul>

    </div>
</div>

<script>
    $('#searchterms').on('input',searchterms_change_listener);
    $('#searchterms').trigger('input');
</script>
<?php
require_once("templates/footer.php");
?>
