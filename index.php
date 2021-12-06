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
                    <div id="searchterms_help" class="form-text mb-3">
                        Le parole indicate qua sopra verranno cercate nei dati inseriti in questa applicazione e nelle pubblicazioni registrate
                        su <a href="https://ricerca.unich.it">ricerca.unich.it</a>.
                        <span class="collapsed text-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#searchinfo" aria-expanded="false" aria-controls="#searchinfo">
                            <i class="fas fa-arrow-down text-expanded"></i>
                            <i class="fas fa-arrow-right text-collapsed"></i>
                        </span>
                        <div id="searchinfo" class="collapse">
                            <div class="mt-2">
                            Più in dettaglio, il risultato della ricerca è l'insieme del personale che ha <strong>almeno uno</strong> dei termini della ricerca presente
                            nei dati inseriti nell'applicazione (parole chiave, curriculum, etc...) o nelle pubblicazioni (titolo e abstract). I risultati sono ordinati
                            sulla base del numero di occorrenze dei termini della ricerca.
                            </div>
                            <div class="mt-2">
                            Se si vuole indicare come termine della ricerca una frase e non una singola parola, inserirla tra virgolette. Se invece si vuole escludere
                            dalla ricerca le pubblicazioni o i dati dell'applicazione che contengono una parola specifica, bisogna inserirla tra i termini della ricerca
                            preceduta dal segno meno.
                            </div>
                            <div class="mt-2">
                            Esempio: la ricerca &laquo;azienda "economia aziendale" -sociale&raquo; produrrà l'elenco del personale che ha (tra i dati dell'applicazione o
                            nelle proprie pubblicazioni)la parola "azienda" oppure "economia aziendale", ma non la parola sociale.
                            </div>
                        </div>
                    </div>
                </div>

                <h3>Risultati</h3>

                <ul class="list-group" id='researchers_list'>
                </ul>

                <script src="js/index.js"></script>
<?php
require_once("templates/footer.php");
?>
