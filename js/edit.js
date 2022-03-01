ready(() => {
    /** The follwing requires JQuery and JQueryUI */
    $('#success-alert').fadeTo(2000, 500).slideUp(500, function() {
        $('#success-alert').slideUp(500)
    })

    const tagify_keywords_en = new Tagify(document.querySelector('#keywords_en'))
    tagify_keywords_en.on('input', keywords_autocomplete('en'))
    const tagify_keywords_it = new Tagify(document.querySelector('#keywords_it'))
    tagify_keywords_it.on('input', keywords_autocomplete('it'))

    $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        const hash = $(e.target).attr('data-bs-target')
        if (history.replaceState) {
          history.replaceState(null, null, hash.split('-')[0])
        } else {
          location.hash = hash
        }
      })

    const hash = location.hash
    if (hash) {
        $('.nav-link[data-bs-target="' + hash + '-container"]').tab('show')
    }
})
