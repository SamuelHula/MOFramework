let lastScrollY = window.scrollY;

window.addEventListener('scroll', () => {
      const nav = document.getElementById('nav_bar');
      const currentScrollY = window.scrollY;
      
      if (currentScrollY > lastScrollY && currentScrollY > 100) {
         nav.style.transform = 'translateY(-100%)';
      } else if (currentScrollY < lastScrollY && currentScrollY > 50) {
         nav.style.transform = 'translateY(0)';
      }
      
      lastScrollY = currentScrollY;
});

document.addEventListener('DOMContentLoaded', () => {
   document.getElementById('nav_bar').style.transition = 'transform 0.3s ease';
});