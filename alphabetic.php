<?php
require_once 'config.php';
require_once 'library.php';
require_once 'templates/header.php';

$keywords_en = pe_get_keywords('en');
$keywords_it = pe_get_keywords('it');

function display_keywords($keywords) {
    foreach ($keywords as $keyword) {
        ?><p><?= $keyword ?></p><?php
    }
}
?>

<div class="section" style="border-bottom: 1px solid #ccc;">
    <div class="container">
        <div class="mb-5">
            <h2 style="text-align: center;">Elenco alfabetico parole chiave</h2>
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
    </div>
</div>

<?php
require_once("templates/footer.php");
?>
