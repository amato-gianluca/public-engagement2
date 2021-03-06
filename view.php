<?php
require_once 'library.php';

if (isset($_GET['crisId'])) {
    $iris_username = $_GET['crisId'];
    $idab = iris_idab_from_crisid($iris_username);
} else if (isset($_GET['idab'])) {
    $idab = $_GET['idab'];
    $iris_username = iris_crisid_from_idab($idab);
} else {
    redirect_browser('.');
}

if ($idab) {
    $esse3_displayname = esse3_displayname_from_idab($idab);
    $esse3_cv = esse3_cv_from_idab($idab);
    $esse3_data = esse3_personnel_from_idab($idab);
    $id = pe_id_from_idab($idab);
    if ($id)
        $pe_user = pe_researcher_from_id($id);
}

$search = $_GET['search'] ?? '';
$parsed_search = query_parse($search);

if ($iris_username) {
    $iris_papers = iris_items_from_crisid($iris_username);
    $iris_papers_scores = iris_items_from_crisid_with_score($iris_username, $search);
    $scores = [];
}

require_once 'templates/header.php';
?>

                <div class="mb-5">
                    <h2 class="text-center">Informazioni ricercatore</h2>
                </div>

                <div class="col-md-12 mb-3">
                    <span class="lead"><a href="https://www.unich.it/ugov/person/<?= $idab ?>"><?= h($esse3_displayname) ?></a></span>
                    <br>
                    <?= h($esse3_data['DS_RUOLO'] ?? '') ?>
                    <?= array_key_exists('DS_SSD', $esse3_data) ||  array_key_exists('CD_SSD', $esse3_data) ?
                            'di ' . h($esse3_data['DS_SSD']) . ' (' . h($esse3_data['CD_SSD']) . ')' : '' ?>
                    <br>
                    <?= h($esse3_data['DS_AFF_ORG'] ?? '') ?>
                </div>

                <?php if ($id) { ?>
                <ul class="nav nav-tabs" id="keywords-tab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="it-tab" data-bs-toggle="tab" data-bs-target="#it-container" type="button" role="tab" aria-controls="it-container" aria-selected="true">
                            <img src="images/itflag.png"> Italiano
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="en-tab" data-bs-toggle="tab" data-bs-target="#en-container" type="button" role="tab" aria-controls="en-container" aria-selected="true">
                            <img src="images/gbflag.png"> English
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="papers-tab" data-bs-toggle="tab" data-bs-target="#papers-container" type="button" role="tab" aria-controls="papers-container" aria-selected="false">
                            Prodotti della ricerca
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="keywords-tabcontent">
                    <div class="tab-pane fade show active" id="it-container" role="tabpanel" aria-labelledby="it-tab">
                        <div class="card mb-1">
                            <div class="card-body">
                                <h5 class="card-title">Parole chiavi</h5>
                                <p class="card-text">
                                    <input type="text" id="keywords_it" value="<?= h(list_to_tagify($pe_user['keywords_it'])) ?>" readonly>
                                 </p>
                            </div>
                        </div>
                        <div class="card mb-1">
                            <div class="card-body">
                                <h5 class="card-title">Interessi</h5>
                                <p class="card-text"> <?= highlight_text(h($pe_user['interests_it']), $parsed_search) ?></p>
                            </div>
                        </div>
                        <div class="card mb-1">
                            <div class="card-body">
                                <h5 class="card-title">Spin-off e brevetti</h5>
                                <p class="card-text"><?= highlight_text(h($pe_user['interests_it']), $parsed_search) ?></p>
                            </div>
                        </div>
                        <div class="card mb-1">
                            <div class="card-body">
                                <h5 class="card-title">Premi e onorificenze</h5>
                                <p class="card-text"><?= highlight_text(h($pe_user['awards_it']), $parsed_search) ?></p>
                            </div>
                        </div>
                        <div class="card mb-1">
                            <div class="card-body">
                                <h5 class="card-title">Curriculum</h5>
                                <p class="card-text"> <?= highlight_text(h($pe_user['curriculum_it']), $parsed_search) ?></p>
                                <?php if ($esse3_cv['CV_IT_URL']) { ?>
                                <a href="<?= h($esse3_cv['CV_IT_URL']) ?>" class="card-link" target="_blank">Scarica curriculum completo</a>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="en-container" role="tabpanel" aria-labelledby="en-tab">
                        <div class="card mb-1">
                            <div class="card-body">
                                <h5 class="card-title">Research Keywords</h5>
                                <p class="card-text">
                                    <input type="text" id="keywords_en" value="<?= h(list_to_tagify($pe_user['keywords_en'])) ?>" readonly>
                                </p>
                            </div>
                        </div>
                        <div class="card mb-1">
                            <div class="card-body">
                                <h5 class="card-title">Research Interests and Expertise</h5>
                                <p class="card-text"> <?= highlight_text(h($pe_user['interests_en']), $parsed_search) ?></p>
                            </div>
                        </div>
                        <div class="card mb-1">
                            <div class="card-body">
                                <h5 class="card-title">Spin-off &amp; patents</h5>
                                <p class="card-text"><?= highlight_text(h($pe_user['interests_en']), $parsed_search) ?></p>
                            </div>
                        </div>
                        <div class="card mb-1">
                            <div class="card-body">
                                <h5 class="card-title">Acadmic Honorary Assignments and Awards</h5>
                                <p class="card-text"><?= highlight_text(h($pe_user['awards_en']), $parsed_search) ?></p>
                            </div>
                        </div>
                        <div class="card mb-1">
                            <div class="card-body">
                                <h5 class="card-title">Curriculum</h5>
                                <p class="card-text"> <?= highlight_text(h($pe_user['curriculum_en']), $parsed_search) ?></p>
                                <?php if ($esse3_cv['CV_EN_URL']) { ?>
                                <a href="<?= h($esse3_cv['CV_EN_URL']) ?>" class="card-link" target="_blank">Download full curriculum</a>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="papers-container" role="tabpanel" aria-labelledby="papers-tab">
                    <?php } ?>
                        <h2>Prodotti rilevanti rilevanti per i termini della ricerca</h2>
                        <ul class="list-group">
                        <?php foreach ($iris_papers_scores as $paper) {
                            $scores[$paper['itemId']] = $paper['score'];
                            ?>
                            <li class="list-group-item">
                                <?php iris_item_display($paper, $parsed_search) ?>
                            </li>
                        <?php } ?>
                        </ul>
                        <h2 class="mt-4">Altri prodotti</h2>
                        <ul class="list-group">
                        <?php foreach ($iris_papers as $paper) { ?>
                            <?php if (array_key_exists($paper['itemId'], $scores)) continue; ?>
                            <li class="list-group-item">
                                <?php iris_item_display($paper) ?>
                            </li>
                        <?php } ?>
                        </ul>
                <?php if ($id) { ?>
                    </div>
                </div>
                <?php } ?>

                <script src="js/view.js"></script>
<?php
require_once("templates/footer.php");
?>
