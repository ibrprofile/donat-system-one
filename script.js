document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('donateForm');
    const successMessage = document.createElement('div');
    successMessage.className = 'success-message';
    successMessage.innerHTML = '<i class="fas fa-check-circle"></i><p>Донат успешно начислен на аккаунт!</p>';
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Здесь будет AJAX запрос к process.php
        fetch('process.php', {
            method: 'POST',
            body: new FormData(form)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                form.style.display = 'none';
                form.after(successMessage);
                successMessage.style.display = 'block';
            } else {
                alert('Произошла ошибка при обработке платежа. Пожалуйста, попробуйте еще раз.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Произошла ошибка при обработке платежа. Пожалуйста, попробуйте еще раз.');
        });
    });

    // Particle effect
    function createParticle(x, y) {
        const particle = document.createElement('div');
        particle.className = 'particle';
        particle.style.left = x + 'px';
        particle.style.top = y + 'px';
        particle.style.setProperty('--x', (Math.random() - 0.5) * 300 + 'px');
        particle.style.setProperty('--y', (Math.random() - 0.5) * 300 + 'px');
        document.getElementById('particles').appendChild(particle);
        setTimeout(() => particle.remove(), 1000);
    }

    document.body.addEventListener('mousemove', function(e) {
        createParticle(e.clientX, e.clientY);
    });

    // Liquid button effect
    const button = document.querySelector('.donate-button');
    button.addEventListener('mousemove', function(e) {
        const rect = button.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;
        button.style.setProperty('--x', x + 'px');
        button.style.setProperty('--y', y + 'px');
    });
});

