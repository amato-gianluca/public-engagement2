<?php
require_once 'library.php';
require_once 'templates/header.php';

$keywords_en = pe_keywords_from_lang_and_prefix('en');
$keywords_it = pe_keywords_from_lang_and_prefix('it');

function display_keywords($keywords) { ?>
    <div class="accordion" id="accordion-keywords">
        <?php foreach ($keywords as $keyword) { ?>
        <div class="accordion-item">
            <h2 class="accordion-header" id="flush-heading<?= h($keyword['id']) ?>">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse<?= h($keyword['id']) ?>" aria-expanded="false" aria-controls="flush-collapse<?= h($keyword['id']) ?>">
                    <?= $keyword['keyword'] ?>
                </button>
            </h2>
            <div id="flush-collapse<?= h($keyword['id']) ?>" class="accordion-collapse collapse" aria-labelledby="flush-heading<?= h($keyword['id']) ?>" data-keyword="<?= h($keyword['id']) ?>" data-bs-parent="#accordion-keywords">
                <div class="accordion-body">
                    <ul class="list-group"></ul>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
<?php } ?>

                <div class="mb-5">
                    <h2 class="text-center">Elenco alfabetico parole chiave</h2>
                </div>

                <ul class="nav nav-tabs" id="keywords-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="en-tab" data-bs-toggle="tab" data-bs-target="#en-container" type="button" role="tab" aria-controls="home" aria-selected="true">
                        <img src="images/gbflag.png"> English
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="it-tab" data-bs-toggle="tab" data-bs-target="#it-container" type="button" role="tab" aria-controls="profile" aria-selected="false">
                    <img src="images/itflag.png"> Italiano
                    </button>
                </li>
                </ul>

                <div class="tab-content" id="keywords-tabcontent">
                    <div class="tab-pane fade show active" id="en-container" role="tabpanel" aria-labelledby="en-tab">
                        <?php display_keywords($keywords_en); ?>
                    </div>
                    <div class="tab-pane fade" id="it-container" role="tabpanel" aria-labelledby="it-tab">
                        <?php display_keywords($keywords_it); ?>
                    </div>
                </div>

                <script src="js/alphabetic.js"></script>

<?php
require_once("templates/footer.php");
?>
