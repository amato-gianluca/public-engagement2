<?php
require_once 'library.php';

if (!isset($_SESSION['username'])) {
    redirect_browser('login.php');
}

$userid = pe_id_from_username($_SESSION['username']);

if (! $userid) {
  if (isset($_GET['accept'])) {
      $userid = pe_researcher_create($_SESSION['username']);
  } elseif (isset($_GET['reject'])) {
      unset($_SESSION['username']);
      redirect_browser('.');
  }
}

if ($userid) {
    unset($_SESSION['username']);
    $_SESSION['userid'] = $userid;
    $target = ($_GET['tgt'] ?? '') == 'edit' ? 'edit.php' : '.';
    redirect_browser($target);
}

require_once 'templates/header.php';
?>
                <div class="mb-5">
                    <h2 style="text-align: center;">Normativa per il trattamento dei dati personali</h2>
                </div>

                <div class="mb-3">
                    Per poter utilizzare il Catalogo delle Competenze, è necessario accettare la relativa
                    <em>informativa sul trattamento dei dati personali</em>, riportata qui sotto. Potete in
                    alternativa consultare la
                    <a href="https://www.unich.it/sites/default/files/informativa_sul_trattamento_dei_dati_personali_per_il_sito_web_di_public_engagement.pdf"
                    target="_blank">versione ufficiale in PDF</a>,
                    che si trova sulla pagina del sito web di Ateneo dedicata alla <a href="https://www.unich.it/privacy" target="_blank">privacy</a>.
                    <hr>
                </div>

                <div class="mv-3">
                    <p>
                    Per  facilitare  la  lettura  e  la  comprensione  del  suo  contenuto,  l’informativa  è
                    suddivisa  in  specifici paragrafi e segnatamente:
                    <ol>
                        <li>finalità del trattamento
                        <li>base giuridica del trattamento;
                        <li>modalità del trattamento;
                        <li>natura del conferimento dei dati;
                        <li>comunicazione dei dati personali ed eventuali trasferimenti all’estero;
                        <li>soggetti del trattamento;
                        <li>tempi di conservazione dei dati;
                        <li>diritti dell’interessato e modalità di esercizio;
                        <li>responsabile della protezione dei dati personali.
                    </ol>

                    <p class="h6">1. Finalità del trattamento</p>
                    <p>
                    Secondo    quanto    previsto    dalla    normativa    vigente,    desideriamo    informarLa    che
                    l’Università  G. d’Annunzio di Chieti - Pescara (di seguito, denominata più semplicemente
                    “l’Università”), nel momento in cui Lei dichiara di aderire ai servizi offerti dal sito web di
                    Public Engagement, procederà al trattamento dei dati personali da Lei forniti
                    volontariamente all’interno del sito o estratti da altre banche dati dell’Università. Per  una
                    Sua  maggiore  consapevolezza,  si  richiamano di  seguito  le  principali  definizioni  della
                    normativa    vigente.    Per    “dato    personale”    si    intende    “qualsiasi    informazione
                    riguardante  una persona  fisica  identificata  o  identificabile  («interessato»);  si  considera
                    identificabile    la   persona  fisica   che    può   essere   identificata,   direttamente   o
                    indirettamente,  con  particolare  riferimento  a  un identificativo come il nome, un numero
                    di  identificazione,  dati  relativi all’ubicazione, un identificativo online  o  a  uno  o  più
                    elementi  caratteristici  della sua  identità fisica, fisiologica, genetica,  psichica, economica,
                    culturale  o  sociale”;  mentre  per  “trattamento”  si  intende  “qualsiasi  operazione  o
                    insieme  di  operazioni,  compiute  con  o  senza  l’ausilio  di  processi  automatizzati  e
                    applicate    a    dati  personali    o    insiemi    di    dati    personali,    come    la    raccolta,
                    la registrazione,  l’organizzazione,  la strutturazione,  la  conservazione,  l’adattamento  o  la
                    modifica,    l’estrazione,    la    consultazione,    l’uso,  la      comunicazione      mediante
                    trasmissione,      diffusione  o      qualsiasi      altra      forma      di      messa      a  disposizione,  il
                    raffronto o l’interconnessione, la limitazione, la cancellazione o la distruzione”.

                    <p>
                    L’Università  procede  al  trattamento  dei  Suoi  dati  personali  e  segnatamente  dati
                    anagrafici,    dati    inerenti    al    Suo  status  professionale  e  dipartimento  di  afferenza,    dati
                    curricolare,  metadati  delle  pubblicazioni  censite sul sistema  Iris dell’Università, parole
                    chiave  ed  altri  metadati  sulla  propria  attività  di  ricerca    esplicitamente  inseriti  nella  parte
                    riservata del sito di Public Engagement, esclusivamente  per  lo  svolgimento  delle attività
                    di seguito indicate:
                    <ul>
                    <li> implementazione del sito web di Public Engagement, ovvero un motore di ricerca e
                    un  indice  alfabetico  per  la  diffusione  dell’attività  scientifica  che  si  svolge
                    all’interno dell’Università.
                    </ul>

                    <p>
                    Il    trattamento    dei    Suoi    dati    personali    è    effettuato    nel    rispetto    della    normativa
                    vigente,  dei  diritti, delle   libertà   fondamentali   e   della   dignità   dell’interessato,   con
                    particolare      riferimento      alla  riservatezza,  all’identità  personale  ed  al  diritto  alla
                    protezione  dei  dati  personali,  nonché  dei  principi  di  liceità,  correttezza,  pertinenza,  non
                    eccedenza e finalità.

                    <p class="h6">2. Base giuridica del trattamento
                    <p>
                    Per  le  finalità  di  cui  al  precedente  par.  1,  i  dati  personali  da  Lei  forniti  sono
                    trattati    ricorrendo  le  condizioni    di    cui  all’art.  6,  par.  1°,  lettere b) ed  e)  del
                    Regolamento  (UE)  2016/679,  in quanto necessari all’espletamento delle finalità.

                    <p class="h6">3. Modalità del trattamento
                    <p>
                    Il  trattamento  dei  Suoi  dati  personali  è  effettuato,  per  mezzo  delle  operazioni   o
                    complesso    di  operazioni    indicate    dalla    soprarichiamata    definizione    normativa    di
                    “trattamento”,  con  o  senza l’ausilio di strumenti elettronici.

                    <p class="h6">4. Natura del conferimento dei dati
                    <p>
                    L’accettazione al trattamento dei dati personali è necessaria per poter essere inseriti nel
                    servizio  web  di  Public  Engagement.  Il    Suo    eventuale    rifiuto    di    conferire    i    dati
                    personali comporterà l’esclusione del Suo nominativo dai risultati del sito.

                    <p class="h6">5. Comunicazione dei dati personali ed eventuali trasferimenti all’estero
                    I  Suoi  dati  personali  saranno  trattati  dalle  strutture  e  dei  dipendenti  dell’Università
                    deputati allo sviluppo e al deployment del sito web di Public Engagement, esclusivamente
                    per  le  finalità  strumentali  alla  gestione  del  rapporto  di  lavoro  o  di  servizio.

                    <p clss="h6">6. Soggetti del trattamento
                    <p>
                    Il  titolare  del  trattamento  dei  dati  è  l’Università  G.  d’Annunzio,  con  sede  in  via
                    Vestini  31,  66100 Chieti,    legalmente    rappresentata    dal    Magnifico    Rettore,    tel.
                    0871    -    3556010,    e-mail <a href="mailto:rettore@unich.it">rettore@unich.it</a>; pec.:
                    <a href="mailto:ateneo@pec.unich.it">ateneo@pec.unich.it</a>.
                    L’elenco dei responsabili del trattamento è disponibile sul sito dell’Università:
                    <a href="https://www.unich.it/">www.unich.it</a>.

                    <p clss="h6">7. Tempi di conservazione dei dati oggetto di trattamento
                    I    Suoi    dati    personali  saranno  conservati  finché  dura  il  rapporto  di  lavoro  con
                    l’Università.  I  dati  provenienti  da  altre  banche  dati  dell’Università  saranno
                    automaticamente aggiornati con cadenza almeno settimanale.


                    <p clss="h6">8. Diritti dell’interessato e modalità di esercizio
                    Secondo la normativa vigente, Lei, in qualità di interessato, ha il diritto:
                    <ul>
                    <li>(di)  ottenere  dal  titolare  del  trattamento  la  conferma  che  sia  o  meno  in  corso  un
                    trattamento di dati personali che La riguarda;
                    <li>(di) accesso ai propri dati personali ed in particolare  alle seguenti informazioni: le
                    finalità  del  trattamento;  le  categorie  di  dati  personali  oggetto  di  trattamento;  i
                    destinatari  o  le  categorie  di  destinatari    a    cui    i    dati    personali    sono    stati    o
                    saranno  comunicati,  in  particolare  se destinatari  di paesi  terzi  o  organizzazioni
                    internazionali;  quando  possibile,  il  periodo  di conservazione  dei  dati personali
                    previsto    oppure,    se    non    è    possibile,    i    criteri    utilizzati    per  determinare    tale
                    periodo;  l’eventuale    esistenza    di  un    processo    decisionale    automatizzato,
                    compresa    la    profilazione,    e    nel  qual    caso,    informazioni        significative    sulla
                    logica  utilizzata, nonché l’importanza e le conseguenze previste di tale trattamento
                    per l’interessato;
                    <li>(di) rettifica dei dati personali inesatti ed integrazione dei dati personali incompleti;
                    <li>(alla)  cancellazione  dei  dati  o  alla  limitazione  del  trattamento  che  La  riguarda,
                    ricorrendone le condizioni previste dalla normativa vigente;
                    <li>(alla) portabilità dei dati personali, ricorrendone le circostanze;
                    <li>(di) opporsi al trattamento;
                    <li>(di)    proporre    reclamo,    ricorrendone    le    circostanze    previste    dalla    normativa
                    vigente,  al Garante per la protezione dei dati personali, quale Autorità di controllo
                    operante nel nostro ordinamento.
                    </ol>

                    <p>
                    Per  l’esercizio  dei  diritti  di  cui  al  punto  precedente,  Lei  può  inviare  una
                    comunicazione  scritta, indirizzata al titolare del trattamento: Università G. d’Annunzio,
                    Via Vestini 31, 66100 Chieti, ovvero  AREA DEL PERSONALE 6 al  seguente  indirizzo:
                    <a href="mailto:ateneo@pec.unich.it">ateneo@pec.unich.it</a>.  In  ogni  caso,  e  a  maggiore  tutela  dei  Suoi  diritti,  si prega di
                    voler allegare alla richiesta copia di un documento di riconoscimento in corso di validità.

                    <p clss="h6">9. Responsabile della protezione dei dati personali
                    <p>
                    Il  Responsabile  della  protezione  dei  dati  personali  è  il  Prof.  Gianluca  Bellomo.
                    Per    contattare    il  Responsabile    della    protezione    dei    dati    personali,    Lei    può    inviare
                    un’e-mail  al  seguente  indirizzo: <a href="mailto:dpo@unich.it">dpo@unich.it</a>; <a href="mailto:dpo@pec.unich.it">dpo@pec.unich.it</a>;
                    tel.: 085 – 4537842.

                    <p class="h6">Titolare del trattamento:
                    <p>
                    Università degli Studi “G. d’Annunzio” di Chieti – Pescara
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
