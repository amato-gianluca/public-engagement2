let timer;

function searchterms_change_listener() {
    document.getElementById('researchers_list').innerHTML = `
        <div class="spinner-border" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    `
    clearTimeout(timer)
    timer = setTimeout(searchterms_update, 500)
}

async function searchterms_update() {
    const search = document.getElementById('searchterms').value
    const searchParams = new URLSearchParams({ search: search })
    const results_raw = await fetch('api/iris_get_docenti.php?' + searchParams)
    const results = await results_raw.json()
    const researchers_list = document.getElementById('researchers_list')
    researchers_list.innerHTML = ''
    if (results.length) {
        for (const author of results) {
            researchers_list.innerHTML += `<li class='list-group-item'>${author.name} ${author.score}`
        }
    } else {
        researchers_list.innerHTML += '<div class="alert alert-dark" role="alert">Nessun risultato trovato</div>'
    }
}

function ready(callback) {
    if (document.readyState != 'loading') callback()
    else document.addEventListener('DOMContentLoaded', callback)
}
