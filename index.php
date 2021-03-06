<?php
require_once 'library.php';
require_once 'templates/header.php';

$start = intval($_GET['start'] ?? '');
$limit = intval($_GET['limit'] ?? '');
if ($start <= 0) $limit = 0;
if ($limit <= 0) $limit = intval(get_config('DEFAULT_SEARCH_LIMIT'));
$search = $_GET['search'] ?? '';
$keywords = $_GET['keywords'] ?? ''
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
                        su <a href="https://ricerca.unich.it" target="_blank">ricerca.unich.it</a>.
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
                    <label for="keywords" class="form-label">Ricerca di parole chiave</label>
                    <input type="text" class="form-control" id="keywords" aria-described-by="keywords_help">
                    <div id="keywords_help" class="form-text mb-3">
                        Le parole chiave qua sopra verranno cercate esclusivamente tra quelle inserite dai docenti in questa applicazione.
                    </div>
                </div>

                <h3>Risultati</h3>

                <nav class="navbar navbar-expand mb-3">
                    <div>
                        <ul class="navbar-nav ms-auto">
                            <li class="page-item"><a id="search-first" class="page-link" aria-label="Precedente"><i class='fa-solid fa-angles-left'></i></a></li>
                            <li class="page-item"><a id="search-left" class="page-link" aria-label="Precedente"><i class="fas fa-arrow-left"></i></a></li>
                            <li class="page-item"><a id="search-right" class="page-link"><i class="fas fa-arrow-right"></i></a></li>
                            <span class="navbar-text px-3">Risultati visualizzati : <span id="search-start"></span> &ndash; <span id="search-end"></span></span>
                        </ul>
                    </div>
                </nav>

                <ul class="list-group" id='researchers_list'>
                </ul>

                <script>
                    let start = <?= $start ?>;
                    let page_size = <?= $limit ?>;
                    let search = <?= json_encode($search) ?>;
                    let keywords;
                    try {
                        keywords = JSON.parse(<?= json_encode($keywords) ?>);
                    } catch(e) {
                        keywords = [];
                    }
                </script>
                <script src="js/index.js"></script>
<?php
require_once("templates/footer.php");
?>
