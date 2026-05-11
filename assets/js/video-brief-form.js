(function () {
    var WA_URL = 'https://wa.me/971563955262';


    var CHECK_SVG = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"'
        + ' stroke-linecap="round" stroke-linejoin="round" width="28" height="28">'
        + '<polyline points="20 6 9 17 4 12"/></svg>';

    document.addEventListener('wpcf7mailsent', function () {
        var card = document.querySelector('.brief-card');
        if (!card) return;

        // Keep the brief-head, replace everything after it
        var head = card.querySelector('.brief-head');
        var headHTML = head ? head.outerHTML : '';

        card.innerHTML = headHTML
            + '<div class="brief-thankyou">'
            + '<div class="ty-icon">' + CHECK_SVG + '</div>'
            + '<h2>Brief Received!</h2>'
            + '<p>We\'ll review your project and get back to you within 24 hours with the right creative direction.</p>'
            + '<a class="button primary" href="' + WA_URL + '" target="_blank" rel="noopener noreferrer">'
            + 'Chat on WhatsApp for faster response'
            + '</a>'
            + '</div>';
    }, false);
})();
