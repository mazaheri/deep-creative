/* DeepStudio particle animation */

const canvas    = document.getElementById('particleCanvas');
const ctx       = canvas.getContext('2d');
const logoCanvas = document.getElementById('logo-canvas');
const lctx      = logoCanvas ? logoCanvas.getContext('2d', { willReadFrequently: true }) : null;

let width, height;
let particles     = [];
let logoParticles = [];
let meteors       = [];
let mouse = { x: -1000, y: -1000, radius: 80 };

const PARTICLE_COUNT = 1500;
const METEOR_COUNT   = 15;
const COLORS  = ['#0ea5e9', '#7dd3fc', '#bae6fd', '#38bdf8'];
const NEON    = ['#38bdf8', '#2fb8f5', '#38bdf8', '#1ab0f0', '#38bdf8'];
const LOGO_SIZE = 300;

const LOGO_SRC = (typeof deepstudioData !== 'undefined')
    ? deepstudioData.logoSrc
    : 'assets/images/deep-logo.png';

class Particle {
    constructor(x, y, isInteractive = false, baseX = null, baseY = null, canvasRef = null) {
        this.x     = x || Math.random() * width;
        this.y     = y || Math.random() * height;
        this.baseX = baseX !== null ? baseX : this.x;
        this.baseY = baseY !== null ? baseY : this.y;
        this.size  = isInteractive ? 1.2 : Math.random() * 1.5 + 0.5;
        this.color = isInteractive
            ? NEON[Math.floor(Math.random() * NEON.length)]
            : COLORS[Math.floor(Math.random() * COLORS.length)];
        this.vx          = (Math.random() - 0.5) * 0.5;
        this.vy          = (Math.random() - 0.5) * 0.5;
        this.isInteractive = isInteractive;
        this.canvasRef   = canvasRef;
        this.density     = (Math.random() * 20) + 5;
        this.opacity     = isInteractive ? 0 : Math.random() * 0.5 + 0.2;
        const behaviors  = ['neutral', 'flee', 'seek'];
        this.behavior    = isInteractive ? 'none' : behaviors[Math.floor(Math.random() * behaviors.length)];
    }

    draw(context) {
        if (this.isInteractive) {
            /* neon sphere bead — off-centre radial gradient */
            context.save();
            context.globalAlpha = this.opacity;
            context.shadowBlur  = 7;
            context.shadowColor = this.color;
            const ox   = this.x - this.size * 0.35;
            const oy   = this.y - this.size * 0.35;
            const grad = context.createRadialGradient(ox, oy, this.size * 0.05, this.x, this.y, this.size);
            grad.addColorStop(0,    '#ffffff');
            grad.addColorStop(0.38, this.color);
            grad.addColorStop(1,    'rgba(0,180,255,0)');
            context.fillStyle = grad;
            context.beginPath();
            context.arc(this.x, this.y, this.size, 0, Math.PI * 2);
            context.fill();
            context.restore();
        } else {
            context.fillStyle   = this.color;
            context.globalAlpha = this.opacity;
            context.beginPath();
            context.arc(this.x, this.y, this.size, 0, Math.PI * 2);
            context.fill();
        }
    }

    update() {
        if (!this.isInteractive) {
            this.x += this.vx;
            this.y += this.vy;
            if (this.x < 0 || this.x > width)  this.vx *= -1;
            if (this.y < 0 || this.y > height)  this.vy *= -1;
            this.y += Math.sin(Date.now() * 0.001 + this.x * 0.005) * 0.2;
            if (mouse.x !== -1000 && mouse.y !== -1000) {
                const dx = this.x - mouse.x, dy = this.y - mouse.y;
                const dist = Math.sqrt(dx * dx + dy * dy);
                if (this.behavior === 'flee' && dist < mouse.radius * 1.5) {
                    this.x += (dx / dist) * 1.5; this.y += (dy / dist) * 1.5;
                } else if (this.behavior === 'seek' && dist < mouse.radius * 2 && dist > 40) {
                    this.x -= (dx / dist) * 1.0; this.y -= (dy / dist) * 1.0;
                }
            }
        } else {
            const rect     = this.canvasRef.getBoundingClientRect();
            const globalX  = this.x + rect.left;
            const globalY  = this.y + rect.top;
            const dx       = mouse.x - globalX;
            const dy       = mouse.y - globalY;
            const distance = Math.sqrt(dx * dx + dy * dy);
            if (distance < mouse.radius) {
                const force = (mouse.radius - distance) / mouse.radius;
                this.x -= (dx / distance) * force * this.density;
                this.y -= (dy / distance) * force * this.density;
                this.opacity = 1;
            } else {
                if (this.x !== this.baseX) this.x -= (this.x - this.baseX) / 15;
                if (this.y !== this.baseY) this.y -= (this.y - this.baseY) / 15;
                this.opacity = Math.min(1, this.opacity + 0.02);
            }
        }
    }
}

class Meteor {
    constructor() { this.reset(true); }
    reset(randomTime = false) {
        this.x = Math.random() * width; this.y = Math.random() * height;
        this.vx = (Math.random() - 0.5) * 0.3; this.vy = (Math.random() - 0.5) * 0.3;
        this.state = 'hidden'; this.history = []; this.opacity = 0;
        this.size  = Math.random() * 1.2 + 1.2;
        this.timer = randomTime ? Math.random() * 300 + 50 : Math.random() * 150 + 50;
        this.life  = 0;
    }
    update() {
        if (this.state === 'hidden') { if (--this.timer <= 0) { this.state = 'wandering'; this.opacity = 0; this.life = Math.random() * 200 + 100; } return; }
        this.history.push({ x: this.x, y: this.y });
        if (this.history.length > (this.state === 'shooting' ? 15 : 6)) this.history.shift();
        this.x += this.vx; this.y += this.vy;
        if (this.x < 0 || this.x > width || this.y < 0 || this.y > height) { this.reset(); return; }
        if (this.state === 'wandering') {
            this.life--;
            if (this.opacity < 0.6) this.opacity += 0.01;
            const dx = this.x - mouse.x, dy = this.y - mouse.y;
            const dist = Math.sqrt(dx * dx + dy * dy);
            if (dist < mouse.radius * 1.5) {
                this.state = 'shooting';
                const angle = Math.atan2(dy, dx) + (Math.random() - 0.5) * 0.5;
                const speed = Math.random() * 5 + 5;
                this.vx = Math.cos(angle) * speed; this.vy = Math.sin(angle) * speed;
            } else if (this.life <= 0) { this.opacity -= 0.01; if (this.opacity <= 0) this.reset(); }
        } else if (this.state === 'shooting') { this.opacity -= 0.015; if (this.opacity <= 0) this.reset(); }
    }
    draw(context) {
        if (this.state === 'hidden' || this.opacity <= 0) return;
        context.beginPath();
        if (this.history.length > 0) { context.moveTo(this.history[0].x, this.history[0].y); for (let i = 1; i < this.history.length; i++) context.lineTo(this.history[i].x, this.history[i].y); }
        context.lineTo(this.x, this.y);
        context.strokeStyle = `rgba(255,255,255,${this.opacity * 0.6})`; context.lineWidth = this.size; context.lineCap = 'round';
        context.shadowBlur = this.state === 'shooting' ? 15 : 5; context.shadowColor = '#ffffff'; context.stroke(); context.shadowBlur = 0;
        context.fillStyle = `rgba(255,255,255,${this.opacity})`; context.beginPath(); context.arc(this.x, this.y, this.size, 0, Math.PI * 2); context.fill();
    }
}

function resize() {
    width = canvas.width = window.innerWidth;
    height = canvas.height = window.innerHeight;
    if (logoCanvas) { logoCanvas.width = LOGO_SIZE; logoCanvas.height = LOGO_SIZE; }
    init();
}

function init() {
    particles = []; meteors = [];
    for (let i = 0; i < PARTICLE_COUNT; i++) particles.push(new Particle());
    for (let i = 0; i < METEOR_COUNT;   i++) meteors.push(new Meteor());
    if (logoCanvas) loadLogo();
}

function loadLogo() {
    const img = new Image();
    img.crossOrigin = 'Anonymous';
    img.src = LOGO_SRC;
    img.onload = () => {
        const tmp = document.createElement('canvas');
        const tc  = tmp.getContext('2d');
        tmp.width = tmp.height = LOGO_SIZE;
        const scale = Math.min(260 / img.width, 260 / img.height);
        tc.drawImage(img, (LOGO_SIZE - img.width * scale) / 2, (LOGO_SIZE - img.height * scale) / 2, img.width * scale, img.height * scale);
        const data = tc.getImageData(0, 0, LOGO_SIZE, LOGO_SIZE).data;
        logoParticles = [];
        for (let y = 0; y < LOGO_SIZE; y += 4)
            for (let x = 0; x < LOGO_SIZE; x += 4)
                if (data[(y * LOGO_SIZE + x) * 4 + 3] > 128)
                    logoParticles.push(new Particle(x, y, true, x, y, logoCanvas));
    };
}

function animate() {
    ctx.clearRect(0, 0, width, height);
    if (lctx) lctx.clearRect(0, 0, LOGO_SIZE, LOGO_SIZE);
    particles.forEach(p     => { p.update(); p.draw(ctx); });
    meteors.forEach(m       => { m.update(); m.draw(ctx); });
    if (lctx) logoParticles.forEach(p => { p.update(); p.draw(lctx); });
    requestAnimationFrame(animate);
}

window.addEventListener('resize', resize);
window.addEventListener('mousemove', e => { mouse.x = e.clientX; mouse.y = e.clientY; });
resize();
animate();
