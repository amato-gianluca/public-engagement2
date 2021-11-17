<?php
require_once 'config.php';
require_once 'library.php';

if (!isset($_SESSION['username'])) {
    redirect_browser('login.php');
}

$userid = pe_id_from_username($_SESSION['username']);

if (! $userid) {
  if (isset($_GET['accept'])) {
      $userid = pe_create_researcher($_SESSION['username']);
  } elseif (isset($_GET['reject'])) {
      unset($_SESSION['username']);
      redirect_browser('.');
  }
}

if ($userid) {
    $_SESSION['userid'] = $userid;
    $target = ($_GET['tgt'] ?? '') == 'edit' ? 'edit.php' : '.';
    redirect_browser($target);
}

require_once 'templates/header.php';
?>

<section class="header6" style="height: 100px; background-image: url('images/home.png'); background-size: cover; background-position: center;">
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
            <h1 style="text-align: center;">Catalogo delle competenze</h1>
        </div>
        <div class="mb-5">
            <h2 style="text-align: center;">Normativa per il trattamento dei dati personali</h2>
        </div>

        <div class="mb-3">
            Qui ci mettiamo la normativa.
        </div>

        <form action="" class="mb-3">
            <div class="row g-2">
                <div class="col-auto">
                    <button type="submit" name="accept" class="btn btn-primary">Accetta</button>
                </div>
                <div class="col-auto">
                    <button type="submit"  name="reject" class="btn btn-primary">Rifiuta</button>
                </div>
            </div>
        </form>
<?php
require_once 'templates/footer.php';
?>
