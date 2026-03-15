(function ($) {
    function escapeHtml(str) {
        return String(str || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function isOn($widget, key) {
        return String($widget.data(key)) === '1';
    }

    function parseNum(value, fallback) {
        var n = parseFloat(value);
        return Number.isFinite(n) && n > 0 ? n : fallback;
    }

    function getCardConfig($widget) {
        var raw = $widget.attr('data-card-config') || '';
        if (!raw) {
            return null;
        }

        try {
            var parsed = JSON.parse(raw);
            return parsed && typeof parsed === 'object' ? parsed : null;
        } catch (e) {
            return null;
        }
    }

    function renderCard(item, $widget) {
        var showImage = isOn($widget, 'show-image');
        var showTitle = isOn($widget, 'show-title');
        var showExcerpt = isOn($widget, 'show-excerpt');
        var showCategory = isOn($widget, 'show-category');
        var showButton = isOn($widget, 'show-button');
        var viewText = $widget.data('view-project-text') || 'View Project';

        var category = item.category && item.category.length ? item.category[0] : '';
        var image = '';
        var title = '';
        var excerpt = '';
        var categoryHtml = '';
        var buttonHtml = '';

        if (showImage && item.image) {
            image = '<a class="pp-card-image" href="' + escapeHtml(item.link) + '"><img src="' + escapeHtml(item.image) + '" alt=""></a>';
        }
        if (showCategory && category) {
            categoryHtml = '<div class="pp-card-category">' + escapeHtml(category) + '</div>';
        }
        if (showTitle) {
            title = '<h3 class="pp-card-title"><a href="' + escapeHtml(item.link) + '">' + escapeHtml(item.title) + '</a></h3>';
        }
        if (showExcerpt) {
            excerpt = '<p class="pp-card-excerpt">' + escapeHtml(item.excerpt || '') + '</p>';
        }
        if (showButton) {
            buttonHtml = '<a class="pp-card-btn" href="' + escapeHtml(item.link) + '">' + escapeHtml(viewText) + '</a>';
        }

        return '' +
            '<article class="pp-project-card">' +
            image +
            '<div class="pp-card-content">' +
            categoryHtml +
            title +
            excerpt +
            buttonHtml +
            '</div>' +
            '</article>';
    }

    function wrapSliderCard(cardHtml, isSlider) {
        if (!isSlider) {
            return cardHtml;
        }

        return '<div class="swiper-slide pp-project-slide">' + cardHtml + '</div>';
    }

    function initProjectSlider(trackEl) {
        if (!trackEl || typeof window.Swiper === 'undefined') {
            return;
        }

        if (trackEl.dataset.swiperReady === '1' && trackEl.ppSwiper) {
            return;
        }

        var shell = trackEl.closest('.pp-slider-shell');
        var nextEl = shell ? shell.querySelector('.pp-slider-next') : null;
        var prevEl = shell ? shell.querySelector('.pp-slider-prev') : null;
        var desktop = parseNum(trackEl.dataset.sliderDesktop, 3);
        var tablet = parseNum(trackEl.dataset.sliderTablet, 2);
        var mobile = parseNum(trackEl.dataset.sliderMobile, 1);

        trackEl.dataset.swiperReady = '1';
        trackEl.ppSwiper = new Swiper(trackEl, {
            slidesPerView: mobile,
            spaceBetween: 16,
            speed: 550,
            loop: true,
            loopAdditionalSlides: 2,
            watchOverflow: false,
            observer: true,
            observeParents: true,
            navigation: {
                nextEl: nextEl,
                prevEl: prevEl,
            },
            breakpoints: {
                768: { slidesPerView: tablet },
                1024: { slidesPerView: desktop },
            },
        });
    }

    function updateProjectSlider(trackEl, resetToStart) {
        if (!trackEl) {
            return;
        }

        initProjectSlider(trackEl);

        if (!trackEl.ppSwiper) {
            return;
        }

        if (trackEl.ppSwiper.params && trackEl.ppSwiper.params.loop && typeof trackEl.ppSwiper.loopDestroy === 'function' && typeof trackEl.ppSwiper.loopCreate === 'function') {
            trackEl.ppSwiper.loopDestroy();
            trackEl.ppSwiper.loopCreate();
        }

        trackEl.ppSwiper.update();
        if (resetToStart) {
            if (trackEl.ppSwiper.params && trackEl.ppSwiper.params.loop && typeof trackEl.ppSwiper.slideToLoop === 'function') {
                trackEl.ppSwiper.slideToLoop(0, 0);
            } else {
                trackEl.ppSwiper.slideTo(0, 0);
            }
        }
    }

    function fetchProjects($widget, page, append) {
        var $active = $widget.find('.pp-filter-btn.active');
        var category = String($active.attr('data-category-id') || $active.attr('data-category') || '');
        var cfg = getCardConfig($widget);
        var perPage = parseInt($widget.attr('data-per-page'), 10) || parseInt(PP_DATA.default_per_page, 10) || 9;
        var imageSize = String((cfg && cfg.image_size) || $widget.attr('data-image-size') || 'large');
        var showImage = cfg ? (parseInt(cfg.show_image, 10) === 1 ? 1 : 0) : (isOn($widget, 'show-image') ? 1 : 0);
        var showTitle = cfg ? (parseInt(cfg.show_title, 10) === 1 ? 1 : 0) : (isOn($widget, 'show-title') ? 1 : 0);
        var showExcerpt = cfg ? (parseInt(cfg.show_excerpt, 10) === 1 ? 1 : 0) : (isOn($widget, 'show-excerpt') ? 1 : 0);
        var showCategory = cfg ? (parseInt(cfg.show_category, 10) === 1 ? 1 : 0) : (isOn($widget, 'show-category') ? 1 : 0);
        var showButton = cfg ? (parseInt(cfg.show_button, 10) === 1 ? 1 : 0) : (isOn($widget, 'show-button') ? 1 : 0);
        var viewText = String((cfg && cfg.view_text) || $widget.attr('data-view-project-text') || '').trim();
        if (!viewText) {
            var currentButtonText = String($widget.find('.pp-card-btn').first().text() || '').trim();
            viewText = currentButtonText || 'View Project';
        }
        var endpoint = PP_DATA.rest_url + '/projects?page=' + page + '&per_page=' + perPage +
            '&image_size=' + encodeURIComponent(imageSize) +
            '&show_image=' + showImage +
            '&show_title=' + showTitle +
            '&show_excerpt=' + showExcerpt +
            '&show_category=' + showCategory +
            '&show_button=' + showButton +
            '&view_text=' + encodeURIComponent(viewText);

        if (category) {
            endpoint += '&category=' + encodeURIComponent(category);
        }

        $.ajax({
            url: endpoint,
            method: 'GET',
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-WP-Nonce', PP_DATA.nonce);
            },
        }).done(function (res) {
            if (!res || !Array.isArray(res.items)) {
                window.console && console.warn && console.warn('Projects endpoint response is invalid', res);
                return;
            }

            var layout = String($widget.data('layout') || 'grid');
            var isSlider = layout === 'slider';
            var html = '';
            (res.items || []).forEach(function (item) {
                var cardHtml = item.html ? String(item.html) : renderCard(item, $widget);
                html += wrapSliderCard(cardHtml, isSlider);
            });

            var $list = $widget.find('.pp-projects-list');
            var $target = isSlider ? $list.find('.swiper-wrapper') : $list;
            if (!$target.length) {
                $target = $list;
            }

            if (append) {
                $target.append(html);
            } else {
                $target.html(html);
            }

            if (isSlider && $list.length) {
                updateProjectSlider($list[0], !append);
            }

            $widget.data('pp-page', page);
            $widget.data('pp-max-pages', res.max_pages || 1);
        });
    }

    $(document).on('click', '.pp-projects-widget .pp-filter-btn', function (e) {
        e.preventDefault();
        var $btn = $(this);
        var $widget = $btn.closest('.pp-projects-widget');

        $widget.find('.pp-filter-btn').removeClass('active');
        $btn.addClass('active');
        fetchProjects($widget, 1, false);
    });

    $(document).on('click', '.pp-projects-widget .pp-load-more', function (e) {
        e.preventDefault();
        var $widget = $(this).closest('.pp-projects-widget');
        var page = parseInt($widget.data('pp-page') || 1, 10);
        var maxPages = parseInt($widget.data('pp-max-pages') || 1, 10);

        if (page < maxPages) {
            fetchProjects($widget, page + 1, true);
        }
    });

    function setupInfinite() {
        $('.pp-projects-widget[data-pagination="infinite"]').each(function () {
            var $widget = $(this);
            var $trigger = $widget.find('.pp-infinite-trigger');
            if (!$trigger.length || !('IntersectionObserver' in window)) {
                return;
            }

            var obs = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (!entry.isIntersecting) {
                        return;
                    }
                    var page = parseInt($widget.data('pp-page') || 1, 10);
                    var maxPages = parseInt($widget.data('pp-max-pages') || 1, 10);
                    if (page < maxPages) {
                        fetchProjects($widget, page + 1, true);
                    }
                });
            }, { threshold: 1 });

            obs.observe($trigger[0]);
        });
    }

    function setupProjectSliders() {
        $('.pp-slider-track').each(function () {
            updateProjectSlider(this, false);
        });
    }

    function setupGallerySwipers() {
        if (typeof window.Swiper === 'undefined') {
            return;
        }

        $('.pp-gallery-slider[data-pp-gallery-slider="1"]').each(function () {
            var sliderEl = this;
            if (sliderEl.dataset.swiperReady === '1') {
                return;
            }
            sliderEl.dataset.swiperReady = '1';

            var mainEl = sliderEl.querySelector('.pp-gallery-main');
            var thumbsEl = sliderEl.querySelector('.pp-gallery-thumbs');
            var nextEl = sliderEl.querySelector('.pp-gallery-next');
            var prevEl = sliderEl.querySelector('.pp-gallery-prev');

            if (!mainEl) {
                return;
            }

            var thumbsSwiper = null;
            if (thumbsEl) {
                thumbsSwiper = new Swiper(thumbsEl, {
                    spaceBetween: 10,
                    slidesPerView: 4,
                    freeMode: true,
                    slideToClickedSlide: true,
                    watchSlidesProgress: true,
                    breakpoints: {
                        0: { slidesPerView: 3 },
                        768: { slidesPerView: 4 },
                        1024: { slidesPerView: 5 },
                    },
                });
            }

            var mainSwiper = new Swiper(mainEl, {
                slidesPerView: 1,
                spaceBetween: 14,
                speed: 550,
                loop: true,
                loopAdditionalSlides: 2,
                observer: true,
                observeParents: true,
                navigation: {
                    nextEl: nextEl,
                    prevEl: prevEl,
                },
                thumbs: thumbsSwiper ? { swiper: thumbsSwiper } : undefined,
            });

            var imgs = mainEl.querySelectorAll('img');
            imgs.forEach(function (img) {
                if (img.complete) {
                    return;
                }
                img.addEventListener('load', function () {
                    mainSwiper.update();
                    if (thumbsSwiper) {
                        thumbsSwiper.update();
                    }
                }, { once: true });
            });

            sliderEl.ppMainSwiper = mainSwiper;
        });
    }

    $(function () {
        $('.pp-projects-widget').each(function () {
            var $widget = $(this);
            $widget.data('pp-page', 1);
            $widget.data('pp-max-pages', 999);
        });

        setupInfinite();
        setupProjectSliders();
        setupGallerySwipers();
    });
})(jQuery);
