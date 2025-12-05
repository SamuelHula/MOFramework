<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Thank You - Code Library</title>
   <link rel="stylesheet" href="../css/general.css">
   <link rel="stylesheet" href="../css/home.css">
   <style>
      .confirmation-container {
         min-height: 100vh;
         display: flex;
         align-items: center;
         justify-content: center;
         background: linear-gradient(135deg, var(--back-light) 0%, #e8f4f8 50%, var(--back-dark) 100%);
         position: relative;
         overflow: hidden;
      }
      .confirmation-content {
         text-align: center;
         background: white;
         padding: 4rem 3rem;
         border-radius: 20px;
         box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
         position: relative;
         z-index: 2;
         max-width: 500px;
         width: 90%;
      }
      .confirmation-icon {
         width: 100px;
         height: 100px;
         background: linear-gradient(135deg, var(--primary), var(--secondary));
         border-radius: 50%;
         display: flex;
         align-items: center;
         justify-content: center;
         margin: 0 auto 2rem;
         color: white;
         animation: bounce 2s infinite;
      }
      .confirmation-content h1 {
         font-size: 3rem;
         margin-bottom: 1rem;
         color: var(--text-color);
      }
      .confirmation-content h2 {
         font-size: 1.5rem;
         margin-bottom: 1.5rem;
         color: var(--text-color);
         opacity: 0.8;
      }
      .confirmation-content p {
         font-size: 1.1rem;
         margin-bottom: 2.5rem;
         line-height: 1.6;
         color: var(--text-color);
         opacity: 0.7;
      }
      .confirmation-details {
         background: var(--back-light);
         padding: 1.5rem;
         border-radius: 10px;
         margin: 2rem 0;
         text-align: left;
      }
      .confirmation-details h3 {
         margin-bottom: 1rem;
         color: var(--primary);
      }
      .detail-item {
         display: flex;
         justify-content: space-between;
         margin-bottom: 0.5rem;
         padding: 0.5rem 0;
         border-bottom: 1px solid var(--back-dark);
      }
      .detail-label {
         font-weight: bold;
         color: var(--text-color);
         min-width: 80px;
      }
      .detail-value {
         color: var(--text-color);
         opacity: 0.8;
      }
      @keyframes bounce {
         0%, 20%, 50%, 80%, 100% {
            transform: translateY(0);
         }
         40% {
            transform: translateY(-10px);
         }
         60% {
            transform: translateY(-5px);
         }
      }
   </style>
</head>
<body>
   <div class="confirmation-container">
      <div class="floating-balls">
         <div class="ball"></div>
         <div class="ball"></div>
         <div class="ball"></div>
         <div class="ball"></div>
         <div class="ball"></div>
         <div class="ball"></div>
      </div>
      <div class="confirmation-content">
         <div class="confirmation-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="50" height="50" fill="white">
               <path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209L241 337c-9.4 9.4-24.6 9.4-33.9 0l-64-64c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l47 47L335 175c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9z"/>
            </svg>
         </div>
         <h1>Thank You!</h1>
         <h2>Message Sent Successfully</h2>
         <p>We've received your message and will get back to you as soon as possible. Typically, we respond within 24 hours.</p>
         
         <div class="confirmation-details">
            <h3>Message Details</h3>
            <?php if (isset($_GET['name'])): ?>
            <div class="detail-item">
               <span class="detail-label">Name:</span>
               <span class="detail-value"><?php echo htmlspecialchars($_GET['name']); ?></span>
            </div>
            <?php endif; ?>
            <?php if (isset($_GET['email'])): ?>
            <div class="detail-item">
               <span class="detail-label">Email:</span>
               <span class="detail-value"><?php echo htmlspecialchars($_GET['email']); ?></span>
            </div>
            <?php endif; ?>
            <?php if (isset($_GET['subject'])): ?>
            <div class="detail-item">
               <span class="detail-label">Subject:</span>
               <span class="detail-value"><?php echo htmlspecialchars($_GET['subject']); ?></span>
            </div>
            <?php endif; ?>
         </div>

         <div class="btns">
            <a href="../index.php" class="primary_btn">
               <span>Back to Home</span>
            </a>
            <a href="../index.php#contact_form" class="secondary_btn">
               <span>Send Another</span>
            </a>
         </div>
      </div>
   </div>
   <script src="../js/scroll.js"></script>
   <script>
         // Initialize scroll effects
         document.addEventListener('DOMContentLoaded', function() {
            const scrollElements = document.querySelectorAll('.scroll-effect');
            const elementInView = (el, dividend = 1) => {
               const elementTop = el.getBoundingClientRect().top;
               return (
                     elementTop <=
                     (window.innerHeight || document.documentElement.clientHeight) / dividend
               );
            };
            const displayScrollElement = (element) => {
               element.classList.add('visible');
            };
            const handleScrollAnimation = () => {
               scrollElements.forEach((el) => {
                     if (elementInView(el, 1.25)) {
                        displayScrollElement(el);
                     }
               });
            };
            window.addEventListener('scroll', () => {
               handleScrollAnimation();
            });
            // Trigger once on load
            handleScrollAnimation();
         });
   </script>
</body>
</html>