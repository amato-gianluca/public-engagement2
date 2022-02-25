function ready(callback) {
    if (document.readyState != 'loading') callback()
    else document.addEventListener('DOMContentLoaded', callback)
}

function encodeHTML(html){
    return document.createElement('div')
        .appendChild(document.createTextNode(html))
        .parentNode
        .innerHTML
}

function keywords_autocomplete (lang = '') {
    let controller;

    return (e) => {
        const tagify = e.detail.tagify
        const value = e.detail.value
        tagify.whitelist = null

        // https://developer.mozilla.org/en-US/docs/Web/API/AbortController/abort
        controller && controller.abort()
        controller = new AbortController()

        // show loading animation and hide the suggestions dropdown
        tagify.loading(true).dropdown.hide()

        const parameters = new URLSearchParams({
            lang: lang,
            value: value,
        })
        fetch('api/autocomplete.php?' + parameters, { signal: controller.signal })
        .then(RES => RES.json())
        .then((newWhitelist) => {
            tagify.whitelist = newWhitelist.map((x) => x.keyword) // update inwhitelist Array in-place
            tagify.loading(false).dropdown.show(value) // render the suggestions dropdown
        })
        .catch(e => {
            if (e.name !== 'AbortError')  console.log("error")
        })
    }
}
