/**
 * Fan Hub Plus — Core Client-side Scripts
 * Theme Management, Bookmarking, 5-Star Ratings & Notes
 */

document.addEventListener('DOMContentLoaded', () => {
    initThemeManager();
    initKeyboardAccessibility();
    initBookmarkInteractions();
    initRatingInteractions();
    initNoteInteractions();
    initNetflixNavbar();
    initNetflixSliders();
    initNetflixHeroRotator();
    initNetflixDetailModal();
    initNetflixScrollReveal();
    initTelemetryCounters();
    initCardQuickActions();
    initHeroSoundToggle();
});

/**
 * Theme Manager: Dark & Light Mode (Responsive & Animated)
 */
function initThemeManager() {
    const htmlElement = document.documentElement;

    function updateThemeUi(theme, animate = false) {
        const themeButtons = document.querySelectorAll('.fh-theme-toggle');
        const isDark = theme === 'dark';

        themeButtons.forEach(btn => {
            const icon = btn.querySelector('i');
            if (icon) {
                if (isDark) {
                    icon.className = 'bi bi-sun-fill text-warning';
                } else {
                    icon.className = 'bi bi-moon-stars-fill text-primary';
                }

                if (animate) {
                    icon.classList.remove('fh-theme-spin');
                    void icon.offsetWidth; // Force reflow
                    icon.classList.add('fh-theme-spin');
                    setTimeout(() => icon.classList.remove('fh-theme-spin'), 500);
                }
            }

            // Sync any text labels inside pill or sidebar toggles
            const modeLabel = btn.querySelector('.fh-theme-mode');
            if (modeLabel) {
                modeLabel.textContent = isDark ? 'Dark' : 'Light';
                modeLabel.className = 'fh-theme-mode text-capitalize ' + (isDark ? 'text-warning' : 'text-primary');
            }

            const labelAttr = isDark ? 'Current: Dark theme (Switch to Light)' : 'Current: Light theme (Switch to Dark)';
            btn.setAttribute('aria-label', labelAttr);
            btn.setAttribute('title', labelAttr);
        });
    }

    const currentTheme = htmlElement.getAttribute('data-bs-theme') || localStorage.getItem('fanhub_theme') || 'dark';
    htmlElement.setAttribute('data-bs-theme', currentTheme);
    updateThemeUi(currentTheme, false);

    // Event delegation on document to catch all theme toggles dynamically
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.fh-theme-toggle');
        if (!btn) return;

        e.preventDefault();
        const activeTheme = htmlElement.getAttribute('data-bs-theme') || 'dark';
        const nextTheme = activeTheme === 'dark' ? 'light' : 'dark';

        htmlElement.setAttribute('data-bs-theme', nextTheme);
        localStorage.setItem('fanhub_theme', nextTheme);
        updateThemeUi(nextTheme, true);

        // Async sync with server if logged in
        const metaCsrf = document.querySelector('meta[name="csrf-token"]');
        const metaAuth = document.querySelector('meta[name="user-authenticated"]');

        if (metaAuth && metaAuth.content === 'true' && metaCsrf) {
            fetch('/profile/preferences', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': metaCsrf.content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    theme: nextTheme,
                    font_size: 'normal',
                    animations: 1,
                })
            }).catch(err => {
                console.debug('Theme preference async sync:', err);
            });
        }
    });
}

/**
 * Bookmark Toggle Interactions (AJAX)
 */
function initBookmarkInteractions() {
    document.addEventListener('click', async (e) => {
        const btn = e.target.closest('.fh-bookmark-toggle');
        if (!btn) return;

        e.preventDefault();
        const type = btn.getAttribute('data-type');
        const id = btn.getAttribute('data-id');
        const metaCsrf = document.querySelector('meta[name="csrf-token"]');

        if (!metaCsrf) {
            window.location.href = '/login';
            return;
        }

        btn.disabled = true;

        const toggleUrl = document.querySelector('meta[name="bookmark-toggle-url"]')?.content || '/bookmarks/toggle';

        try {
            const res = await fetch(toggleUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': metaCsrf.content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    bookmarkable_type: type,
                    bookmarkable_id: parseInt(id),
                })
            });

            if (res.status === 401) {
                window.location.href = '/login';
                return;
            }

            const data = await res.json();
            if (data.success) {
                const icon = btn.querySelector('i');
                const textSpan = btn.querySelector('#detailBookmarkText');
                const noteBtn = document.getElementById('bookmarkNoteToggleBtn');

                if (data.is_bookmarked) {
                    if (icon) icon.className = 'bi bi-bookmark-fill';
                    btn.classList.add('text-warning');
                    btn.classList.remove('text-light');
                    if (btn.classList.contains('btn-outline-light')) {
                        btn.classList.remove('btn-outline-light');
                        btn.classList.add('btn-warning', 'text-dark');
                    }
                    if (textSpan) textSpan.textContent = 'Bookmarked';
                    if (noteBtn) noteBtn.classList.remove('d-none');
                } else {
                    if (icon) icon.className = 'bi bi-bookmark';
                    btn.classList.remove('text-warning');
                    btn.classList.add('text-light');
                    if (btn.classList.contains('btn-warning')) {
                        btn.classList.remove('btn-warning', 'text-dark');
                        btn.classList.add('btn-outline-light');
                    }
                    if (textSpan) textSpan.textContent = 'Bookmark';
                    if (noteBtn) noteBtn.classList.add('d-none');
                    const noteCollapse = document.getElementById('bookmarkNoteBox');
                    if (noteCollapse && window.bootstrap?.Collapse) {
                        const inst = window.bootstrap.Collapse.getInstance(noteCollapse);
                        if (inst) inst.hide();
                    }
                }
            }
        } catch (err) {
            console.error('Bookmark error:', err);
        } finally {
            btn.disabled = false;
        }
    });
}

/**
 * 5-Star Ratings Engine Interactions
 */
function initRatingInteractions() {
    const starBtns = document.querySelectorAll('.fh-star-btn');
    if (!starBtns.length) return;

    starBtns.forEach(btn => {
        btn.addEventListener('click', async () => {
            const score = parseInt(btn.getAttribute('data-score'));
            const type = btn.getAttribute('data-type');
            const id = parseInt(btn.getAttribute('data-id'));
            const metaCsrf = document.querySelector('meta[name="csrf-token"]');

            if (!metaCsrf) {
                window.location.href = '/login';
                return;
            }

            const ratingUrl = document.querySelector('meta[name="rating-url"]')?.content || '/ratings';

            try {
                const res = await fetch(ratingUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': metaCsrf.content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        ratable_type: type,
                        ratable_id: id,
                        score: score,
                    })
                });

                if (res.status === 401) {
                    window.location.href = '/login';
                    return;
                }

                const data = await res.json();
                if (data.success) {
                    // Update star colors
                    starBtns.forEach(sb => {
                        const sVal = parseInt(sb.getAttribute('data-score'));
                        const icon = sb.querySelector('i');
                        if (sVal <= score) {
                            sb.className = 'btn btn-link p-0 text-decoration-none fh-star-btn text-warning';
                            if (icon) icon.className = 'bi bi-star-fill fs-4';
                        } else {
                            sb.className = 'btn btn-link p-0 text-decoration-none fh-star-btn text-secondary';
                            if (icon) icon.className = 'bi bi-star fs-4';
                        }
                    });

                    // Update feedback line
                    const feedback = document.getElementById('userRatingFeedback');
                    if (feedback) {
                        feedback.innerHTML = `<span class="text-info">You rated this ${score}/5 stars</span>
                            <button type="button" class="btn btn-link p-0 text-danger small ms-2" id="removeRatingBtn" data-type="${type}" data-id="${id}">Clear</button>`;
                        bindRemoveRatingBtn();
                    }

                    // Update aggregate display
                    const aggScore = document.getElementById('aggregateScoreText') || document.getElementById('mediaAggregateScore');
                    const aggCount = document.getElementById('aggregateCountText') || document.getElementById('mediaAggregateCount');
                    if (aggScore && data.average_score) {
                        aggScore.textContent = data.average_score.toFixed(1);
                        aggScore.classList.remove('small', 'text-muted');
                        aggScore.classList.add('display-6', 'fw-bold', 'text-light');
                    }
                    if (aggCount) {
                        aggCount.textContent = `(${data.ratings_count} ratings)`;
                    }
                }
            } catch (err) {
                console.error('Rating error:', err);
            }
        });
    });

    bindRemoveRatingBtn();
}

function bindRemoveRatingBtn() {
    const removeBtn = document.getElementById('removeRatingBtn');
    if (!removeBtn) return;

    removeBtn.addEventListener('click', async (e) => {
        e.preventDefault();
        const type = removeBtn.getAttribute('data-type');
        const id = parseInt(removeBtn.getAttribute('data-id'));
        const metaCsrf = document.querySelector('meta[name="csrf-token"]');
        if (!metaCsrf) return;

        const ratingUrl = document.querySelector('meta[name="rating-url"]')?.content || '/ratings';

        try {
            const res = await fetch(ratingUrl, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': metaCsrf.content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    ratable_type: type,
                    ratable_id: id,
                })
            });

            const data = await res.json();
            if (data.success) {
                const starBtns = document.querySelectorAll('.fh-star-btn');
                starBtns.forEach(sb => {
                    sb.className = 'btn btn-link p-0 text-decoration-none fh-star-btn text-secondary';
                    const icon = sb.querySelector('i');
                    if (icon) icon.className = 'bi bi-star fs-4';
                });

                const feedback = document.getElementById('userRatingFeedback');
                if (feedback) {
                    feedback.innerHTML = '<span class="text-muted">Click a star to record your vote</span>';
                }

                const aggScore = document.getElementById('aggregateScoreText') || document.getElementById('mediaAggregateScore');
                const aggCount = document.getElementById('aggregateCountText') || document.getElementById('mediaAggregateCount');
                if (aggScore) {
                    aggScore.textContent = data.average_score ? data.average_score.toFixed(1) : 'Not rated yet';
                }
                if (aggCount) {
                    aggCount.textContent = data.ratings_count ? `(${data.ratings_count} ratings)` : '(Be the first to rate)';
                }
            }
        } catch (err) {
            console.error('Remove rating error:', err);
        }
    });
}

/**
 * Bookmark Notes Updating
 */
function initNoteInteractions() {
    const detailForm = document.getElementById('detailNoteForm');
    if (detailForm) {
        detailForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const action = detailForm.getAttribute('action');
            if (!action || action === '#') return;

            const input = document.getElementById('detailNoteInput');
            const status = document.getElementById('noteSaveStatus');
            const saveBtn = document.getElementById('saveNoteBtn');
            const metaCsrf = document.querySelector('meta[name="csrf-token"]');

            if (saveBtn) saveBtn.disabled = true;
            if (status) status.textContent = 'Saving...';

            try {
                const res = await fetch(action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': metaCsrf ? metaCsrf.content : '',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ note: input ? input.value : '' })
                });

                const data = await res.json();
                if (data.success && status) {
                    status.textContent = 'Saved!';
                    setTimeout(() => { status.textContent = ''; }, 2500);
                }
            } catch (err) {
                if (status) status.textContent = 'Error saving note';
            } finally {
                if (saveBtn) saveBtn.disabled = false;
            }
        });
    }

    // Bookmarks page card forms
    document.querySelectorAll('.note-update-form').forEach(form => {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const bmId = form.getAttribute('data-id');
            const textarea = form.querySelector('textarea');
            const metaCsrf = document.querySelector('meta[name="csrf-token"]');

            try {
                const res = await fetch(form.getAttribute('action'), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': metaCsrf ? metaCsrf.content : '',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ note: textarea ? textarea.value : '' })
                });

                const data = await res.json();
                if (data.success) {
                    const preview = document.getElementById(`note-preview-${bmId}`);
                    if (preview) {
                        preview.textContent = data.note || 'No personal notes attached.';
                    }
                    const collapseEl = document.getElementById(`edit-note-${bmId}`);
                    if (collapseEl && window.bootstrap?.Collapse) {
                        const inst = window.bootstrap.Collapse.getInstance(collapseEl);
                        if (inst) inst.hide();
                    }
                }
            } catch (err) {
                console.error('Note update error:', err);
            }
        });
    });
}

/**
 * Keyboard accessibility enhancements (Escape to close dropdowns / modals)
 */
function initKeyboardAccessibility() {
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            const openDropdowns = document.querySelectorAll('.dropdown-menu.show');
            openDropdowns.forEach(menu => {
                const toggle = menu.closest('.dropdown')?.querySelector('[data-bs-toggle="dropdown"]');
                if (toggle && window.bootstrap?.Dropdown) {
                    const inst = window.bootstrap.Dropdown.getInstance(toggle);
                    if (inst) inst.hide();
                }
            });

            const searchBox = document.querySelector('.fh-expandable-search');
            if (searchBox && searchBox.classList.contains('is-open')) {
                searchBox.classList.remove('is-open');
            }
        }
    });
}

/**
 * Netflix Fixed Header Transition & Expandable Search
 */
function initNetflixNavbar() {
    const navbar = document.querySelector('.fh-navbar');
    if (navbar) {
        const updateNavbarScroll = () => {
            if (window.scrollY > 40) {
                navbar.classList.add('navbar-scrolled');
            } else {
                navbar.classList.remove('navbar-scrolled');
            }
        };
        window.addEventListener('scroll', updateNavbarScroll, { passive: true });
        updateNavbarScroll();
    }

    const searchBox = document.getElementById('navSearchBox') || document.querySelector('.fh-expandable-search');
    const searchToggleBtn = document.getElementById('navSearchToggle') || searchBox?.querySelector('.fh-search-toggle-btn');
    const searchInput = document.getElementById('navSearchInput') || searchBox?.querySelector('.fh-expandable-input');
    const searchForm = document.getElementById('navSearchForm') || searchBox?.querySelector('form');
    const searchClearBtn = document.getElementById('navSearchClear');

    const updateClearBtn = () => {
        if (searchClearBtn && searchInput) {
            searchClearBtn.style.display = searchInput.value.trim().length > 0 ? 'flex' : 'none';
        }
    };

    if (searchBox && searchToggleBtn && searchInput) {
        const openSearch = () => {
            searchBox.classList.add('is-open');
            if (navbar) navbar.classList.add('fh-search-active');
            updateClearBtn();
            searchInput.focus();
        };

        const closeSearch = () => {
            searchBox.classList.remove('is-open');
            if (navbar) navbar.classList.remove('fh-search-active');
            updateClearBtn();
        };

        // If there's an existing search term, keep the bar open and show clear
        if (searchInput.value && searchInput.value.trim().length > 0) {
            searchBox.classList.add('is-open');
            if (navbar) navbar.classList.add('fh-search-active');
            updateClearBtn();
        }

        searchInput.addEventListener('input', updateClearBtn);

        if (searchClearBtn) {
            searchClearBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                searchInput.value = '';
                updateClearBtn();
                searchInput.focus();
            });
        }

        searchToggleBtn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();

            if (!searchBox.classList.contains('is-open')) {
                openSearch();
            } else if (searchInput.value.trim().length > 0) {
                if (searchForm) {
                    searchForm.submit();
                } else {
                    const target = searchInput.dataset.action || '/explore';
                    window.location.href = `${target}?search=${encodeURIComponent(searchInput.value.trim())}`;
                }
            } else {
                closeSearch();
            }
        });

        searchInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                if (searchInput.value.trim().length > 0) {
                    if (searchForm) {
                        e.preventDefault();
                        searchForm.submit();
                    }
                } else {
                    e.preventDefault();
                }
            } else if (e.key === 'Escape') {
                closeSearch();
                searchInput.blur();
            }
        });

        document.addEventListener('click', (e) => {
            if (!searchBox.contains(e.target) && searchInput.value.trim().length === 0) {
                closeSearch();
            }
        });
    }

    // Mobile Search Overlay Toggle
    const mobileSearchBtn = document.getElementById('mobileSearchBtn');
    const mobileSearchOverlay = document.getElementById('mobileSearchOverlay');
    const closeMobileSearch = document.getElementById('closeMobileSearch');
    const mobileSearchInput = document.getElementById('mobileSearchOverlayInput');

    if (mobileSearchBtn && mobileSearchOverlay) {
        mobileSearchBtn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            const isVisible = mobileSearchOverlay.style.display === 'block';
            mobileSearchOverlay.style.display = isVisible ? 'none' : 'block';
            if (!isVisible && mobileSearchInput) {
                setTimeout(() => mobileSearchInput.focus(), 50);
            }
        });

        if (closeMobileSearch) {
            closeMobileSearch.addEventListener('click', (e) => {
                e.preventDefault();
                mobileSearchOverlay.style.display = 'none';
            });
        }

        document.addEventListener('click', (e) => {
            if (mobileSearchOverlay.style.display === 'block' &&
                !mobileSearchOverlay.contains(e.target) &&
                !mobileSearchBtn.contains(e.target)) {
                mobileSearchOverlay.style.display = 'none';
            }
        });
    }
}

/**
 * Netflix Horizontal Row Sliders
 */
function initNetflixSliders() {
    document.querySelectorAll('.netflix-slider-container').forEach(container => {
        const track = container.querySelector('.netflix-slider-track');
        const arrowLeft = container.querySelector('.netflix-slider-arrow.arrow-left');
        const arrowRight = container.querySelector('.netflix-slider-arrow.arrow-right');

        if (!track) return;

        if (arrowLeft) {
            arrowLeft.addEventListener('click', () => {
                const scrollAmount = container.clientWidth * 0.75;
                track.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
            });
        }

        if (arrowRight) {
            arrowRight.addEventListener('click', () => {
                const scrollAmount = container.clientWidth * 0.75;
                track.scrollBy({ left: scrollAmount, behavior: 'smooth' });
            });
        }

        // Mouse wheel horizontal scroll support
        track.addEventListener('wheel', (e) => {
            if (e.deltaY !== 0) {
                e.preventDefault();
                track.scrollLeft += e.deltaY;
            }
        }, { passive: false });
    });
}

/**
 * Netflix Hero Banner Auto-Rotator
 */
function initNetflixHeroRotator() {
    const slides = document.querySelectorAll('.netflix-hero-bg');
    const titleEl = document.getElementById('heroActiveTitle');
    const synopsisEl = document.getElementById('heroActiveSynopsis');
    const matchEl = document.getElementById('heroActiveMatch');
    const ratingEl = document.getElementById('heroActiveRating');
    const categoryEl = document.getElementById('heroActiveCategory');
    const playBtn = document.getElementById('heroActivePlayBtn');
    const infoBtn = document.getElementById('heroActiveInfoBtn');
    const dots = document.querySelectorAll('.netflix-hero-dot');

    if (slides.length <= 1) return;

    let currentIndex = 0;
    let rotatorInterval = null;

    function activateSlide(index) {
        slides.forEach((s, idx) => {
            if (idx === index) {
                s.classList.add('active');
                s.classList.remove('inactive');
            } else {
                s.classList.remove('active');
                s.classList.add('inactive');
            }
        });

        dots.forEach((d, idx) => {
            d.classList.toggle('active', idx === index);
        });

        const activeSlide = slides[index];
        if (activeSlide) {
            if (titleEl && activeSlide.dataset.title) titleEl.textContent = activeSlide.dataset.title;
            if (synopsisEl && activeSlide.dataset.synopsis) synopsisEl.textContent = activeSlide.dataset.synopsis;
            if (matchEl && activeSlide.dataset.match) matchEl.textContent = activeSlide.dataset.match;
            if (ratingEl && activeSlide.dataset.rating) ratingEl.textContent = activeSlide.dataset.rating;
            if (categoryEl && activeSlide.dataset.category) categoryEl.textContent = activeSlide.dataset.category;
            if (playBtn && activeSlide.dataset.url) playBtn.setAttribute('href', activeSlide.dataset.url);
            if (infoBtn && activeSlide.dataset.slug) {
                infoBtn.setAttribute('data-slug', activeSlide.dataset.slug);
                infoBtn.setAttribute('data-title', activeSlide.dataset.title || '');
                infoBtn.setAttribute('data-synopsis', activeSlide.dataset.synopsis || '');
                infoBtn.setAttribute('data-image', activeSlide.dataset.image || '');
                infoBtn.setAttribute('data-category', activeSlide.dataset.category || '');
                infoBtn.setAttribute('data-url', activeSlide.dataset.url || '');
                infoBtn.setAttribute('data-id', activeSlide.dataset.id || '');
            }
        }

        currentIndex = index;
    }

    function startRotator() {
        rotatorInterval = setInterval(() => {
            const nextIndex = (currentIndex + 1) % slides.length;
            activateSlide(nextIndex);
        }, 6000);
    }

    dots.forEach((dot, idx) => {
        dot.addEventListener('click', () => {
            clearInterval(rotatorInterval);
            activateSlide(idx);
            startRotator();
        });
    });

    startRotator();
}

/**
 * Netflix Quick Detail Modal Dialog
 */
function initNetflixDetailModal() {
    const modalEl = document.getElementById('netflixDetailModal');
    if (!modalEl) return;

    const heroEl = document.getElementById('modalHeroBg');
    const titleEl = document.getElementById('modalTitle');
    const categoryEl = document.getElementById('modalCategory');
    const synopsisEl = document.getElementById('modalSynopsis');
    const matchEl = document.getElementById('modalMatch');
    const playBtn = document.getElementById('modalPlayBtn');
    const bookmarkBtn = document.getElementById('modalBookmarkBtn');

    document.addEventListener('click', (e) => {
        const trigger = e.target.closest('[data-netflix-modal="true"]');
        if (!trigger) return;

        e.preventDefault();

        const title = trigger.getAttribute('data-title') || 'Lore Entry';
        const synopsis = trigger.getAttribute('data-synopsis') || '';
        const image = trigger.getAttribute('data-image') || '';
        const category = trigger.getAttribute('data-category') || 'Fandom';
        const match = trigger.getAttribute('data-match') || '98% Match';
        const url = trigger.getAttribute('data-url') || '#';
        const id = trigger.getAttribute('data-id') || '';

        if (titleEl) titleEl.textContent = title;
        if (categoryEl) categoryEl.textContent = category;
        if (synopsisEl) synopsisEl.textContent = synopsis;
        if (matchEl) matchEl.textContent = match;
        if (playBtn) playBtn.setAttribute('href', url);

        if (heroEl && image) {
            heroEl.style.backgroundImage = `url('${image}')`;
        }

        if (bookmarkBtn && id) {
            bookmarkBtn.setAttribute('data-id', id);
            bookmarkBtn.setAttribute('data-type', 'content');
        }

        if (window.bootstrap?.Modal) {
            let bsModal = window.bootstrap.Modal.getInstance(modalEl);
            if (!bsModal) {
                bsModal = new window.bootstrap.Modal(modalEl);
            }
            bsModal.show();
        }
    });
}

/**
 * Intersection Observer Scroll Reveal
 */
function initNetflixScrollReveal() {
    if (!('IntersectionObserver' in window)) {
        document.querySelectorAll('.netflix-reveal').forEach(el => el.classList.add('is-revealed'));
        return;
    }

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-revealed');
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -40px 0px'
    });

    document.querySelectorAll('.netflix-reveal').forEach(el => observer.observe(el));
}

/**
 * Animated Number Counters for Platform Telemetry
 */
function initTelemetryCounters() {
    const counterElements = document.querySelectorAll('.fh-telemetry-counter');
    if (counterElements.length === 0) return;

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const el = entry.target;
                const target = parseInt(el.getAttribute('data-target') || el.textContent, 10);
                if (isNaN(target)) return;

                let count = 0;
                const speed = 25; // ms
                const increment = Math.max(1, Math.ceil(target / 40));

                const timer = setInterval(() => {
                    count += increment;
                    if (count >= target) {
                        el.textContent = target.toLocaleString();
                        clearInterval(timer);
                    } else {
                        el.textContent = count.toLocaleString();
                    }
                }, speed);

                observer.unobserve(el);
            }
        });
    }, { threshold: 0.3 });

    counterElements.forEach(el => observer.observe(el));
}

/**
 * Card Quick Action Micro-Interactions (Like, Thumbs Up, Bookmark feedback)
 */
function initCardQuickActions() {
    document.addEventListener('click', (e) => {
        const likeBtn = e.target.closest('.netflix-like-btn');
        if (likeBtn) {
            e.preventDefault();
            e.stopPropagation();
            const icon = likeBtn.querySelector('i');
            if (likeBtn.classList.contains('active')) {
                likeBtn.classList.remove('active');
                if (icon) icon.className = 'bi bi-hand-thumbs-up';
                showFanhubToast('Rating removed', 'bi-hand-thumbs-down');
            } else {
                likeBtn.classList.add('active');
                if (icon) icon.className = 'bi bi-hand-thumbs-up-fill text-danger';
                showFanhubToast('Liked & added to your recommendations!', 'bi-hand-thumbs-up-fill');
            }
        }
    });
}

/**
 * Hero Banner Audio / Sound Preview Micro-Toggle
 */
function initHeroSoundToggle() {
    const soundBadge = document.getElementById('heroSoundBadge');
    if (!soundBadge) return;

    let isMuted = false;
    soundBadge.addEventListener('click', () => {
        isMuted = !isMuted;
        const bars = soundBadge.querySelectorAll('.netflix-sound-wave-bar');
        const text = soundBadge.querySelector('.sound-text');
        if (isMuted) {
            bars.forEach(b => { b.style.animationPlayState = 'paused'; b.style.height = '3px'; });
            if (text) text.textContent = 'Audio Muted';
            showFanhubToast('Hero audio preview muted', 'bi-volume-mute-fill');
        } else {
            bars.forEach(b => { b.style.animationPlayState = 'running'; });
            if (text) text.textContent = 'Dolby Atmos 5.1';
            showFanhubToast('Hero audio preview enabled', 'bi-volume-up-fill');
        }
    });
}

/**
 * Toast Notification System
 */
function showFanhubToast(message, iconClass = 'bi-check-circle-fill') {
    let container = document.getElementById('fanhubToastContainer');
    if (!container) {
        container = document.createElement('div');
        container.id = 'fanhubToastContainer';
        container.className = 'fanhub-toast-container';
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = 'fanhub-toast';
    toast.innerHTML = `<i class="bi ${iconClass} fs-5 text-danger"></i> <span>${message}</span>`;
    container.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(15px) scale(0.9)';
        toast.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 3200);
}
