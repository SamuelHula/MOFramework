let lastScrollY = window.scrollY;

window.addEventListener('scroll', () => {
      const nav = document.getElementById('nav_bar');
      const currentScrollY = window.scrollY;
      
      if (currentScrollY > lastScrollY && currentScrollY > 100) {
         // Scrolling down - hide nav
         nav.style.transform = 'translateY(-100%)';
      } else if (currentScrollY < lastScrollY && currentScrollY > 50) {
         // Scrolling up - show nav
         nav.style.transform = 'translateY(0)';
      }
      
      lastScrollY = currentScrollY;
});

// Initialize transition
document.addEventListener('DOMContentLoaded', () => {
   document.getElementById('nav_bar').style.transition = 'transform 0.3s ease';
});