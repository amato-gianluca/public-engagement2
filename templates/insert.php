<?php
    if(isset($_POST['submit'])){       
        #print_r(json_encode($_POST));
        $data = (new DateTime())->format('Y-m-d H:i:s');
        $result = InserisciRisposte($db, $form['id'], $_SESSION, $data, $_POST);
        #echo 'Inserito: '.($result ? 'si':'no');
        unset($_POST);
    }
    
    $action='';
    if(isset($_GET['action']))
        if ($_GET['action']=='v')
            $action = 'v';
?>
    
    <div class="section" style="border-bottom: 1px solid #ccc;">
        <div class="container">
            <div class="section-title">
                <h1 style="text-align: left;"><?php echo $form['nome']; ?></h1>
            </div>

            <div class="row pt-4 pb-4" style="border-bottom: 1px solid #ccc;">
                <div class="col-md-12">
                    <div class="paragrafo">
                        <?php
                        echo 'Nome: ' .$_SESSION['nome']. ' ' .$_SESSION['cognome'];
                        echo '<br>Sede: ' .$_SESSION['dipartimento'];
			if($_SESSION['areanum']!='00') {
			     echo '<br>Area: ' .$_SESSION['areanum']. ' - ' .$_SESSION['area'];
			}
                        ?>
                        <p><br><?php echo $form['descrizione']; ?></p>
                    </div>
                    <div class="paragrafo">
                        <div class="orie-title">
                            <Ri:a>RIEPILOGO ATTIVITÀ INSERITE</Ri:a>
                            <div class="linkVari pull-right">
                                <a class="btn btn-primary add-record" href="#riga"><i class="fa fa-plus"></i> Inserisci un'attività</a>
                            </div>
                        </div>
                        <div class="orie-line"></div>
                        
                        <script>
                            jQuery(document).delegate('a.add-record', 'click', function(e) {
                                $("#form1").show();
                                $("#vform1").hide();
                            });
                        </script>
                        
                        <?php
                        if($result = GetRisposte($db, $form['id'], $_SESSION['matricola'])){
                            if(mysqli_num_rows($result) > 0){
                                echo '<table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Anno</th>
                                        <th>Campo d\'Azione</th>
                                        <th>Tipologia</th>
                                        <th>Titolo</th>
                                        <th>Azioni</th>
                                    </tr>
                                </thead>
                                <tbody>';
                                while($row = mysqli_fetch_array($result)){
                                    $risp = json_decode($row['risposta']);
                                    echo    '<tr>
                                                <td>'.$risp->{'d-1'}.'</td>
                                                <td>'.GetCampoDAzione($db, $form['id'], $risp->{'d-3'}).'</td>
                                                <td>'.GetTipologia($db, $form['id'], $risp->{'d-3'}, $risp->{'d-3-2'}).'</td>
                                                <td>'.$risp->{'d-5'}.'</td>
                                            
                                                <td><a href="compila.php?action=v&&rid='.$row['id'].'&&id='.$form['id'].'" class="mr-3" title="Visualizza Record" data-toggle="tooltip"><span class="fa fa-eye"></span></a>
                                                <!--<a href="#" class="mr-3" title="Compila" data-toggle="tooltip"><span class="fa fa-play"></span></a>
                                                    <a href="#" class="mr-3" title="View Record" data-toggle="tooltip"><span class="fa fa-eye"></span></a>
                                                    <a href="#" class="mr-3" title="Aggiorna Record" data-toggle="tooltip"><span class="fa fa-pencil-alt"></span></a>-->
                                                    <a class="delete_row" href="elimina.php?rid='.$row['id'].'&&id='.$form['id'].'" title="Elimina Record" data-toggle="tooltip"><span class="fa fa-trash"></span></a>
                                                </td>
                                            </tr>';
                                }
                                echo '</tbody></table>
                                <script>
                                    $(".delete_row").click(function(){
                                        return confirm("Sei sicuro di volerlo eliminare?");
                                    })
                                </script>';
                            }
                        }
                        ?>

                    </div>
                    <div id="riga" class="pt-5 mt-5"></div>
                    <div class="paragrafo pt-5" id="form1" style="display:none">
                        <div class="orie-title">COMPILA IL FORM</div>
                        <div class="orie-line"></div>
                        
                        <?php
                        echo creaForm($db, $form['id']);

                        ?>
                    </div>
                    <?php
                    if ($action=='v')
                    echo '
                    <div class="paragrafo pt-5" id="vform1" style="display:block">
                        <div class="orie-title">VISUALIZZA</div>
                        <div class="orie-line"></div>'.
                        visualizzaForm($db, $form['id'], $_GET['rid']).
                    '</div>';
                    ?>
                </div>
            </div>
        </div>
    </div>
<?php
function visualizzaForm ($db, $formId, $rId) {
    $str = '<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>Domanda</th>
            <th>Risposta</th>
        </tr>
    </thead>
    <tbody>';
    $risp='';
    $risposta = GetRisposta ($db, $rId, $_SESSION['matricola']);
    if(mysqli_num_rows($risposta) > 0){
        $row = mysqli_fetch_array($risposta);
        $risp = json_decode($row['risposta']);
        //echo json_encode($risp);
        $ca = '';
        foreach($risp as $domanda => $valore){
            if($valore!=''){
                $num = substr($domanda,2);
                $d = RecuperaDomanda($db, $formId, $num);
                $dd = mysqli_fetch_array($d);       
                switch ($num) {
                    case '3':
                        $str.='<tr><td>'.$dd['descrizione'].'</td><td>'.GetCampoDAzione($db, '' , $valore).'</td></tr>';
                        $ca = $valore;
                        break;
                    case '3-2':
                        $str.='<tr><td>Tipologia</td><td>'.GetTipologia($db,3, $ca, $valore).'</td></tr>';
                        break;
                    case '12':
                    case '21':
                    case '22':
                    case '24':
		    case '27':
                        $str.='<tr><td>'.$dd['descrizione'].'</td><td>';
                        $aaa = (json_decode($valore));
                        foreach($aaa as $aa){
                            $str.= $aa->value;
                            if (next($aaa)==true) $str.=', ';
                        }
                        $str.= '</td></tr>';
                        break;
                    default:
                        $str.='<tr><td>'.$dd['descrizione'].'</td><td>'.$valore.'</td></tr>';
                }
            }
        }
    }
    $str.='</tdoby></table>';
    return $str;
}

    function creaForm($db, $formId) {
        $livello = 1;
        $str = "";
        if($result = getDomande($db, $formId)){
            if(mysqli_num_rows($result) > 0){
                $str.='<p>* Campo obbligatorio</p>';
                $str.= '<form id="formId" class="needs-validation" novalidate action="" method="post">';

                while($row = mysqli_fetch_array($result)){
                    $str.= visualizzaDomanda($db, $row); 
                }

                $str.= '<div class="d-flex justify-content-center">
                            <button class="btn btn-primary btn-lg" type="submit" name="submit">Inserisci l&apos;attività</button>
                        </div>
                    </form>';
                $str.="<script type='text/javascript'>
                    (function () {
                    'use strict'
                  
                    var forms = document.querySelectorAll('.needs-validation')
                  
                    Array.prototype.slice.call(forms)
                      .forEach(function (form) {
                        form.addEventListener('submit', function (event) {
                          if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                          }
                  
                          form.classList.add('was-validated')
                        }, false)
                      })
                  })()
                  </script>";    
            }
        }
        return $str;
    }

    function visualizzaDomanda($db, $domanda){
        $obbligatorio = ($domanda['obbligatorio']==1)?" required":"";
        $str = intestazioneDomanda($domanda);
        switch($domanda['tipologia']){
            case 'text':
                $str.= '<input class="form-control" type="text" id="d-'.$domanda['id'].'" name="d-'.$domanda['id'].'" '.$obbligatorio.'/>';
                break;
            case 'number':
                $str.='<input class="form-control" type="number" step="any" id="d-'.$domanda['id'].'" name="d-'.$domanda['id'].'" '.$obbligatorio.'/>';
                break;
            case 'textArea':
                $str.= '<textarea class="form-control" rows="3" id="d-'.$domanda['id'].'" name="d-'.$domanda['id'].'" '.$obbligatorio.'></textarea>';
                break;
            case 'select':
                $str.= '<select class="form-control" id="d-'.$domanda['id'].'" name="d-'.$domanda['id'].'" '.$obbligatorio.'>
                            <option disabled selected value="">Scegli...</option>';    
                $str.= caricaOpzioni($db, $domanda['id']);            
                $str.= '</select>';    
                break;
            case 'tags':
                $str.= '<input class="form-control" type="text" id="d-'.$domanda['id'].'" name="d-'.$domanda['id'].'" '.$obbligatorio.'/>';
                $str.= "<script type='text/javascript'>
                $(document).ready(function(){
                    $('[name=d-".$domanda['id']."]').tagify({
                        duplicates: false,
                        whitelist: [],
                        placeholder: '',
                        maxTags: 10,
                        enforceWhitelist: false,
                        dropdown: {
                            classname: '',
                            enabled: 2, 
                            caseSensitive: false,
                            fuzzySearch: true,
                            accentedSearch: true,
                        }
                    });
                });
                </script>";    
                break;
            case 'autoRipe':
                $str.= '<input class="form-control" type="text" id="d-'.$domanda['id'].'" name="d-'.$domanda['id'].'" '.$obbligatorio.'/>';
                
                $str.= '<script type="text/javascript">
                var input = document.querySelector("input[name=d-'.$domanda['id'].']"),
                    tagify'.$domanda['id'].' = new Tagify(input, {
                        whitelist:[],
                        enforceWhitelist : true,  
                        autocomplete: true,
                        dropdown : {
                            enabled: 2, 
                            maxItems : 10
                        }
                    }),
                    controller; 

                    tagify'.$domanda['id'].'.on("input", onInput)

                    function onInput( e ){
                    var value = e.detail.value
                    tagify'.$domanda['id'].'.whitelist = null 

                    controller && controller.abort()
                    controller = new AbortController()

                    tagify'.$domanda['id'].'.loading(true).dropdown.hide()

                    fetch("autocomplete.php?term=" + value, {signal:controller.signal})
                        .then(RES => RES.json())
                        .then(function(res){
                            var values = [] 
                            $.each(res, function(matricola, nome){
                                var client = {code: matricola, value: nome}
                                values.push(client)
                            })
                            values = JSON.stringify(values)
                            values = JSON.parse(values)
                            //alert (JSON.stringify(values, null, 4))
     
                         tagify'.$domanda['id'].'.settings.whitelist = values;
                         tagify'.$domanda['id'].'.loading(false)
                         tagify'.$domanda['id'].'.dropdown.show.call(tagify'.$domanda['id'].', value);
                        })
                    }
                </script>';
                
                /*
                $str.= '<script type="text/javascript">
                    $(function() {
                        $( "#d-'.$domanda['id'].'" ).autocomplete({
                            source: "autocomplete.php"
                        });
                    });
                    </script>';
                    */  
                break;

            case 'autoRipe2':
                $str.= '<input class="form-control" type="text" id="d-'.$domanda['id'].'" name="d-'.$domanda['id'].'" '.$obbligatorio.'/>';

                $str.= '<script type="text/javascript">
                var input = document.querySelector("input[name=d-'.$domanda['id'].']"),
                    tagify'.$domanda['id'].' = new Tagify(input, {
                        whitelist:[],
                        enforceWhitelist : true,
                        autocomplete: true,
                        dropdown : {
                            enabled: 2,
                            maxItems : 10
                        }
                    }),
                    controller;

                    tagify'.$domanda['id'].'.on("input", onInput)

                    function onInput( e ){
                    var value = e.detail.value
                    tagify'.$domanda['id'].'.whitelist = null

                    controller && controller.abort()
                    controller = new AbortController()

                    tagify'.$domanda['id'].'.loading(true).dropdown.hide()

                    fetch("autocomplete2.php?term=" + value, {signal:controller.signal})
                        .then(RES => RES.json())
                        .then(function(res){
                            var values = []
                            $.each(res, function(ssd, ssd_des){
                                var client = {code: ssd, value: ssd_des}
                                values.push(client)
                            })
                            values = JSON.stringify(values)
                            values = JSON.parse(values)
                            //alert (JSON.stringify(values, null, 4))

                         tagify'.$domanda['id'].'.settings.whitelist = values;
                         tagify'.$domanda['id'].'.loading(false)
                         tagify'.$domanda['id'].'.dropdown.show.call(tagify'.$domanda['id'].', value);
                        })
                    }
                </script>';

                /*
                $str.= '<script type="text/javascript">
                    $(function() {
                        $( "#d-'.$domanda['id'].'" ).autocomplete({
                            source: "autocomplete2.php"
                        });
                    });
                    </script>';
                    */
                break;



	    case 'radio':
                $str.= '<div class="form-check" style="padding-left:30px;">
                    <input class="form-check-input" type="radio" name="d-'.$domanda['id'].'" id="d-'.$domanda['id'].'-si" value="Si" onclick="ShowHide'.$domanda['id'].'()">
                    <label class="form-check-label" for="d-'.$domanda['id'].'">
                    Si
                    </label>
                </div>
                <div class="form-check" style="padding-left:30px;">
                    <input class="form-check-input" type="radio" name="d-'.$domanda['id'].'" id="d-'.$domanda['id'].'-no" value="No" onclick="ShowHide'.$domanda['id'].'()">
                    <label class="form-check-label" for="d-'.$domanda['id'].'">
                    No
                    </label>
                </div>';
                break;
            case 'radioMult':
                $str.= '<div class="form-check" style="padding-left:30px;">
                    <input class="form-check-input" type="radio" name="d-'.$domanda['id'].'" id="d-'.$domanda['id'].'-si" value="Si" onclick="ShowHide'.$domanda['id'].'()">
                    <label class="form-check-label" for="d-'.$domanda['id'].'">
                    Si
                    </label>
                </div>
                <div class="form-check" style="padding-left:30px;">
                    <input class="form-check-input" type="radio" name="d-'.$domanda['id'].'" id="d-'.$domanda['id'].'-no" value="No" onclick="ShowHide'.$domanda['id'].'()">
                    <label class="form-check-label" for="d-'.$domanda['id'].'">
                    No
                    </label>
                </div>';
                
                $str.= '<script type="text/javascript">
                function ShowHide'.$domanda['id'].'() {
                    var chkYes = document.getElementById("d-'.$domanda['id'].'-si");
                    var chkNo = document.getElementById("d-'.$domanda['id'].'-no");
                    var dvtext = document.getElementById("d-'.$domanda['id'].'-1");
                    dvtext.style.display = chkYes.checked ? "block" : "none";
                    var sel = document.getElementById("d-'.$domanda['id'].'-2");
                    sel.required = chkNo.checked ? false : true;

                }
                </script>';
                
                $str.= '<div class="form-group" id="d-'.$domanda['id'].'-1" style="display: none">';
                if($onu = GetDomanda($db, 3, 1, 11))
                    if(mysqli_num_rows($onu) > 0){
                        $onu1 = mysqli_fetch_array($onu);
                        $str.= '<label for="d-'.$domanda['id'].'-1">'.$onu1['descrizione'].'</label>
                            <span class="caution"> *</span>
                            <select class="form-control" id="d-'.$domanda['id'].'-2" name="d-'.$domanda['id'].'-2" required >
                                <option disabled selected value="">Scegli...</option>'; 
                        
                        $str.= caricaOpzioniGenitore($db, 3, 2, 11);
                        $str.= '</select>';    
                        $str.= '</div>';
                    }
                break;
            case 'selectMult':
                $str.= '<select class="form-control" id="d-'.$domanda['id'].'" onChange="caricaSelect()" name="d-'.$domanda['id'].'" '.$obbligatorio.'>
                            <option value="">Scegli...</option>';
                $opz = GetOpzioniPerLivello($db, $domanda['id'], 1);
                if(mysqli_num_rows($opz) > 0)
                    while($row2 = mysqli_fetch_array($opz))
                        $str.= '<option value="'.$row2['ordine'].'">'.$row2['descrizione'].'</option>';
                $str.= '</select></div>';

                //secondo livello
                $str.= "<script type='text/javascript'>
                    function caricaSelect(){
                        var obj = {};
                        var select = $('#d-".$domanda['id']."-2'); 
                        select.find('option').remove(); 
                        if($('#d-".$domanda['id']."').val() != ''){
                            $('#d-".$domanda['id']."-1').show();
                            //nascondi tutto
                            $('div[id^=\"v-\"]').filter(
                                function(){
                                    return this.id.match(/\d+$/);
                                }).hide();
                            $('div[id^=\"v-\"] :input[type=text]').filter(
                                function(){
                                    return this.id.match(/\d+$/);
                                }).prop('required',false);
                            $('div[id^=\"v-\"] select').filter(
                                function(){
                                    return this.id.match(/\d+$/);
                                }).prop('required',false);
                            $('div[id^=\"v-\"] radio').filter(
                                function(){
                                    return this.id.match(/\d+$/);
                                }).prop('required',false);
                            $('div[id^=\"v-\"] tags').filter(
                                function(){
                                    return this.id.match(/\d+$/);
                                }).prop('required',false);
                            $('div[id^=\"v-\"] :input[type=text]').each(
                                function(){
                                    obj[this.id] = $(this).prop('required');
                                });
                            $('div[id^=\"v-\"] select').each(
                                function(){
                                    obj[this.id] = $(this).prop('required');
                                });
                            $('div[id^=\"v-\"] radio').each(
                                function(){
                                    obj[this.id] = $(this).prop('required');
                                });
                            $('div[id^=\"v-\"] tags').each(
                                function(){
                                    obj[this.id] = $(this).prop('required');
                                });
                            var a = $('#d-".$domanda['id']."').val();
                            if(a!=''){
                                //console.log('a '+JSON.stringify(obj, null, 4))

                                $('#v-'+a).show();
                                $('#v-'+a+' :input[type=text]').each(function(){
                                    
                                    $(this).prop('required', 'true');
                                });
                            }
                            
                            
                            $.getJSON( 'ajax.php', {
                                f: 1,
                                id: ".$domanda['id'].",
                                livello: 2,
                                genitore_id: $('#d-".$domanda['id']."').val()
                                }
                            )
                            .done(function( data ) {
                                $.each( data, function( key, value ) {
                                    $('<option>').val(key).text(value).appendTo(select);
                                });
                            });
                        }
                    }
                    </script>";
                $str.= '<div id="d-'.$domanda['id'].'-1" style="display: none">
                    <div class="form-group">
                        <label for="d-'.$domanda['id'].'">Tipologia</label>
                        <select class="form-control" id="d-'.$domanda['id'].'-2" name="d-'.$domanda['id'].'-2"></select>
                    </div>';
                //mostra altre domande dello stesso campo d'azione
                $str.= caricaSecondoLivello($db, 1, $domanda['id']);
                break;

        }
        $str.= '</div>';
        return $str;
    }

    function caricaSecondoLivello($db, $formId, $domanda) {
        $str= "";
        if($num = GetNumeroSecondoLivello($db, $formId, $domanda))
            if(mysqli_num_rows($num) > 0)
                while($nn = mysqli_fetch_array($num)){
                    $numero = $nn['opzioneId'];
                    $str.= "<div id='v-".$numero."' style='display: none'>";
                    if($domande = GetDomandeSecondoLivello($db, $formId, $domanda, $numero ))
                        if(mysqli_num_rows($domande) > 0) 
                            while($dom = mysqli_fetch_array($domande))
                                $str.= visualizzaDomanda($db, $dom);
                    $str.="</div>";    
                }
        return $str;
    }

    function caricaOpzioniGenitore($db, $i, $ii, $iii){
        $str="";
        if ($opzioni = GetOpzioniGenitore($db, 3, 2, 11))
            if(mysqli_num_rows($opzioni) > 0)
                while($riga = mysqli_fetch_array($opzioni))
                    $str.= '<option value="'.$riga['nome'].'">'.$riga['descrizione'].'</option>';
        return $str;        
    }

    function caricaOpzioni($db, $id) {
        $str = "";
        if($opzioni = getOpzioni($db, $id))
            if(mysqli_num_rows($opzioni) > 0)
                while($riga = mysqli_fetch_array($opzioni))
                    $str.= '<option value="'.$riga['nome'].'">'.$riga['descrizione'].'</option>';
        return $str;        
    }

    function intestazioneDomanda($domanda) {
        $obbligatorio = ($domanda['obbligatorio']==1)?"*":"";
        $str = '<div class="form-group">
            <label for="d-'.$domanda['id'].'">'.$domanda['descrizione'].'</label>
            <span class="caution">'.$obbligatorio.'</span>';
        if ($domanda['suggerimento'] != "")    
            $str.= '<span class="fa fa-info pl-5" data-toggle="tooltip" data-placement="right" title="'.$domanda['suggerimento'].'"> info</span>';
        return $str;
    }
?>