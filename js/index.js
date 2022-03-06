let timer, search_field, keywords_field, tagify_keywords_field, search_left, search_right, search_current_page

function search_change_listener() {
    document.getElementById('researchers_list').innerHTML = `
        <div class="spinner-border" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    `
    clearTimeout(timer)
    timer = setTimeout(searchterms_update, 300)
}

async function searchterms_update() {
    const search = search_field.value
    const keywords = keywords_field.value
    const searchParams = new URLSearchParams({ start: start, search: search, keywords: keywords })
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

    if (results.length > 0) {
        search_start.innerHTML = start + 1
        search_end.innerHTML = start + results.length
    } else {
        search_start.innerHTML = ''
        search_start.innerHTML = ''

    }

    const page_params = new URLSearchParams({ 'start': start, 'search': search, 'keywords': keywords_field.value})
    const page_url = window.location.origin + window.location.pathname + '?' + page_params
    history.replaceState(null, null, page_url)
}

ready(function() {
    search_field = document.getElementById('searchterms')
    keywords_field = document.getElementById('keywords')
    tagify_keywords_field = new Tagify(keywords_field)
    search_start = document.getElementById('search-start')
    search_end = document.getElementById('search-end')
    search_left = document.getElementById('search-left')
    search_right = document.getElementById('search-right')

    /*
    const history_state = history.state
    if (history_state) {
        search_field.value = history_state.search
        tagify_keywords_field.addTags(history_state.keywords)
    }*/
    search_field.value = search
    console.log(keywords)
    tagify_keywords_field.addTags(keywords)

    search_field.addEventListener('input',
        (e) => {
            start = 0
            search_change_listener()
        }
    )

    tagify_keywords_field.settings.enforceWhitelist = true
    tagify_keywords_field.on('input', keywords_autocomplete())

    keywords_field.addEventListener('change',
        (e) => {
            start = 0
            search_change_listener()
        }
    )
    search_left.addEventListener('click',
        (e) => {
            start = start < page_size ? 0 : start - page_size
            search_change_listener()
        }
    )
    search_right.addEventListener('click',
        (e) => {
            start += page_size
            search_change_listener()
        }
    )
    search_change_listener()
})
