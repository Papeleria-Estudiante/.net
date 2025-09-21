// Toggle menú responsive
function toggleMenu() {
    document.getElementById('menu').classList.toggle('active');
}

// Animaciones al hacer scroll
function revealOnScroll() {
    const elementos = document.querySelectorAll('.fade-in, .card');
    const ventanaAltura = window.innerHeight;
    elementos.forEach(el => {
        const top = el.getBoundingClientRect().top;
        if(top < ventanaAltura - 50) el.classList.add('visible');
    });
}
window.addEventListener('scroll', revealOnScroll);
window.addEventListener('load', revealOnScroll);
