$(document).ready(function() {
    $("#success-alert").fadeTo(2000, 500).slideUp(500, function(){
        $("#success-alert").slideUp(500);
    });

    const tagify_keywords_en = new Tagify(document.querySelector('#keywords_en'));
    const tagify_keywords_it = new Tagify(document.querySelector('#keywords_it'));
});
