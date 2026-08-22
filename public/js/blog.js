/**
 * Personal Blog — infinite scroll, skeleton loading & reveal animations
 * (vanilla JS, no dependencies).
 */
(function () {
    'use strict';

    var SKELETON_COUNT = 3;

    function observeReveals(scope) {
        if (!('IntersectionObserver' in window)) return;

        var items = scope.querySelectorAll('.reveal:not(.is-visible)');
        if (!items.length) return;

        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -30px 0px' });

        items.forEach(function (el) {
            observer.observe(el);
        });
    }

    function skeletonCard() {
        var card = document.createElement('article');
        card.className = 'skeleton-card';
        card.setAttribute('aria-hidden', 'true');

        var block = document.createElement('div');
        block.className = 'skeleton-block';

        var body = document.createElement('div');
        body.className = 'skeleton-body';

        [35, 90, 75, 45].forEach(function (width) {
            var line = document.createElement('div');
            line.className = 'skeleton-line';
            line.style.width = width + '%';
            body.appendChild(line);
        });

        card.appendChild(block);
        card.appendChild(body);

        return card;
    }

    function showSkeletons(grid) {
        var existing = grid.querySelectorAll('.skeleton-card');
        if (existing.length) return;

        for (var i = 0; i < SKELETON_COUNT; i++) {
            grid.appendChild(skeletonCard());
        }
    }

    function removeSkeletons(grid) {
        grid.querySelectorAll('.skeleton-card').forEach(function (node) {
            node.remove();
        });
    }

    function showEndMessage(container) {
        var end = document.createElement('div');
        end.className = 'scroll-end';
        end.innerHTML = '<i class="fa-solid fa-check-circle" aria-hidden="true"></i> You\u2019ve reached the end';
        container.appendChild(end);
    }

    function initContainer(container) {
        var sentinel = container.querySelector('.scroll-sentinel');
        var grid = container.querySelector('.posts-grid');
        var pagination = container.querySelector('.js-pagination');

        if (!sentinel || !grid) return;

        // Classic pagination is the no-JS fallback; hide it once infinite scroll is active.
        if (pagination) pagination.style.display = 'none';

        var loading = false;

        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) return;

                var url = sentinel.dataset.nextPage || '';
                if (!url || loading) return;

                loading = true;
                showSkeletons(grid);

                fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(function (response) {
                        if (!response.ok) throw new Error('HTTP ' + response.status);
                        return response.text();
                    })
                    .then(function (html) {
                        var template = document.createElement('template');
                        template.innerHTML = html;

                        template.content.querySelectorAll('.news-card').forEach(function (card) {
                            grid.appendChild(card);
                        });

                        var nextData = template.content.querySelector('.scroll-sentinel-data');
                        var nextUrl = nextData ? (nextData.dataset.nextPage || '') : '';

                        removeSkeletons(grid);
                        observeReveals(grid);

                        if (nextUrl) {
                            sentinel.dataset.nextPage = nextUrl;
                        } else {
                            observer.disconnect();
                            sentinel.remove();
                            showEndMessage(container);
                        }
                    })
                    .catch(function () {
                        removeSkeletons(grid);
                    })
                    .finally(function () {
                        loading = false;
                    });
            });
        }, { rootMargin: '500px 0px' });

        observer.observe(sentinel);
    }

    function init() {
        // Mobile nav toggle (works everywhere, no dependencies)
        document.querySelectorAll('.nav-toggle').forEach(function (button) {
            button.addEventListener('click', function () {
                var nav = document.getElementById(button.getAttribute('aria-controls') || 'main-nav');
                if (!nav) return;

                var isOpen = nav.classList.toggle('open');
                button.classList.toggle('is-open', isOpen);
                button.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                button.querySelector('i').className = isOpen ? 'fa-solid fa-xmark' : 'fa-solid fa-bars';
            });
        });

        // Report network type (Wi-Fi vs cellular) for visitor analytics
        try {
            var conn = navigator.connection || navigator.mozConnection || navigator.webkitConnection;
            var connType = conn && (conn.type || conn.effectiveType);
            if (connType) {
                connType = String(connType).toLowerCase();
                if (connType === 'wifi' || connType === 'cellular') {
                    var ping = new Image();
                    ping.src = '/visit-network?type=' + encodeURIComponent(connType);
                }
            }
        } catch (e) {
            // Network Information API unsupported — visitor stays "unknown"
        }

        if (!('IntersectionObserver' in window)) {
            return; // keep classic pagination as fallback, no entrance animations
        }

        // Enable entrance animations only when JS is available
        document.documentElement.classList.add('js-anim');
        observeReveals(document);

        document.querySelectorAll('[data-infinite-scroll]').forEach(initContainer);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
