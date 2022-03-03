<?php
require_once 'library.php';

if (!isset($_SESSION['userid'])) {
    redirect_browser('login.php?tgt=edit');
}

$userid = $_SESSION['userid'];

if (isset($_POST['edit'])) {
    $keywords_en = $_POST['keywords_en'] ? array_map(
        function($tag) { return $tag->value; },
        json_decode($_POST['keywords_en'])
    ) : [];
    $keywords_it = $_POST['keywords_it'] ? array_map(
        function($tag) { return $tag->value; },
        json_decode($_POST['keywords_it'])
    ) : [];
    $result = pe_researcher_edit($userid, $keywords_en, $keywords_it, $_POST);
    if (! $result) trigger_error('Problem updating user info');
    $_SESSION['flash'] = 'Dati salvati con successo';
    redirect_browser('edit.php');
}

$pe_user = pe_researcher_from_id($userid);

$username = $pe_user['username'];
$esse3_displayname = esse3_displayname_from_matricola($username);
$esse3_cv = esse3_cv_from_matricola($username);
$esse3_role = esse3_role_from_matricola($username);

$iris_username = iris_crisid_from_matricola($username);
$iris_papers = iris_items_from_crisid($iris_username);

if (! $pe_user) {
    // Here we need to ask the user if he want to be part of the site.
    if (pe_researcher_create($username)) {
        $pe_user = pe_researcher_from_id($username);
        if (! $pe_user) trigger_error('Problem creating new user #2');
    } else {
        trigger_error('Problem creating new user #1');
    }
}

require_once 'templates/header.php';
?>
                <div class="mb-5">
                    <h2 class="text-center">Modifica competenze</h2>
                </div>

                <?php if (isset($_SESSION['flash'])) { ?>
                    <div id="success-alert" class="alert alert-primary" role="alert">
                    <strong><?= $_SESSION['flash'] ?>
                    </div>
                    <?php
                    unset($_SESSION['flash']);
                } ?>

                <form action="" method="POST">

                <div class="row mb-3">
                    <div class="col-md-8">
                        <span class="lead"><?= h($esse3_displayname) ?> </span>
                        <br>
                        <?= $esse3_role ? h($esse3_role['DS_RUOLO']) : '' ?>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="form-group">
                            <button type="reset" class="btn btn-secondary">Annulla modifiche</button>
                            <button type="submit" name="edit" class="btn btn-primary">Salva</button>
                        </div>
                    </div>
                </div>
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
                                    <input type="text" id="keywords_it" name="keywords_it" lang="it" title="parole chiavi in italiano" value="<?= h(list_to_tagify($pe_user['keywords_it'])) ?>">
                                 </p>
                            </div>
                        </div>
                        <div class="card mb-1">
                            <div class="card-body">
                                <h5 class="card-title">Interessi</h5>
                                <p class="card-text"><textarea id="interests_it" name="interests_it" lang="it" rows="5"><?= h($pe_user['interests_it']) ?></textarea></p>
                            </div>
                        </div>
                        <div class="card mb-1">
                            <div class="card-body">
                                <h5 class="card-title">Spin-off e brevetti</h5>
                                <p class="card-text"><textarea id="demerging_it" name="demerging_it" lang="it" rows="5"><?= h($pe_user['demerging_it']) ?></textarea></p>
                            </div>
                        </div>
                        <div class="card mb-1">
                            <div class="card-body">
                                <h5 class="card-title">Premi e onorificenze</h5>
                                <p class="card-text"><textarea id="awards_it" name="awards_it" lang="it" rows="5"><?= h($pe_user['awards_it']) ?></textarea></p>
                            </div>
                        </div>
                        <div class="card mb-1">
                            <div class="card-body">
                                <h5 class="card-title">Curriculum</h5>
                                <p class="card-text"><textarea id="curriculum_it" name="curriculum_it" lang="it" rows="5"><?= h($pe_user['curriculum_it']) ?></textarea></p>
                                <?php if ($esse3_cv && $esse3_cv['CV_IT_URL']) { ?>
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
                                    <input type="text" id="keywords_en" name="keywords_en" lang="en " title="keywords in english" value="<?= h(list_to_tagify($pe_user['keywords_en'])) ?>">
                                </p>
                            </div>
                        </div>
                        <div class="card mb-1">
                            <div class="card-body">
                                <h5 class="card-title">Research Interests and Expertise</h5>
                                <p class="card-text"><textarea id="interests_en" name="interests_en" lang="en" rows="5"><?= h($pe_user['interests_en']) ?></textarea></p>
                            </div>
                        </div>
                        <div class="card mb-1">
                            <div class="card-body">
                                <h5 class="card-title">Spin-off &amp; patents</h5>
                                <p class="card-text"><textarea id="demerging_en" name="demerging_en" lang="en" rows="5"><?= h($pe_user['demerging_en']) ?></textarea></p>
                            </div>
                        </div>
                        <div class="card mb-1">
                            <div class="card-body">
                                <h5 class="card-title">Acadmic Honorary Assignments and Awards</h5>
                                <p class="card-text"><textarea id="awards_en" name="awards_en" lang="en" rows="5"><?= h($pe_user['awards_en']) ?></textarea></p>
                            </div>
                        </div>
                        <div class="card mb-1">
                            <div class="card-body">
                                <h5 class="card-title">Curriculum</h5>
                                <p class="card-text"><textarea id="curriculum_en" name="curriculum_en" lang="en" rows="5"><?= h($pe_user['curriculum_en']) ?></textarea></p>
                                <?php if ($esse3_cv && $esse3_cv['CV_EN_URL']) { ?>
                                <a href="<?= h($esse3_cv['CV_EN_URL']) ?>" class="card-link" target="_blank">Download full curriculum</a>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="papers-container" role="tabpanel" aria-labelledby="papers-tab">
                        <ul class="list-group">
                        <?php foreach ($iris_papers as $paper) { ?>
                            <li class="list-group-item">
                                <?php iris_item_display($paper) ?>
                            </li>
                        <?php } ?>
                        </ul>
                    </div>
                </div>
                </form>

                <script src="js/edit.js"></script>

<?php
require_once("templates/footer.php");
?>
