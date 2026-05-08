// Hamburger menu toggle
document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.querySelector('.menu-toggle');
    const navMenu = document.querySelector('header nav ul');

    if (menuToggle) {
        menuToggle.addEventListener('click', function() {
            menuToggle.classList.toggle('active');
            navMenu.classList.toggle('open');
        });

        // Close menu when a link is clicked
        const navLinks = document.querySelectorAll('header nav ul li a');
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                menuToggle.classList.remove('active');
                navMenu.classList.remove('open');
            });
        });
    }
});

// Simple Slideshow functionality
let currentSlideIndex = 0;
let slideInterval;

function showSlide(index) {
    const slides = document.querySelectorAll('.slide');
    const dots = document.querySelectorAll('.dot');

    // Hide all slides
    slides.forEach(slide => slide.classList.remove('active'));
    dots.forEach(dot => dot.classList.remove('active'));

    // Show current slide
    slides[index].classList.add('active');
    dots[index].classList.add('active');

    currentSlideIndex = index;
}

function nextSlide() {
    const slides = document.querySelectorAll('.slide');
    const totalSlides = slides.length;
    currentSlideIndex = (currentSlideIndex + 1) % totalSlides;
    showSlide(currentSlideIndex);
}

function prevSlide() {
    const slides = document.querySelectorAll('.slide');
    const totalSlides = slides.length;
    currentSlideIndex = (currentSlideIndex - 1 + totalSlides) % totalSlides;
    showSlide(currentSlideIndex);
}

function goToSlide(index) {
    showSlide(index);
    // Reset the auto-slide timer
    clearInterval(slideInterval);
    slideInterval = setInterval(nextSlide, 4000); // 4 seconds per slide
}

// Start slideshow when page loads
document.addEventListener('DOMContentLoaded', function() {
    slideInterval = setInterval(nextSlide, 4000); // Change slide every 4 seconds
});

// Smooth scrolling for navigation links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth'
            });
        }
    });
});

// Form validation
function validateForm(formId) {
    const form = document.getElementById(formId);
    const inputs = form.querySelectorAll('input[required]');
    let isValid = true;

    inputs.forEach(input => {
        if (!input.value.trim()) {
            input.style.borderColor = 'red';
            isValid = false;
        } else {
            input.style.borderColor = '#4B8B3E';
        }
    });

    return isValid;
}

// Add event listeners when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Login form validation
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            if (!validateForm('loginForm')) {
                e.preventDefault();
                alert('Please fill in all required fields.');
            }
        });
    }

    // Register form validation
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            if (!validateForm('registerForm')) {
                e.preventDefault();
                alert('Please fill in all required fields.');
            }
        });
    }

    // Contact form validation
    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            if (!validateForm('contactForm')) {
                e.preventDefault();
                alert('Please fill in all required fields.');
            }
        });
    }

    // Mobile navigation toggle
    const menuToggle = document.querySelector('.menu-toggle');
    const nav = document.querySelector('nav');
    if (menuToggle && nav) {
        menuToggle.addEventListener('click', function() {
            nav.classList.toggle('open');
        });

        document.querySelectorAll('nav ul li a').forEach(link => {
            link.addEventListener('click', function() {
                if (nav.classList.contains('open')) {
                    nav.classList.remove('open');
                }
            });
        });
    }
});

// Simple image gallery (if needed)
function openGallery(imageSrc) {
    // This could be expanded for a full gallery
    window.open(imageSrc, '_blank');
}