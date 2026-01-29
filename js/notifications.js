if (!document.querySelector('#notification-styles')) {
   const style = document.createElement('style');
   style.id = 'notification-styles';
   style.textContent = `
      @keyframes slideIn {
         from { transform: translateX(100%); opacity: 0; }
         to { transform: translateX(0); opacity: 1; }
      }
      @keyframes slideOut {
         from { transform: translateX(0); opacity: 1; }
         to { transform: translateX(100%); opacity: 0; }
      }
   `;
   document.head.appendChild(style);
}

function showNotification(message, type = 'success') {
   const notification = document.createElement('div');
   notification.style.cssText = `
      position: fixed;
      top: 20px;
      right: 20px;
      background: ${type === 'success' ? 'var(--primary)' : '#ff4444'};
      color: white;
      padding: 1rem 1.5rem;
      border-radius: 8px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.2);
      z-index: 10000;
      animation: slideIn 0.3s ease;
   `;
   notification.textContent = message;
   document.body.appendChild(notification);
   
   setTimeout(() => {
      notification.style.animation = 'slideOut 0.3s ease';
      setTimeout(() => notification.remove(), 300);
   }, 3000);
}