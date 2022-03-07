<!doctype html>
<html lang="it">
    <head>
        <title>Unich - Terza Missione</title>

        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- Custom meta tags -->
        <meta name="description" content="Unich - Università degli Studi G. d'Annunzio">
        <meta name="keywords" content="università, d'annunzio, chieti, pescara, studi, public engagement, competenze">

        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

        <!--Tagify -->
        <script src="https://unpkg.com/@yaireo/tagify"></script>
        <script src="https://unpkg.com/@yaireo/tagify/dist/tagify.polyfills.min.js"></script>
        <link href="https://unpkg.com/@yaireo/tagify/dist/tagify.css" rel="stylesheet" type="text/css" />

        <!--Montserrat + Playfair Font-->
        <link href="https://fonts.googleapis.com/css?family=Montserrat:300,400|Playfair+Display:400,700" rel="stylesheet">

        <!--Font Awesome-->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <!-- Themify Icon Set -->
        <link rel="stylesheet" href="bower_components/themify-icons/css/themify-icons.css" />

        <!-- JQuery and JQuery UI -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

        <!-- MobApp CSS styles con modifiche di Simone -->
        <link href="css/style.css" rel="stylesheet">
        <!-- <script src="js/script.js"></script> -->

        <!-- Custom CSS -->
        <link href="css/pe.css" rel="stylesheet">

        <!-- Custom js library -->
        <script src="js/library.js"></script>
    </head>

    <body>
        <div class="nav-menu-top">
            <nav class="navbar navbar-expand-md navbar-dark">
                <div class="container">
                    <div class="collapse navbar-collapse" id="navbar-top">
                        <ul class="navbar-nav ms-auto">
                            <li class="nav-item">
                                <a href="http://www.unich.it/" class="nav-link py-1 text-white" title="Home UdA" target="_blank">Home UdA</a>
                            </li>
                            <li class="nav-item">
                                <a href="https://www.unich.it/node/7208" class="nav-link py-1 text-white" title="MyPage" target="_blank">MyPage</a>
                            </li>
                            <li class="nav-item">
                                <a href="http://webmail.unich.it/horde/imp/" class="nav-link py-1 text-white" title="Webmail Personale" target="_blank">Webmail Personale</a>
                            </li>
                            <li class="nav-item">
                                <a href="https://mail.studenti.unich.it/" class="nav-link py-1 text-white" title="Webmail Studenti" target="_blank">Webmail Studenti</a>
                            </li>
                            <li class="nav-item">
                                <a href="https://www.unich.it/rubrica" class="nav-link py-1 text-white" title="Rubrica" target="_blank">Rubrica</a>
                            </li>
                            <li class="nav-item">
                                <a href="https://en.unich.it" class="nav-link py-1 text-white" title="EN" target="_blank">EN</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
        <!--menu-top-->

        <!-- Nav Menu -->

        <div class="nav-menu">
            <nav class="navbar navbar-light navbar-expand-lg">
                <div class="container">
                    <span class="navbar-brand pe-5">
                        <a href="https://www.unich.it/"><img src="images/logo_1.png" width="50" class="img-fluid py-2" alt="logo UdA"></a>
                        <a href="index.php" class="brand-text" style="text-decoration: none;">COMPETENZE</a>
                    </span>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar" aria-controls="navbar" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbar">
                        <ul class="navbar-nav">
                            <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                            <li class="nav-item"><a class="nav-link" href="alphabetic.php">Indice parole chiave</a></li>
                            <li class="nav-item"><a class="nav-link" href="edit.php">Modifica competenze</a></li>
                            <li class="nav-item"><a class="nav-link" href="credits.php">Credits</a></li>
                            <?php if (isset($_SESSION['userid'])) { ?>
                            <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
            </nav>
        </div>

        <section class="header6" style="height: 100px; background-image: url('images/home.png'); background-size: cover; background-position: center;">
            <div class="container">
                <div class="row justify-content-md-center">
                    <div class="mbr-white col-md-10">
                    </div>
                </div>
            </div>
        </section>

        <section class="section">
            <div class="container mb-5">
                <div class="section-title">
                    <h1 class="text-center">Catalogo delle competenze</h1>
                </div>
