function searchterms_change_listener() {
    const researchers_list = $('#researchers_list');
    researchers_list.empty();
    researchers_list.append(`
        <div class="spinner-border" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    `);
    clearTimeout($.data(this, 'timer'));
    var wait = setTimeout(searchterms_update, 500);
    $(this).data('timer', wait);
}

async function searchterms_update() {
    const search =  $('#searchterms').val();
    const researchers_list = $('#researchers_list');
    const results = await $.getJSON('api/iris_get_docenti.php', { search: search });
    researchers_list.empty();
    if (results.length) {
        for (const author of results) {
            researchers_list.append(`<li class='list-group-item'>${author.name} ${author.score}`);
        }
    } else {
        researchers_list.append('<div class="alert alert-dark" role="alert">Nessun risultato trovato</div>');
    }
}
