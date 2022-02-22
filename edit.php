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
$esse3_user = esse3_docente_from_matricola($username);
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

                <form action="edit.php" method="POST">

                <div class="row">
                    <div class="col-md-8">
                        <span class="lead"><?= h($esse3_user['COGNOME']) ?> <?= h($esse3_user['NOME']) ?></span>
                        <br>
                        <?= h($esse3_role['DS_RUOLO']) ?>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="form-group">
                            <button type="reset" class="btn btn-secondary">Annulla modifiche</button>
                            <button type="submit" name="edit" class="btn btn-primary">Salva</button>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <table class="table table-borderless researchertable">
                        <thead>
                            <tr>
                                <th scope="col" class="legend" style="width: 30%"></th>
                                <th scope="col" class="content" style="width: 35%">english</th>
                                <th scope="col" class="content" style="width: 35%">italiano</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th scope="row">
                                    <span class="rowtitle">Research Keywords</span><br>
                                    <span lang="it">(ogni keyword, singola o composta, va separata dalle altre usando il punto e virgola)</span>
                                </th>
                                <td>
                                    <input type="text" id="keywords_en" name="keywords_en" lang="en" title="keywords in english" value="<?= h(list_to_tagify($pe_user['keywords_en'])) ?>">
                                </td>
                                <td>
                                    <input type="text" id="keywords_it" name="keywords_it" lang="it" title="parole chiavi in italiano" value="<?= h(list_to_tagify($pe_user['keywords_it'])) ?>">
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <span class="rowtitle">Research Interests and Expertise</span><br>
                                </th>
                                <td>
                                    <textarea id="interests_en" name="interests_en" lang="en" rows="5"><?= h($pe_user['interests_en']) ?></textarea>
                                </td>
                                <td>
                                    <textarea id="interests_it" name="interests_it" lang="it"rows="5"><?= h($pe_user['interests_it']) ?></textarea>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <span class="rowtitle">Demerging (spin off) and Patents</span><br>
                                </th>
                                <td>
                                    <textarea id="demerging_en" name="demerging_en" lang="en" rows="5"><?= h($pe_user['demerging_en']) ?></textarea>
                                </td>
                                <td>
                                    <textarea id="demerging_it" name="demerging_it" lang="it" rows="5"><?= h($pe_user['demerging_it']) ?></textarea>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <span class="rowtitle">Acadmic Honorary Assignments and Awards</span>
                                </th>
                                <td>
                                    <textarea id="awards_en" name="awards_en" lang="en" rows="5"><?= h($pe_user['awards_en']) ?></textarea>
                                </td>
                                <td>
                                    <textarea id="awards_it" name="awards_it" lang="it" rows="5"><?= h($pe_user['awards_it']) ?></textarea>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <span class="rowtitle">Short Curriculum Vitae</span>
                                </th>
                                <td>
                                    <textarea id="curriculum_en" name="curriculum_en" lang="en" rows="5"><?= h($pe_user['curriculum_en']) ?></textarea>
                                </td>
                                <td>
                                    <textarea id="curriculum_it" name="curriculum_it" lang="it" rows="5"><?= h($pe_user['curriculum_it']) ?></textarea>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <span class="rowtitle">Curriculum in PDF</span>
                                </th>
                                <td class="align-middle" style="text-align: center; padding: 5px;">
                                    <?php if ($esse3_cv['CV_EN_URL']) { ?>
                                        <a href="<?= h($esse3_cv['CV_EN_URL']) ?>" class="btn-secondary btn" target="_blank" lang="en">Download english curriculum</a>
                                    <?php } ?>
                                </td>
                                <td class="align-middle" style="text-align: center; padding: 5px;">
                                    <?php if ($esse3_cv['CV_IT_URL']) { ?>
                                        <a href="<?= h($esse3_cv['CV_IT_URL']) ?>" class="btn-secondary btn" target="_blank" lang="it">Scarica curriculum in italiano</a>
                                    <?php } ?>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3"></td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <span class="rowtitle" lang="en">Publications last 5 years</span>
                                </th>
                                <td colspan="2">
                                    <ul class="list-group">
                                        <?php foreach ($iris_papers as $paper) { ?>
                                            <li class="list-group-item"><?php iris_display_item($paper) ?></li>
                                        <?php } ?>
                                    </ul>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </form>

                <script src="js/edit.js"></script>

<?php
require_once("templates/footer.php");
?>
