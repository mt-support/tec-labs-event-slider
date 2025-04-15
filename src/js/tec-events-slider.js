class TECEventsSlider {
    constructor(container, interval) {
        this.container = container;
        this.radioButtons = container.querySelectorAll("input[type='radio']");
        this.totalSlides = this.radioButtons.length;
        this.currentSlide = 0;
        this.interval = interval;
        this.rotationTimer = null;
        
        this.init();
    }

    init() {
        if (this.interval > 0) {
            // Find the initially checked radio button
            this.radioButtons.forEach((radio, index) => {
                if (radio.checked) {
                    this.currentSlide = index;
                }
            });
            
            this.startRotation();
            this.setupHoverHandlers();
        }
    }

    startRotation() {
        this.rotationTimer = setInterval(() => this.rotateSlide(), this.interval);
    }

    stopRotation() {
        if (this.rotationTimer) {
            clearInterval(this.rotationTimer);
            this.rotationTimer = null;
        }
    }

    rotateSlide() {
        this.currentSlide = (this.currentSlide + 1) % this.totalSlides;
        this.radioButtons[this.currentSlide].checked = true;
    }

    setupHoverHandlers() {
        this.container.addEventListener("mouseenter", () => this.stopRotation());
        this.container.addEventListener("mouseleave", () => this.startRotation());
    }
}

// Initialize sliders when DOM is ready
document.addEventListener("DOMContentLoaded", function() {
    const sliders = document.querySelectorAll(".tec-events-slider[data-autorotate]");
    sliders.forEach(slider => {
        const interval = parseInt(slider.dataset.autorotate, 10) * 1000;
        if (interval > 0) {
            new TECEventsSlider(slider, interval);
        }
    });
}); 