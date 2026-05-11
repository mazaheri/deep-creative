(function () {
    var WA_URL = 'https://wa.me/971563955262';

    var CHECK_SVG = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"'
        + ' stroke-linecap="round" stroke-linejoin="round" width="28" height="28">'
        + '<polyline points="20 6 9 17 4 12"/></svg>';

    /* ── Country code: reveal text input when "Other" is chosen ── */
    function initPhoneCC() {
        var cc       = document.querySelector('.brief-card .phone-cc');
        var ccCustom = document.querySelector('.brief-card .phone-cc-custom');
        if (!cc || !ccCustom) return;

        cc.addEventListener('change', function () {
            if (cc.value === 'other') {
                ccCustom.style.display = '';
                ccCustom.focus();
            } else {
                ccCustom.style.display = 'none';
                ccCustom.value = '';
            }
        });
    }

    /* ── Form submission via fetch ── */
    function initForm() {
        var form = document.getElementById('brief-form');
        if (!form) return;

        form.addEventListener('submit', function (e) {
            e.preventDefault();

            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            var btn = form.querySelector('.brief-submit');
            if (btn) { btn.disabled = true; btn.textContent = 'Sending…'; }

            var data = new FormData(form);
            data.append('action', 'brief_submit');

            fetch(briefData.ajaxUrl, { method: 'POST', body: data })
                .then(function (r) { return r.json(); })
                .then(function (res) {
                    if (res.success) {
                        showThankyou();
                    } else {
                        if (btn) { btn.disabled = false; btn.textContent = 'Submit Brief'; }
                        alert(res.data && res.data.message ? res.data.message : 'Something went wrong. Please try again.');
                    }
                })
                .catch(function () {
                    if (btn) { btn.disabled = false; btn.textContent = 'Submit Brief'; }
                    alert('Network error. Please try again.');
                });
        });
    }

    /* ── Thank-you screen ── */
    function showThankyou() {
        var card = document.querySelector('.brief-card');
        if (!card) return;

        var head    = card.querySelector('.brief-head');
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
    }

    document.addEventListener('DOMContentLoaded', function () {
        initPhoneCC();
        initForm();
    });
})();
