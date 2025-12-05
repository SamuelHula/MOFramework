window.addEventListener('scroll', scrollProgress);

function scrollProgress() {
   const winScroll = document.body.scrollTop || document.documentElement.scrollTop;
   const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
   const scrolled = (winScroll / height) * 100;
   document.getElementById('scrollProgress').style.width = scrolled + '%';
}

document.addEventListener('DOMContentLoaded', function() {
   const progressContainer = document.querySelector('.progress-container');
   const progressBar = document.getElementById('scrollProgress');
   
   if (progressContainer && progressBar) {
      progressContainer.style.position = 'fixed';
      progressContainer.style.top = '0';
      progressContainer.style.left = '0';
      progressContainer.style.width = '100%';
      progressContainer.style.height = '4px';
      progressContainer.style.backgroundColor = 'transparent';
      progressContainer.style.zIndex = '10000';
      
      progressBar.style.height = '4px';
      progressBar.style.backgroundColor = 'var(--primary, #00bfff)';
      progressBar.style.width = '0%';
      progressBar.style.transition = 'width 0.3s ease-out';
      progressBar.style.borderRadius = '0 2px 2px 0';
   }
});