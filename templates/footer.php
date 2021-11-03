<div class="section" id="contact">
        <div class="container">
            <div class="section-title">
                <h2 class="text-white">Scopri cosa vuol dire essere dell'Ud'A</h2>
            </div>
            <div class="row text-white text-center">
                <div class="col-lg-4">
                    <span class="ti-map-alt"></span>
                    <p><small>SEDE DI CHIETI</small><br>
                        <small>Via dei Vestini,31<br>
                            Centralino 0871.3551</small>
                    </p>
                    <p><small>SEDE DI PESCARA</small><br>
                        <small>Viale Pindaro,42<br>
                            Centralino 085.45371</small>
                    </p>
                </div>
                <div class="col-lg-4">
                    <span class="ti-pencil-alt"></span>
                    <p><small>email: <a href="mailto:info@unich.it">info@unich.it</a></small><br>
                        <small>PEC: <a href="mailto:ateneo@pec.unich.it">ateneo@pec.unich.it</a></small><br>
                        <small>Partita IVA 01335970693</small><br>
                    </p>
                </div>
                <div class="col-lg-4">
                    <span class="ti-direction-alt"></span>
                    <p>
                        <a href="https://www.facebook.com/universitadannunzio" target="_blank" title="Facebook">
                            <img alt="icona Facebook" src="images/ico_fb.png">
                        </a>&nbsp;
                        <a href="http://twitter.com/univUda" target="_blank" title="Twitter">
                            <img alt="icona Twitter" src="images/ico_twitter.png">
                        </a>
                    </p>
                    <p>
                        <a href="http://www.youtube.com" title="Youtube" target="_blank">
                            <img alt="icona Youtube" src="images/ico_yt.png">
                        </a>&nbsp;
                        <a href="https://www.instagram.com/" title="Instagram">
                            <img alt="icona Instagram" src="images/ico_inst.png">
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <footer class="light-bg py-3 my-0 text-center">
        <!-- Copyright  -->
        <p class="mb-2"><small>COPYRIGHT © 2021. ALL RIGHTS RESERVED - UNIVERSITÀ DEGLI STUDI GABRIELE D'ANNUNZIO -
                CHIETI/PESCARA</small></p>
    </footer>
    <script>
        $(document).ready(function() {
            // Add minus icon for collapse element which is open by default
            $(".collapse.show").each(function() {
                $(this).prev(".card-header").find(".fa").addClass("fa-minus").removeClass("fa-plus");
            });

            // Toggle plus minus icon on show hide of collapse element
            $(".collapse").on('show.bs.collapse', function() {
                $(this).prev(".card-header").find(".fa").removeClass("fa-plus").addClass("fa-minus");
            }).on('hide.bs.collapse', function() {
                $(this).prev(".card-header").find(".fa").removeClass("fa-minus").addClass("fa-plus");
            });
        });
    </script>
</body>

</html>