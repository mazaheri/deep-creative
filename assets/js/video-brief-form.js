(function () {
    var WA_URL = 'https://wa.me/971563955262';

    /* ── Country-code picker ─────────────────────────────────── */
    var COUNTRIES = [
        ['+971', 'UAE'],          ['+966', 'Saudi Arabia'], ['+965', 'Kuwait'],
        ['+974', 'Qatar'],        ['+973', 'Bahrain'],      ['+968', 'Oman'],
        ['+962', 'Jordan'],       ['+961', 'Lebanon'],      ['+20',  'Egypt'],
        ['+90',  'Turkey'],       ['+91',  'India'],        ['+92',  'Pakistan'],
        ['+1',   'US / Canada'],  ['+44',  'UK'],           ['+33',  'France'],
        ['+49',  'Germany'],      ['+61',  'Australia'],
    ];

    function initPhoneField() {
        var tel = document.querySelector(
            '.brief-card input[type="tel"], .brief-card input.wpcf7-tel'
        );
        if (!tel) return;

        /* Fix label — search all labels in the card for "WhatsApp" text */
        document.querySelectorAll('.brief-card label').forEach(function (lbl) {
            if (/whatsapp/i.test(lbl.textContent)) {
                Array.from(lbl.childNodes).forEach(function (node) {
                    if (node.nodeType === 3) {
                        node.textContent = node.textContent.replace(/whatsapp/gi, 'Contact Number');
                    }
                });
            }
        });

        /* Remove conflicting placeholder */
        tel.placeholder = '50 123 4567';
        tel.autocomplete = 'tel-national';

        /* Datalist for suggestions — user can still type any code freely */
        var listId = 'phone-cc-list';
        if (!document.getElementById(listId)) {
            var dl = document.createElement('datalist');
            dl.id = listId;
            COUNTRIES.forEach(function (c) {
                var opt = document.createElement('option');
                opt.value = c[0];
                opt.label = c[1];
                dl.appendChild(opt);
            });
            document.body.appendChild(dl);
        }

        /* Country-code input — lives OUTSIDE .wpcf7-form-control-wrap so CF7 ignores it */
        var cc = document.createElement('input');
        cc.type = 'text';
        cc.className = 'phone-cc';
        cc.setAttribute('list', listId);
        cc.setAttribute('aria-label', 'Country code');
        cc.setAttribute('autocomplete', 'tel-country-code');
        cc.value = '+971';
        cc.placeholder = '+971';

        /*
         * Wrap the cc input + the entire .wpcf7-form-control-wrap span together.
         * This keeps the tel input untouched inside its CF7 span while the cc
         * input sits cleanly alongside it as a sibling — no CF7 interference.
         */
        var cfWrap = tel.closest('.wpcf7-form-control-wrap') || tel.parentNode;
        var fieldContainer = cfWrap.parentNode;

        var wrapper = document.createElement('div');
        wrapper.className = 'phone-field-wrap';
        fieldContainer.insertBefore(wrapper, cfWrap);
        wrapper.appendChild(cc);
        wrapper.appendChild(cfWrap);

        /* On submit: strip leading zeros and prepend country code */
        var form = tel.closest('form');
        if (form) {
            form.addEventListener('submit', function () {
                var num = tel.value.trim();
                if (num && !/^\+/.test(num)) {
                    var code = (cc.value.trim() || '+971').replace(/(?!^\+)[^\d]/g, '');
                    tel.value = code + num.replace(/^0+/, '');
                }
            }, true);
        }
    }

    document.addEventListener('DOMContentLoaded', initPhoneField);


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
