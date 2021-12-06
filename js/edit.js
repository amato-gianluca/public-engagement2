ready(function() {
    /** The follwing requires JQuery and JQueryUI */
    $('#success-alert').fadeTo(2000, 500).slideUp(500, function() {
        $('#success-alert').slideUp(500)
    })

    const tagify_keywords_en = new Tagify(document.querySelector('#keywords_en'))
    tagify_keywords_en.on('input', keywords_autocomplete('en'))
    const tagify_keywords_it = new Tagify(document.querySelector('#keywords_it'))
    tagify_keywords_it.on('input', keywords_autocomplete('it'))
})
