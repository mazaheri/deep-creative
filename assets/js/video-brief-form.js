(function () {
    var WA_URL = 'https://wa.me/971563955262';

    var COUNTRIES = [
        ['+971', 'UAE'],          ['+966', 'Saudi Arabia'], ['+965', 'Kuwait'],
        ['+974', 'Qatar'],        ['+973', 'Bahrain'],      ['+968', 'Oman'],
        ['+962', 'Jordan'],       ['+961', 'Lebanon'],      ['+20',  'Egypt'],
        ['+90',  'Turkey'],       ['+91',  'India'],        ['+92',  'Pakistan'],
        ['+1',   'US / Canada'],  ['+44',  'UK'],           ['+33',  'France'],
        ['+49',  'Germany'],      ['+61',  'Australia'],
    ];

    var CHECK_SVG = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"'
        + ' stroke-linecap="round" stroke-linejoin="round" width="28" height="28">'
        + '<polyline points="20 6 9 17 4 12"/></svg>';

    /* ── Country-code picker injected around CF7's tel field ── */
    function initPhoneField() {
        var tel = document.querySelector(
            '.brief-card input[type="tel"], .brief-card input.wpcf7-tel'
        );
        if (!tel) return;

        tel.placeholder    = '50 123 4567';
        tel.autocomplete   = 'tel-national';

        /* Build the country-code <select> */
        var cc = document.createElement('select');
        cc.className = 'phone-cc';
        cc.setAttribute('aria-label', 'Country code');

        COUNTRIES.forEach(function (c) {
            var opt = document.createElement('option');
            opt.value       = c[0];
            opt.textContent = c[0] + '  ' + c[1];
            if (c[0] === '+971') opt.selected = true;
            cc.appendChild(opt);
        });

        var otherOpt = document.createElement('option');
        otherOpt.value       = 'other';
        otherOpt.textContent = 'Other…';
        cc.appendChild(otherOpt);

        /* Text input revealed when "Other" is chosen */
        var ccCustom = document.createElement('input');
        ccCustom.type        = 'text';
        ccCustom.className   = 'phone-cc-custom';
        ccCustom.placeholder = '+XX';
        ccCustom.setAttribute('aria-label', 'Enter country code');
        ccCustom.style.display = 'none';

        cc.addEventListener('change', function () {
            if (cc.value === 'other') {
                ccCustom.style.display = '';
                ccCustom.focus();
            } else {
                ccCustom.style.display = 'none';
                ccCustom.value = '';
            }
        });

        /* Wrap cc + custom input in a left-column div */
        var ccWrap = document.createElement('div');
        ccWrap.className = 'phone-cc-wrap';
        ccWrap.appendChild(cc);
        ccWrap.appendChild(ccCustom);

        /*
         * Move the entire .wpcf7-form-control-wrap span next to ccWrap.
         * The tel input stays inside its CF7 span — validation keeps working.
         */
        var cfWrap        = tel.closest('.wpcf7-form-control-wrap') || tel.parentNode;
        var fieldContainer = cfWrap.parentNode;

        var wrapper = document.createElement('div');
        wrapper.className = 'phone-field-wrap';
        fieldContainer.insertBefore(wrapper, cfWrap);
        wrapper.appendChild(ccWrap);
        wrapper.appendChild(cfWrap);

        /* On submit: prepend country code, strip leading zero */
        var form = tel.closest('form');
        if (form) {
            form.addEventListener('submit', function () {
                var num  = tel.value.trim();
                if (num && !/^\+/.test(num)) {
                    var code = cc.value === 'other'
                        ? (ccCustom.value.trim() || '+971').replace(/(?!^\+)[^\d]/g, '')
                        : cc.value;
                    tel.value = code + num.replace(/^0+/, '');
                }
            }, true);
        }
    }

    document.addEventListener('DOMContentLoaded', initPhoneField);

    /* ── Thank-you screen after CF7 sends mail ── */
    document.addEventListener('wpcf7mailsent', function () {
        var card = document.querySelector('.brief-card');
        if (!card) return;

        var head     = card.querySelector('.brief-head');
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
