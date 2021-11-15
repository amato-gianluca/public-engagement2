<?php
require_once 'config.php';
require_once 'library.php';

if (!DEBUG) die();

if (isset($_GET['username'])) {
    $_SESSION['username'] = $_GET['username'];
    header('Location: index.php');
    die();
}

require_once 'templates/header.php';
?>
<div class="section" style="border-bottom: 1px solid #ccc;">
    <div class="container">
            <div class="section-title">
                <h2 style="text-align: center;">Catalogo delle competenze</h2>
            </div>

            <p class="h2">Login</p>

            <form action="<?= $_SERVER['PHP_SELF'] ?>">
                <div class="mb-3">
                    <label for="username" class="form-label">Inserire matricola utente</label>
                    <div class="input-group">
                    <span class="input-group-text" id="basic-addon1">@</span>
                    <input type="text" class="form-control" name="username" id="username" aria-describedby="username_help">
                    </div>
                    <div id="username_help" class="form-text">
                        Utilizzare la stessa matricola che si utilizza per l'accesso agli altri servizi dell'ateneo
                        come UdaOnline.
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Login</button>
            </form>

        </div>
    </div>
</div>

<?php
require_once("templates/footer.php");
?>
