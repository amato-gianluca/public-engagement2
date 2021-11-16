$(document).ready(function() {
    $("#success-alert").fadeTo(2000, 500).slideUp(500, function(){
        $("#success-alert").slideUp(500)
    })

    const tagify_keywords_en = new Tagify(document.querySelector('#keywords_en'))
    tagify_keywords_en.on('input', tagify_autocomplete)
    const tagify_keywords_it = new Tagify(document.querySelector('#keywords_it'))
    tagify_keywords_it.on('input', tagify_autocomplete)

    let controller;

    function tagify_autocomplete(e){
        const tagify = e.detail.tagify
        const value = e.detail.value

        // https://developer.mozilla.org/en-US/docs/Web/API/AbortController/abort
        controller && controller.abort()
        controller = new AbortController()

        // show loading animation and hide the suggestions dropdown
        tagify.loading(true).dropdown.hide()

        const lang =  tagify == tagify_keywords_en ? 'en' : 'it'
        fetch('api/autocomplete.php?lang=' + lang, { signal:controller.signal })
        .then(RES => RES.json())
        .then(function (newWhitelist) {
            tagify.whitelist = newWhitelist // update inwhitelist Array in-place
            tagify.loading(false).dropdown.show(value) // render the suggestions dropdown
        })
    }
})
