<?php
require_once 'library.php';
require_once 'templates/header.php';
?>
                <div class="mb-5">
                    <h2 class="text-center">Ricerca competenze</h2>
                </div>

                <h3>Criteri di ricerca</h3>

                <div class="mb-5">
                    <label for="searchterms" class="form-label">Ricerca libera del testo</label>
                    <input type="text" class="form-control" id="searchterms" aria-describedby="searchterms_help">
                    <div id="searchterms_help" class="form-text">
                        Le parole indicate verranno cercate nel curriculum, negli abstract delle pubblicazioni e nelle parole chiavi dei docenti.
                    </div>
                </div>

                <h3>Risultati</h3>

                <ul class="list-group" id='researchers_list'>
                </ul>

                <script src="js/index.js"></script>
<?php
require_once("templates/footer.php");
?>
