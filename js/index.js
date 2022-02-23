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
    const keywords =document.getElementById('keywords').value
    const searchParams = new URLSearchParams({ search: search, keywords: keywords })
    const results_raw = await fetch('api/search.php?' + searchParams)
    const results = await results_raw.json()
    const researchers_list = document.getElementById('researchers_list')
    var newhtml = ''
    if (results.length) {
        for (const author of results) {
            newhtml += `
                <li class='list-group-item'>
                    <div class="d-flex w-100 justify-content-between">`
            if ('crisId' in author)
                newhtml += `<a href='view.php?crisId=${encodeHTML(encodeURIComponent(author.crisId))}&amp;search=${encodeHTML(encodeURIComponent(search))}'>`
            else if ('matricola' in author)
                newhtml += `<a href='view.php?matricola=${encodeHTML(encodeURIComponent(author.matricola))}&amp;search=${encodeHTML(encodeURIComponent(search))}'>`
            else
                newhtml += `<a href=''>`
            newhtml += `
                        ${encodeHTML(author.name)}
                    </a>
                    <span class="ms-auto">${encodeHTML(author.score.toFixed(2))}</span>
                    </div>
                </li>
            `
        }
        researchers_list.innerHTML = newhtml
    } else {
        researchers_list.innerHTML = '<div class="alert alert-dark" role="alert">Nessun risultato trovato</div>'
    }
}

ready(function() {
    const searchTerms = document.getElementById('searchterms')
    searchTerms.addEventListener('input',searchterms_change_listener)
    searchTerms.dispatchEvent(new Event('input'));

    const keywords = document.getElementById('keywords');
    const tagify_keywords = new Tagify(keywords, {
        enforceWhitelist: true
    })
    tagify_keywords.on('input', keywords_autocomplete())
    keywords.addEventListener('change', searchterms_change_listener)
})
