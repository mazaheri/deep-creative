/* DeepStudio particle animation — logo and text particle canvas */

const canvas = document.getElementById('particleCanvas');
const ctx = canvas.getContext('2d');
const logoCanvas = document.getElementById('logo-canvas');
const lctx = logoCanvas.getContext('2d', { willReadFrequently: true });
const textCanvas = document.getElementById('text-canvas');
const tctx = textCanvas.getContext('2d', { willReadFrequently: true });

let width, height;
let particles = [];
let logoParticles = [];
let textParticles = [];
let meteors = [];
let mouse = { x: -1000, y: -1000, radius: 80 };

const PARTICLE_COUNT = 1500;
const METEOR_COUNT = 15;
const COLORS = ['#0ea5e9', '#7dd3fc', '#bae6fd', '#38bdf8'];

const LOGO_SRC = (typeof deepstudioData !== 'undefined')
    ? deepstudioData.logoSrc
    : 'assets/images/deep-logo.png';

class Particle {
    constructor(x, y, isInteractive = false, baseX = null, baseY = null, canvasRef = null) {
        this.x = x || Math.random() * width;
        this.y = y || Math.random() * height;
        this.baseX = baseX !== null ? baseX : this.x;
        this.baseY = baseY !== null ? baseY : this.y;
        this.size = isInteractive ? 1.2 : Math.random() * 1.5 + 0.5;
        this.color = isInteractive ? '#7dd3fc' : COLORS[Math.floor(Math.random() * COLORS.length)];
        this.vx = (Math.random() - 0.5) * 0.5;
        this.vy = (Math.random() - 0.5) * 0.5;
        this.isInteractive = isInteractive;
        this.canvasRef = canvasRef;
        this.density = (Math.random() * 20) + 5;
        this.opacity = isInteractive ? 0 : Math.random() * 0.5 + 0.2;

        const behaviors = ['neutral', 'flee', 'seek'];
        this.behavior = isInteractive ? 'none' : behaviors[Math.floor(Math.random() * behaviors.length)];
    }

    draw(context) {
        context.fillStyle = this.color;
        context.globalAlpha = this.opacity;
        context.beginPath();
        context.arc(this.x, this.y, this.size, 0, Math.PI * 2);
        context.fill();
    }

    update() {
        if (!this.isInteractive) {
            this.x += this.vx;
            this.y += this.vy;
            if (this.x < 0 || this.x > width) this.vx *= -1;
            if (this.y < 0 || this.y > height) this.vy *= -1;
            this.y += Math.sin(Date.now() * 0.001 + this.x * 0.005) * 0.2;

            if (mouse.x !== -1000 && mouse.y !== -1000) {
                let dx = this.x - mouse.x;
                let dy = this.y - mouse.y;
                let dist = Math.sqrt(dx * dx + dy * dy);

                if (this.behavior === 'flee' && dist < mouse.radius * 1.5) {
                    this.x += (dx / dist) * 1.5;
                    this.y += (dy / dist) * 1.5;
                } else if (this.behavior === 'seek' && dist < mouse.radius * 2 && dist > 40) {
                    this.x -= (dx / dist) * 1.0;
                    this.y -= (dy / dist) * 1.0;
                }
            }
        } else {
            const rect = this.canvasRef.getBoundingClientRect();
            const globalX = this.x + rect.left;
            const globalY = this.y + rect.top;

            let dx = mouse.x - globalX;
            let dy = mouse.y - globalY;
            let distance = Math.sqrt(dx * dx + dy * dy);

            if (distance < mouse.radius) {
                let forceDirectionX = dx / distance;
                let forceDirectionY = dy / distance;
                let force = (mouse.radius - distance) / mouse.radius;
                let moveX = forceDirectionX * force * this.density;
                let moveY = forceDirectionY * force * this.density;

                this.x -= moveX;
                this.y -= moveY;
                this.opacity = 1;
            } else {
                if (this.x !== this.baseX) {
                    let dx = this.x - this.baseX;
                    this.x -= dx / 15;
                }
                if (this.y !== this.baseY) {
                    let dy = this.y - this.baseY;
                    this.y -= dy / 15;
                }
                this.opacity = Math.min(1, this.opacity + 0.02);
            }
        }
    }
}

class Meteor {
    constructor() {
        this.reset(true);
    }

    reset(randomTime = false) {
        this.x = Math.random() * width;
        this.y = Math.random() * height;
        this.vx = (Math.random() - 0.5) * 0.3;
        this.vy = (Math.random() - 0.5) * 0.3;
        this.state = 'hidden';
        this.history = [];
        this.opacity = 0;
        this.size = Math.random() * 1.2 + 1.2;
        this.timer = randomTime ? Math.random() * 300 + 50 : Math.random() * 150 + 50;
        this.life = 0;
    }

    update() {
        if (this.state === 'hidden') {
            this.timer--;
            if (this.timer <= 0) {
                this.state = 'wandering';
                this.opacity = 0;
                this.life = Math.random() * 200 + 100;
            }
            return;
        }

        this.history.push({ x: this.x, y: this.y });
        if (this.history.length > (this.state === 'shooting' ? 15 : 6)) {
            this.history.shift();
        }

        this.x += this.vx;
        this.y += this.vy;

        if (this.x < 0 || this.x > width || this.y < 0 || this.y > height) {
            this.reset();
            return;
        }

        if (this.state === 'wandering') {
            this.life--;
            if (this.opacity < 0.6) this.opacity += 0.01;

            let dx = this.x - mouse.x;
            let dy = this.y - mouse.y;
            let dist = Math.sqrt(dx * dx + dy * dy);

            if (dist < mouse.radius * 1.5) {
                this.state = 'shooting';
                let angle = Math.atan2(dy, dx) + (Math.random() - 0.5) * 0.5;
                let speed = Math.random() * 5 + 5;
                this.vx = Math.cos(angle) * speed;
                this.vy = Math.sin(angle) * speed;
            } else if (this.life <= 0) {
                this.opacity -= 0.01;
                if (this.opacity <= 0) this.reset();
            }
        } else if (this.state === 'shooting') {
            this.opacity -= 0.015;
            if (this.opacity <= 0) this.reset();
        }
    }

    draw(context) {
        if (this.state === 'hidden' || this.opacity <= 0) return;

        context.beginPath();
        if (this.history.length > 0) {
            context.moveTo(this.history[0].x, this.history[0].y);
            for (let i = 1; i < this.history.length; i++) {
                context.lineTo(this.history[i].x, this.history[i].y);
            }
        }
        context.lineTo(this.x, this.y);

        context.strokeStyle = `rgba(255, 255, 255, ${this.opacity * 0.6})`;
        context.lineWidth = this.size;
        context.lineCap = 'round';
        context.shadowBlur = this.state === 'shooting' ? 15 : 5;
        context.shadowColor = '#ffffff';
        context.stroke();
        context.shadowBlur = 0;

        context.fillStyle = `rgba(255, 255, 255, ${this.opacity})`;
        context.beginPath();
        context.arc(this.x, this.y, this.size, 0, Math.PI * 2);
        context.fill();
    }
}

function resize() {
    width = canvas.width = window.innerWidth;
    height = canvas.height = window.innerHeight;

    logoCanvas.width = 250;
    logoCanvas.height = 250;

    textCanvas.width = 400;
    textCanvas.height = 100;

    init();
}

function init() {
    particles = [];
    meteors = [];
    for (let i = 0; i < PARTICLE_COUNT; i++) {
        particles.push(new Particle());
    }
    for (let i = 0; i < METEOR_COUNT; i++) {
        meteors.push(new Meteor());
    }
    loadLogo();
    initText();
}

function loadLogo() {
    const img = new Image();
    img.crossOrigin = 'Anonymous';
    img.src = LOGO_SRC;
    img.onload = () => {
        const tempCanvas = document.createElement('canvas');
        const tempCtx = tempCanvas.getContext('2d');
        tempCanvas.width = 250;
        tempCanvas.height = 250;

        const scale = Math.min(200 / img.width, 200 / img.height);
        const x = (250 - img.width * scale) / 2;
        const y = (250 - img.height * scale) / 2;
        tempCtx.drawImage(img, x, y, img.width * scale, img.height * scale);

        const imageData = tempCtx.getImageData(0, 0, 250, 250).data;
        logoParticles = [];
        for (let y = 0; y < 250; y += 4) {
            for (let x = 0; x < 250; x += 4) {
                const alpha = imageData[(y * 250 + x) * 4 + 3];
                if (alpha > 128) {
                    logoParticles.push(new Particle(x, y, true, x, y, logoCanvas));
                }
            }
        }
    };
}

function initText() {
    const tempCanvas = document.createElement('canvas');
    const tempCtx = tempCanvas.getContext('2d');
    tempCanvas.width = 400;
    tempCanvas.height = 100;

    tempCtx.fillStyle = 'white';
    tempCtx.font = '700 40px Inter';
    tempCtx.textAlign = 'center';
    tempCtx.textBaseline = 'middle';
    tempCtx.letterSpacing = '10px';
    tempCtx.fillText('CREATIVE', 200, 50);

    const imageData = tempCtx.getImageData(0, 0, 400, 100).data;
    textParticles = [];
    for (let y = 0; y < 100; y += 3) {
        for (let x = 0; x < 400; x += 3) {
            const alpha = imageData[(y * 400 + x) * 4 + 3];
            if (alpha > 128) {
                textParticles.push(new Particle(x, y, true, x, y, textCanvas));
            }
        }
    }
}

function animate() {
    ctx.clearRect(0, 0, width, height);
    lctx.clearRect(0, 0, 250, 250);
    tctx.clearRect(0, 0, 400, 100);

    particles.forEach(p => {
        p.update();
        p.draw(ctx);
    });

    meteors.forEach(m => {
        m.update();
        m.draw(ctx);
    });

    logoParticles.forEach(p => {
        p.update();
        p.draw(lctx);
    });

    textParticles.forEach(p => {
        p.update();
        p.draw(tctx);
    });

    requestAnimationFrame(animate);
}

window.addEventListener('resize', resize);
window.addEventListener('mousemove', (e) => {
    mouse.x = e.clientX;
    mouse.y = e.clientY;
});

resize();
animate();
