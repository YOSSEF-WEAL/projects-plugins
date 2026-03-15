(function () {
    function icon(name) {
        return (window.PP_DATA && window.PP_DATA.icons && window.PP_DATA.icons[name]) ? window.PP_DATA.icons[name] : '';
    }

    function ensureModal() {
        var modal = document.querySelector('.pp-lightbox-modal');
        if (modal) {
            return modal;
        }

        modal = document.createElement('div');
        modal.className = 'pp-lightbox-modal';
        modal.innerHTML = '' +
            '<div class="pp-lightbox-backdrop" data-pp-close="1"></div>' +
            '<div class="pp-lightbox-dialog" role="dialog" aria-modal="true" aria-label="Image lightbox">' +
                '<button type="button" class="pp-lightbox-close" data-pp-close="1" aria-label="Close"><img src="' + icon('close') + '" alt="" aria-hidden="true"></button>' +
                '<button type="button" class="pp-lightbox-nav pp-lightbox-prev" aria-label="Previous"><img src="' + icon('caret_right') + '" alt="" aria-hidden="true"></button>' +
                '<button type="button" class="pp-lightbox-nav pp-lightbox-next" aria-label="Next"><img src="' + icon('caret_left') + '" alt="" aria-hidden="true"></button>' +
                '<div class="swiper pp-lightbox-swiper"><div class="swiper-wrapper"></div></div>' +
            '</div>';

        document.body.appendChild(modal);
        return modal;
    }

    function getSlidesFromGallery(galleryEl) {
        var slideEls = galleryEl.querySelectorAll('.pp-gallery-main .pp-slide');
        var slides = [];
        slideEls.forEach(function (slide) {
            var full = slide.getAttribute('data-full');
            if (!full) {
                var img = slide.querySelector('img');
                full = img ? img.getAttribute('src') : '';
            }
            if (full) {
                slides.push(full);
            }
        });
        return slides;
    }

    function openLightbox(galleryEl) {
        if (typeof window.Swiper === 'undefined') {
            return;
        }

        var slides = getSlidesFromGallery(galleryEl);
        if (!slides.length) {
            return;
        }

        var modal = ensureModal();
        var wrapper = modal.querySelector('.pp-lightbox-swiper .swiper-wrapper');
        wrapper.innerHTML = slides.map(function (src) {
            return '<div class="swiper-slide"><img src="' + src + '" alt=""></div>';
        }).join('');

        var startIndex = 0;
        if (galleryEl.ppMainSwiper && typeof galleryEl.ppMainSwiper.activeIndex === 'number') {
            startIndex = galleryEl.ppMainSwiper.activeIndex;
        }

        if (modal.ppLightboxSwiper) {
            modal.ppLightboxSwiper.destroy(true, true);
        }

        modal.ppLightboxSwiper = new Swiper(modal.querySelector('.pp-lightbox-swiper'), {
            initialSlide: startIndex,
            slidesPerView: 1,
            spaceBetween: 8,
            speed: 350,
            loop: true,
            loopAdditionalSlides: 2,
            navigation: {
                nextEl: modal.querySelector('.pp-lightbox-next'),
                prevEl: modal.querySelector('.pp-lightbox-prev'),
            },
        });

        if (typeof modal.ppLightboxSwiper.slideToLoop === 'function') {
            modal.ppLightboxSwiper.slideToLoop(startIndex, 0);
        }

        modal.classList.add('is-open');
        document.body.classList.add('pp-lightbox-open');
    }

    function closeLightbox() {
        var modal = document.querySelector('.pp-lightbox-modal');
        if (!modal) {
            return;
        }
        modal.classList.remove('is-open');
        document.body.classList.remove('pp-lightbox-open');
    }

    function bindEvents() {
        document.addEventListener('click', function (e) {
            var zoomBtn = e.target.closest('.pp-gallery-zoom');
            if (zoomBtn) {
                e.preventDefault();
                var gallery = zoomBtn.closest('.pp-gallery-slider');
                if (gallery) {
                    openLightbox(gallery);
                }
                return;
            }

            var closeBtn = e.target.closest('[data-pp-close="1"]');
            if (closeBtn) {
                e.preventDefault();
                closeLightbox();
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeLightbox();
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        bindEvents();
    });
})();
