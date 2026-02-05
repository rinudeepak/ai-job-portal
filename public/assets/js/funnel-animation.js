// Animated Counter Function
function animateCounter(element, target, duration = 1500) {
    const start = 0;
    const increment = target / (duration / 16);
    let current = start;
    
    const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
            element.textContent = target.toLocaleString();
            clearInterval(timer);
        } else {
            element.textContent = Math.floor(current).toLocaleString();
        }
    }, 16);
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Animate funnel stage counts
    const funnelCounts = document.querySelectorAll('.funnel-count[data-count]');
    funnelCounts.forEach((element, index) => {
        const target = parseInt(element.getAttribute('data-count'));
        setTimeout(() => {
            animateCounter(element, target);
        }, index * 200);
    });
    
    // Animate summary values
    const summaryValues = document.querySelectorAll('.summary-value[data-count]');
    summaryValues.forEach((element, index) => {
        const target = parseInt(element.getAttribute('data-count'));
        setTimeout(() => {
            animateCounter(element, target);
        }, 500 + (index * 100));
    });
    
    // Animate funnel stage widths
    const funnelStages = document.querySelectorAll('.funnel-stage[data-width]');
    funnelStages.forEach((stage, index) => {
        setTimeout(() => {
            stage.style.animation = `slideIn 0.6s ease-out forwards`;
        }, index * 150);
    });
});

// Initialize AOS (Animate On Scroll) if available
if (typeof AOS !== 'undefined') {
    AOS.init({
        duration: 800,
        easing: 'ease-in-out',
        once: true,
        offset: 100
    });
}
