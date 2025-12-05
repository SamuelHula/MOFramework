document.addEventListener('DOMContentLoaded', () => {
   const elements = document.querySelectorAll('.scroll-effect');
   const observer = new IntersectionObserver(
   (entries) => {
      entries.forEach(entry => {
         entry.target.classList.toggle('visible', entry.isIntersecting);
      });
   },
   {
      root: null,
      rootMargin: '0px',
      threshold: 0.2
   }
   );
   elements.forEach(element => observer.observe(element));
});