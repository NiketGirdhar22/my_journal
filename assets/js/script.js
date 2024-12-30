function scrollToAbout() {
    document.getElementById("about").scrollIntoView({ behavior: 'smooth' });
}

document.getElementById('hamburger').addEventListener('click', function() {
    document.getElementById('nav-links').classList.toggle('open');
    this.classList.toggle('open');
});
