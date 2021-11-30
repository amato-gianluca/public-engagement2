<?php
require_once 'library.php';

if (isset($_GET['crisId'])) {
    $iris_username = $_GET['crisId'];
    $matricola = iris_crisId_to_matricola($iris_username);
} else if (isset($_GET['matricola'])) {
    if (get_config('ERROR_MODE') != 'debug')
        trigger_error('The  parameter `matricola` should only be used in DEBUG mode');
    $matricola = $_GET['matricola'];
    $iris_username = iris_matricola_to_crisId($matricola);
} else {
    redirect_browser('.');
}

if ($matricola) {
    $esse3_user = esse3_get_author_by_matricola($matricola);
    $esse3_cv = esse3_get_cv_by_matricola($matricola);
    $esse3_role = esse3_get_role_by_matricola($matricola);
    $id = pe_id_from_username($matricola);
    if ($id)
        $pe_user = pe_get_researcher($id);
}

if ($iris_username) {
    $iris_papers = iris_get_paper_from_crisId($iris_username);
}

require_once 'templates/header.php';
?>

                <div class="mb-5">
                    <h2 class="text-center">Informazioni ricercatore</h2>
                </div>

                <div class="col-md-12 mb-3">
                    <span class="lead"><?= h($esse3_user['COGNOME']) ?> <?= h($esse3_user['NOME']) ?></span>
                    <br>
                    <?= h($esse3_role['DS_RUOLO'] ?? '') ?>
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
                            Papers
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
                                <p class="card-text"> <?= h($pe_user['interests_it']) ?></p>
                            </div>
                        </div>
                        <div class="card mb-1">
                            <div class="card-body">
                                <h5 class="card-title">Spin-off e brevetti</h5>
                                <p class="card-text"><?= h($pe_user['interests_it']) ?></p>
                            </div>
                        </div>
                        <div class="card mb-1">
                            <div class="card-body">
                                <h5 class="card-title">Premi e onorificenze</h5>
                                <p class="card-text"><?= h($pe_user['awards_it']) ?></p>
                            </div>
                        </div>
                        <div class="card mb-1">
                            <div class="card-body">
                                <h5 class="card-title">Curriculum</h5>
                                <p class="card-text"> <?= h($pe_user['curriculum_it']) ?></p>
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
                                <p class="card-text"> <?= h($pe_user['interests_en']) ?></p>
                            </div>
                        </div>
                        <div class="card mb-1">
                            <div class="card-body">
                                <h5 class="card-title">Spin-off &amp; patents</h5>
                                <p class="card-text"><?= h($pe_user['interests_en']) ?></p>
                            </div>
                        </div>
                        <div class="card mb-1">
                            <div class="card-body">
                                <h5 class="card-title">Acadmic Honorary Assignments and Awards</h5>
                                <p class="card-text"><?= h($pe_user['awards_en']) ?></p>
                            </div>
                        </div>
                        <div class="card mb-1">
                            <div class="card-body">
                                <h5 class="card-title">Curriculum</h5>
                                <p class="card-text"> <?= h($pe_user['curriculum_en']) ?></p>
                                <?php if ($esse3_cv['CV_EN_URL']) { ?>
                                <a href="<?= h($esse3_cv['CV_EN_URL']) ?>" class="card-link" target="_blank">Download full curriculum</a>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="papers-container" role="tabpanel" aria-labelledby="papers-tab">
                    <?php } ?>
                        <ul class="list-group">
                        <?php foreach ($iris_papers as $paper) { ?>
                            <li class="list-group-item"><?php iris_display_paper($paper) ?></li>
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
