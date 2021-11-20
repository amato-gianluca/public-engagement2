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
