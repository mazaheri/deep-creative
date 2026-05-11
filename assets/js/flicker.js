(function () {
    'use strict';

    var wall = document.querySelector('.hero-wall');
    if (!wall) return;

    /* ── Flicker engine ── */
    // Sequences: [brightness, holdMs]
    var sequences = [
        // Quick double-blink
        [
            [0.55, 35],
            [1.00, 20],
            [0.60, 40],
            [1.00, 25],
        ],
        // Long struggle
        [
            [0.40, 50],
            [0.90, 15],
            [0.25, 45],
            [0.80, 20],
            [0.55, 35],
            [1.00, 20],
            [0.70, 30],
            [1.00, 18],
        ],
        // Fast stutter
        [
            [0.65, 25],
            [1.00, 12],
            [0.50, 22],
            [1.00, 14],
            [0.72, 28],
            [1.00, 18],
        ],
        // Single deep dip
        [
            [0.20, 60],
            [0.90, 20],
            [0.50, 30],
            [1.00, 20],
        ],
    ];

    function runFlicker() {
        var seq = sequences[Math.floor(Math.random() * sequences.length)];
        var i   = 0;

        function next() {
            if (i >= seq.length) {
                wall.style.filter = '';
                scheduleNext();
                return;
            }
            wall.style.filter = 'brightness(' + seq[i][0] + ') saturate(.96) contrast(1.05)';
            setTimeout(next, seq[i][1]);
            i++;
        }

        next();
    }

    function scheduleNext() {
        // Short pause between sequences: 0.8 – 2.5 s
        setTimeout(runFlicker, 800 + Math.random() * 1700);
    }

    setTimeout(runFlicker, 800);
})();
