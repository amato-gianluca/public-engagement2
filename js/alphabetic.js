let expanded_keywords = [ ]

async function load_researchers(e) {
    const secion = e.target
    const researchers_list = secion.querySelector('.list-group')
    const keyword = secion.dataset.keyword

    if (! expanded_keywords.includes(keyword)) {

        researchers_list.innerHTML = `
            <div class="spinner-border" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        `

        expanded_keywords.push(keyword)

        const searchParams = new URLSearchParams({ search: keyword })
        const results_raw = await fetch('api/esse3_researchers_from_keyword.php?' + searchParams)
        const results = await results_raw.json()

        researchers_list.innerHTML = ''
        for (const author of results) {
            researchers_list.innerHTML += `
                <li class='list-group-item'>
                    <div class="d-flex w-100 justify-content-between">
                    <a href='view.php?matricola=${encodeHTML(encodeURIComponent(author.MATRICOLA))}'>
                        ${encodeHTML(author.NOME)} ${encodeHTML(author.COGNOME)}
                    </a>
                    </div>
                </li>
            `
        }

    }

}

ready(() => {
    document.querySelectorAll('.accordion-collapse').forEach((keyword) => {
        keyword.addEventListener('show.bs.collapse', load_researchers)
    })
    return true
})
