<?php
require_once 'config.php';
require_once 'library.php';
require_once 'templates/header.php';
?>
<section class="header6" style="height: 300px; background-image: url('images/home.png'); background-size: cover; background-position: center;">
    <div class="container">
        <div class="row justify-content-md-center">
            <div class="mbr-white col-md-10">
            </div>
        </div>
    </div>
</section>

<div class="section" style="border-bottom: 1px solid #ccc;">
    <div class="container">
        <div class="section-title">
            <h2 style="text-align: center;">Censimento delle attività di</h2>
                <h1 style="text-align: center;">Public Engagement</h1>
        </div>

        <p class="h2">Filtri per la ricerca</p>

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
<?
require_once("templates/footer.php");
?>
