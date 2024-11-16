function scrollToAbout() {
    document.getElementById("about").scrollIntoView({ behavior: 'smooth' });
}

// JavaScript to toggle the navbar
document.getElementById('hamburger').addEventListener('click', function() {
    // Toggle the 'open' class for both hamburger and nav-links
    document.getElementById('nav-links').classList.toggle('open');
    this.classList.toggle('open');
});
