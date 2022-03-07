<?php
require_once 'library.php';

if (isset($_SERVER['uid']) && $_SERVER['uid'] != get_config('ADMIN_USERNAME')) {
  $idab = esse3_idab_from_matricola($_SERVER['uid']);
  if ($idab && esse3_user_allowed($idab)) {
    $_SESSION['idab'] = $idab;
    redirect_browser('checkuser.php?tgt='.urlencode($_GET['tgt']));
  } else {
    $_SESSION['flash'] = 'Non sei autorizzato ad accedere a questo sito';
  }
}

if (isset($_GET['username'])) {
  $idab = esse3_idab_from_matricola($_GET['username']);
  if ($idab && esse3_user_allowed($idab)) {
    $_SESSION['idab'] = $idab;
    redirect_browser('checkuser.php?tgt='.urlencode($_GET['tgt']));
  } else {
    $_SESSION['flash'] = 'Non sei autorizzato ad accedere a questo sito';
  }
}

require_once 'templates/header.php';
?>
                <div class="mb-5">
                    <h2 class="text-center">Login</h2>
                </div>

                <?php if (isset($_SESSION['flash'])) { ?>
                    <div id="success-alert" class="alert alert-primary" role="alert">
                    <strong><?= $_SESSION['flash'] ?>
                    </div>
                    <?php
                    unset($_SESSION['flash']);
                } ?>

                <form action="<?= $_SERVER['PHP_SELF'] ?>">
                    <div class="mb-3">
                        <label for="username" class="form-label">Inserire idab utente</label>
                        <div class="input-group">
                        <span class="input-group-text" id="basic-addon1">@</span>
                        <input type="text" class="form-control" name="username" id="username" aria-describedby="username_help">
                        </div>
                        <div id="username_help" class="form-text">
                            Utilizzare la stessa idab che si utilizza per l'accesso agli altri servizi dell'ateneo
                            come UdaOnline.
                        </div>
                    </div>
                    <input type="hidden" name="tgt" value="<?= $_GET['tgt'] ?? '' ?>">
                    <button type="submit" class="btn btn-primary">Login</button>
                </form>

<?php
require_once("templates/footer.php");
