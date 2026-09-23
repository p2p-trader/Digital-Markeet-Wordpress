document.addEventListener('DOMContentLoaded',function(){'use strict';
const body=document.body,toggle=document.getElementById('theme-toggle'),saved=localStorage.getItem('dmp-theme');
if(saved==='dark')body.classList.add('dark-mode');
function updateThemeIcon(){if(toggle)toggle.textContent=body.classList.contains('dark-mode')?'☀':'☾';}
updateThemeIcon();
if(toggle)toggle.addEventListener('click',function(){body.classList.toggle('dark-mode');localStorage.setItem('dmp-theme',body.classList.contains('dark-mode')?'dark':'light');updateThemeIcon();});
const menu=document.getElementById('wp-mobile-menu-toggle'),panel=document.getElementById('wp-mobile-nav-panel');
if(menu&&panel)menu.addEventListener('click',function(){const open=panel.classList.toggle('is-open');menu.setAttribute('aria-expanded',open?'true':'false');});

const search=document.getElementById('nav-search-input');
document.addEventListener('keydown',function(e){
    if((e.ctrlKey||e.metaKey)&&e.key.toLowerCase()==='k'){
        e.preventDefault();
        if(search){search.focus();search.select();}
    }
});

window.addEventListener('scroll',function(){const header=document.getElementById('marketplace-header');if(header)header.classList.toggle('is-scrolled',window.scrollY>8);},{passive:true});

const observer=new IntersectionObserver(function(entries){
    entries.forEach(function(entry){
        if(entry.isIntersecting){
            entry.target.classList.add('is-visible');
            observer.unobserve(entry.target);
        }
    });
},{threshold:.08});
function observeElements(root){
    (root||document).querySelectorAll('.category-tile,.market-card,.process-card,.faq-item,.audience-point').forEach(function(el){
        el.classList.add('reveal-on-scroll');
        observer.observe(el);
    });
}
observeElements();

const main=document.getElementById('main-gallery-image');
document.querySelectorAll('.product-thumb-item').forEach(function(btn){
    btn.addEventListener('click',function(){
        const src=btn.dataset.fullImage;
        if(main&&src){
            main.src=src;
            document.querySelectorAll('.product-thumb-item').forEach(b=>b.classList.remove('active'));
            btn.classList.add('active');
        }
    });
});

document.querySelectorAll('.qty-stepper').forEach(function(step){
    const minus=step.querySelector('.qty-btn-minus'),plus=step.querySelector('.qty-btn-plus'),val=step.querySelector('.qty-val')||step.querySelector('.qty-input');
    if(!minus||!plus||!val)return;
    minus.addEventListener('click',function(){
        let n=parseInt(val.value||val.textContent,10)||1;
        n=Math.max(1,n-1);
        if(val.tagName==='INPUT')val.value=n;else val.textContent=n;
        val.dispatchEvent(new Event('change',{bubbles:true}));
    });
    plus.addEventListener('click',function(){
        let n=parseInt(val.value||val.textContent,10)||1;
        n++;
        if(val.tagName==='INPUT')val.value=n;else val.textContent=n;
        val.dispatchEvent(new Event('change',{bubbles:true}));
    });
});

function bindWishlistButtons(root){
    (root||document).querySelectorAll('.wishlist').forEach(function(btn){
        if(btn.dataset.wishlistBound)return;
        btn.dataset.wishlistBound='1';
        btn.addEventListener('click',function(e){
            e.preventDefault();
            e.stopPropagation();
            btn.classList.toggle('saved');
            btn.textContent=btn.classList.contains('saved')?'♥':'♡';
        });
    });
}
bindWishlistButtons();

document.querySelectorAll('.newsletter form,.footer-news form').forEach(function(form){
    form.addEventListener('submit',function(e){
        const input=form.querySelector('input[type=email]');
        if(input&&!input.value){e.preventDefault();input.focus();}
    });
});

/**
 * =========================================================================
 * 1. LIVE AJAX INSTANT SEARCH POP-OVER (Ctrl+K / Nav & Hero search)
 * =========================================================================
 */
const ajaxEndpoint = (typeof digitalMarketplaceData !== 'undefined' && digitalMarketplaceData.ajaxUrl) ? digitalMarketplaceData.ajaxUrl : '/wp-admin/admin-ajax.php';
const searchNonce = (typeof digitalMarketplaceData !== 'undefined' && digitalMarketplaceData.nonce) ? digitalMarketplaceData.nonce : '';

function escapeRegExp(string) {
    return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
}

function highlightMatch(text, query) {
    if (!query) return text;
    const regex = new RegExp('(' + escapeRegExp(query) + ')', 'gi');
    return text.replace(regex, '<mark class="search-match">$1</mark>');
}

function initInstantSearch(formSelector, inputSelector, popoverSelector) {
    const form = document.querySelector(formSelector);
    if (!form) return;
    const input = form.querySelector(inputSelector);
    const popover = form.querySelector(popoverSelector);
    if (!input || !popover) return;

    let debounceTimer = null;
    let currentQuery = '';
    let selectedIndex = -1;
    let abortCtrl = null;

    function closePopover() {
        popover.classList.remove('is-open');
        popover.setAttribute('aria-hidden', 'true');
        popover.innerHTML = '';
        selectedIndex = -1;
    }

    function openPopover() {
        popover.classList.add('is-open');
        popover.setAttribute('aria-hidden', 'false');
    }

    function updateSelection(items) {
        items.forEach((item, idx) => {
            if (idx === selectedIndex) {
                item.classList.add('is-selected');
                item.scrollIntoView({ block: 'nearest' });
            } else {
                item.classList.remove('is-selected');
            }
        });
    }

    function executeSearch(query) {
        query = query.trim();
        if (query.length < 2) {
            closePopover();
            return;
        }

        currentQuery = query;
        openPopover();
        popover.innerHTML = `
            <div class="search-popover-loading">
                <div class="search-mini-spinner"></div>
                <span>Searching catalog for &ldquo;${query}&rdquo;&hellip;</span>
            </div>
        `;

        if (abortCtrl) abortCtrl.abort();
        abortCtrl = new AbortController();

        const formData = new FormData();
        formData.append('action', 'digital_marketplace_search');
        formData.append('term', query);
        formData.append('nonce', searchNonce);
        formData.append('limit', '6');

        fetch(ajaxEndpoint, {
            method: 'POST',
            body: formData,
            signal: abortCtrl.signal
        })
        .then(res => res.json())
        .then(data => {
            if (!data.success || !data.data) {
                popover.innerHTML = `
                    <div class="search-popover-empty">
                        <p>No results found for &ldquo;${query}&rdquo;.</p>
                        <small>Try keywords like subscription, hosting, license, or template.</small>
                    </div>
                `;
                return;
            }

            const payload = data.data;
            const count = payload.count || 0;
            const products = payload.products || [];

            if (products.length === 0) {
                popover.innerHTML = `
                    <div class="search-popover-empty">
                        <div class="search-empty-icon">⌕</div>
                        <p>No products matching &ldquo;<strong>${query}</strong>&rdquo;</p>
                        <small>Try checking your spelling or searching for a general term.</small>
                    </div>
                `;
                return;
            }

            let html = `
                <div class="search-popover-header">
                    <span>Products (${count})</span>
                    <small>Press <kbd>↑</kbd> <kbd>↓</kbd> to navigate, <kbd>Enter</kbd> to view</small>
                </div>
                <div class="search-popover-results" role="listbox">
            `;

            products.forEach((prod, index) => {
                const highlightedTitle = highlightMatch(prod.title, query);
                const thumbHtml = prod.thumb 
                    ? `<img src="${prod.thumb}" alt="${prod.title}" class="search-result-thumb">`
                    : `<div class="search-result-avatar"><span>${prod.initials || 'P'}</span></div>`;

                const origPriceHtml = prod.orig_price 
                    ? `<del class="search-orig-price">${prod.orig_price}</del>` 
                    : '';

                html += `
                    <a href="${prod.url}" class="search-result-item" role="option" data-index="${index}" id="search-opt-${index}">
                        ${thumbHtml}
                        <div class="search-result-info">
                            <div class="search-result-top">
                                <span class="search-result-cat">${prod.category}</span>
                                <span class="search-result-format">${prod.format}</span>
                            </div>
                            <h4 class="search-result-title">${highlightedTitle}</h4>
                            <div class="search-result-meta">
                                <div class="search-result-price">
                                    <strong>${prod.price}</strong>
                                    ${origPriceHtml}
                                </div>
                                <span class="search-result-rating">★ ${prod.rating}</span>
                            </div>
                        </div>
                    </a>
                `;
            });

            html += `</div>`;

            if (count > 0 && payload.view_all_url) {
                html += `
                    <a href="${payload.view_all_url}" class="search-popover-footer" role="option" data-index="${products.length}">
                        <span>View all ${count} catalog results</span>
                        <strong>&rarr;</strong>
                    </a>
                `;
            }

            popover.innerHTML = html;
            selectedIndex = -1;
        })
        .catch(err => {
            if (err.name === 'AbortError') return;
            popover.innerHTML = `
                <div class="search-popover-empty">
                    <p>Error loading search results. Please try again.</p>
                </div>
            `;
        });
    }

    input.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        const q = input.value;
        if (q.trim().length < 2) {
            closePopover();
            return;
        }
        debounceTimer = setTimeout(() => {
            executeSearch(q);
        }, 220);
    });

    input.addEventListener('focus', function() {
        if (input.value.trim().length >= 2 && !popover.classList.contains('is-open')) {
            executeSearch(input.value);
        }
    });

    input.addEventListener('keydown', function(e) {
        if (!popover.classList.contains('is-open')) return;
        const items = popover.querySelectorAll('.search-result-item, .search-popover-footer');
        if (!items.length) return;

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            selectedIndex = (selectedIndex + 1) % items.length;
            updateSelection(items);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            selectedIndex = (selectedIndex - 1 + items.length) % items.length;
            updateSelection(items);
        } else if (e.key === 'Enter') {
            if (selectedIndex >= 0 && items[selectedIndex]) {
                e.preventDefault();
                items[selectedIndex].click();
            }
        } else if (e.key === 'Escape') {
            e.preventDefault();
            closePopover();
            input.blur();
        }
    });

    document.addEventListener('click', function(e) {
        if (!form.contains(e.target)) {
            closePopover();
        }
    });
}

// Initialize on header navigation search and hero search
initInstantSearch('#header-search-form', '#nav-search-input', '#header-search-popover');
initInstantSearch('.hero-search', 'input[name="s"]', '.hero-search-popover');

/**
 * =========================================================================
 * 2. ARCHIVE CATALOG INSTANT FILTERING, SEARCHING & SORTING
 * =========================================================================
 */
const catalogWrapper = document.getElementById('catalog-content-wrapper');
if (catalogWrapper) {
    const chipsContainer = document.getElementById('catalog-category-chips');
    const sortSelect = document.getElementById('catalog-sort-select');
    const searchInput = document.getElementById('archive-search-input');
    const searchForm = document.getElementById('archive-catalog-search-form');
    const resultsTarget = document.getElementById('catalog-results-target');
    const countBadge = document.getElementById('catalog-count-badge');
    const headingTitle = document.getElementById('archive-heading-title');
    const clearBtn = document.getElementById('catalog-clear-filters-btn');
    const loadingOverlay = document.getElementById('catalog-loading-overlay');

    let activeCategory = 'all';
    let activeSort = sortSelect ? sortSelect.value : 'newest';
    let activeSearch = searchInput ? searchInput.value.trim() : '';
    let activePaged = 1;
    let catalogAbortCtrl = null;
    let catalogDebounce = null;

    // Detect initial state from URL parameters
    const currentParams = new URLSearchParams(window.location.search);
    if (currentParams.has('s')) {
        activeSearch = currentParams.get('s');
        if (searchInput) searchInput.value = activeSearch;
    }
    if (currentParams.has('sort') && sortSelect) {
        activeSort = currentParams.get('sort');
        sortSelect.value = activeSort;
    }
    if (currentParams.has('paged')) {
        activePaged = parseInt(currentParams.get('paged'), 10) || 1;
    }

    // Active category from active chip
    const activeChip = chipsContainer ? chipsContainer.querySelector('.category-chip.is-active') : null;
    if (activeChip && activeChip.dataset.categorySlug) {
        activeCategory = activeChip.dataset.categorySlug;
    }

    function checkActiveFilters() {
        const isFiltered = (activeCategory !== 'all' || activeSearch.length > 0 || activeSort !== 'newest');
        if (clearBtn) {
            clearBtn.style.display = isFiltered ? 'inline-flex' : 'none';
        }
    }
    checkActiveFilters();

    function updateCatalog(pushHistory) {
        if (loadingOverlay) loadingOverlay.classList.add('is-active');

        // Update URL state
        const params = new URLSearchParams();
        if (activeCategory && activeCategory !== 'all') params.set('product_cat', activeCategory);
        if (activeSearch) params.set('s', activeSearch);
        if (activeSort && activeSort !== 'newest') params.set('sort', activeSort);
        if (activePaged > 1) params.set('paged', activePaged.toString());

        const newQuery = params.toString();
        const baseCatalogUrl = (typeof digitalMarketplaceData !== 'undefined' && digitalMarketplaceData.catalogUrl) 
            ? digitalMarketplaceData.catalogUrl 
            : window.location.pathname;
        const newUrl = newQuery ? `${baseCatalogUrl}?${newQuery}` : baseCatalogUrl;

        if (pushHistory && window.history.pushState) {
            window.history.pushState({
                category: activeCategory,
                sort: activeSort,
                search: activeSearch,
                paged: activePaged
            }, '', newUrl);
        }

        checkActiveFilters();

        if (catalogAbortCtrl) catalogAbortCtrl.abort();
        catalogAbortCtrl = new AbortController();

        const formData = new FormData();
        formData.append('action', 'digital_marketplace_filter_catalog');
        formData.append('category', activeCategory);
        formData.append('sort', activeSort);
        formData.append('s', activeSearch);
        formData.append('paged', activePaged.toString());
        formData.append('nonce', searchNonce);

        fetch(ajaxEndpoint, {
            method: 'POST',
            body: formData,
            signal: catalogAbortCtrl.signal
        })
        .then(res => res.json())
        .then(data => {
            if (loadingOverlay) loadingOverlay.classList.remove('is-active');

            if (data.success && data.data) {
                const res = data.data;
                if (resultsTarget) {
                    resultsTarget.innerHTML = res.html;
                }
                if (countBadge) {
                    countBadge.textContent = `Showing ${res.total} ${res.total === 1 ? 'product' : 'products'}`;
                }
                if (headingTitle && res.title) {
                    headingTitle.textContent = res.title;
                }

                // Re-bind interactive cards
                bindWishlistButtons(resultsTarget);
                observeElements(resultsTarget);

                // Bind reset button in empty state if present
                const resetBtn = document.getElementById('catalog-reset-filters-btn');
                if (resetBtn) {
                    resetBtn.addEventListener('click', resetAllFilters);
                }
            }
        })
        .catch(err => {
            if (err.name === 'AbortError') return;
            if (loadingOverlay) loadingOverlay.classList.remove('is-active');
        });
    }

    function resetAllFilters() {
        activeCategory = 'all';
        activeSearch = '';
        activeSort = 'newest';
        activePaged = 1;

        if (chipsContainer) {
            chipsContainer.querySelectorAll('.category-chip').forEach(chip => {
                chip.classList.toggle('is-active', chip.dataset.categorySlug === 'all');
            });
        }
        if (searchInput) searchInput.value = '';
        if (sortSelect) sortSelect.value = 'newest';

        updateCatalog(true);
    }

    // Category chips click handler
    if (chipsContainer) {
        chipsContainer.addEventListener('click', function(e) {
            const chip = e.target.closest('.category-chip');
            if (!chip) return;
            e.preventDefault();

            chipsContainer.querySelectorAll('.category-chip').forEach(c => c.classList.remove('is-active'));
            chip.classList.add('is-active');

            activeCategory = chip.dataset.categorySlug || 'all';
            activePaged = 1;
            updateCatalog(true);
        });
    }

    // Sort select change handler
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            activeSort = sortSelect.value;
            activePaged = 1;
            updateCatalog(true);
        });
    }

    // Search input debounced typing handler
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(catalogDebounce);
            catalogDebounce = setTimeout(() => {
                activeSearch = searchInput.value.trim();
                activePaged = 1;
                updateCatalog(true);
            }, 300);
        });
    }

    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            activeSearch = searchInput ? searchInput.value.trim() : '';
            activePaged = 1;
            updateCatalog(true);
        });
    }

    if (clearBtn) {
        clearBtn.addEventListener('click', resetAllFilters);
    }

    // Intercept catalog pagination clicks for AJAX pagination
    if (resultsTarget) {
        resultsTarget.addEventListener('click', function(e) {
            const pageLink = e.target.closest('.archive-pagination-wrap a');
            if (!pageLink) return;
            e.preventDefault();

            const href = pageLink.getAttribute('href');
            if (href) {
                try {
                    const url = new URL(href, window.location.origin);
                    const p = url.searchParams.get('paged');
                    if (p) {
                        activePaged = parseInt(p, 10) || 1;
                    } else {
                        // Check if paged is in pretty permalink like /page/2/
                        const match = href.match(/\/page\/(\d+)/i);
                        activePaged = match ? parseInt(match[1], 10) : 1;
                    }
                } catch {
                    activePaged = 1;
                }
            }

            // Smooth scroll to catalog controls
            const controls = document.querySelector('.archive-controls-bar');
            if (controls) {
                controls.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }

            updateCatalog(true);
        });
    }

    // Browser back/forward navigation
    window.addEventListener('popstate', function(e) {
        if (e.state) {
            activeCategory = e.state.category || 'all';
            activeSort = e.state.sort || 'newest';
            activeSearch = e.state.search || '';
            activePaged = e.state.paged || 1;
        } else {
            const p = new URLSearchParams(window.location.search);
            activeCategory = p.get('product_cat') || 'all';
            activeSort = p.get('sort') || 'newest';
            activeSearch = p.get('s') || '';
            activePaged = parseInt(p.get('paged'), 10) || 1;
        }

        if (chipsContainer) {
            chipsContainer.querySelectorAll('.category-chip').forEach(c => {
                c.classList.toggle('is-active', (c.dataset.categorySlug || 'all') === activeCategory);
            });
        }
        if (searchInput) searchInput.value = activeSearch;
        if (sortSelect) sortSelect.value = activeSort;

        updateCatalog(false);
    });
}

/**
 * =========================================================================
 * 3. PRODUCT TRACKER FOR RECENTLY VIEWED & PERSONALIZATION AFFINITY
 * =========================================================================
 */
const analyticsTracker = document.getElementById('product-page-analytics');
if (analyticsTracker) {
    try {
        const prodData = {
            id: analyticsTracker.dataset.id,
            title: analyticsTracker.dataset.title,
            url: analyticsTracker.dataset.url,
            price: analyticsTracker.dataset.price,
            orig: analyticsTracker.dataset.origPrice || '',
            category: analyticsTracker.dataset.category || 'Digital Product',
            rating: analyticsTracker.dataset.rating || '4.9',
            format: analyticsTracker.dataset.format || 'Instant access',
            thumb: analyticsTracker.dataset.thumb || '',
            timestamp: Date.now()
        };

        if (prodData.id && prodData.title) {
            let recent = [];
            try {
                recent = JSON.parse(localStorage.getItem('dmp_recently_viewed') || '[]');
            } catch {
                recent = [];
            }

            // Remove existing entry for same product ID
            recent = recent.filter(item => String(item.id) !== String(prodData.id));
            // Add current product to beginning of list
            recent.unshift(prodData);
            // Cap at 8 items
            if (recent.length > 8) {
                recent = recent.slice(0, 8);
            }

            localStorage.setItem('dmp_recently_viewed', JSON.stringify(recent));

            // Record category affinity for recommendations
            let affinity = {};
            try {
                affinity = JSON.parse(localStorage.getItem('dmp_category_affinity') || '{}');
            } catch {
                affinity = {};
            }
            const catKey = prodData.category.toLowerCase().trim();
            affinity[catKey] = (affinity[catKey] || 0) + 1;
            localStorage.setItem('dmp_category_affinity', JSON.stringify(affinity));
        }
    } catch (e) {
        console.warn('Unable to record recently viewed product:', e);
    }
}

/**
 * =========================================================================
 * 4. HOMEPAGE RECENTLY VIEWED SHELF RENDERER
 * =========================================================================
 */
const recentShelf = document.getElementById('recently-viewed-shelf');
const recentEmpty = document.getElementById('recently-viewed-empty');
const clearHistoryBtn = document.getElementById('clear-recent-history');
const heroViewCounter = document.getElementById('hero-view-counter');

function renderRecentlyViewedShelf() {
    if (!recentShelf) return;

    let items = [];
    try {
        items = JSON.parse(localStorage.getItem('dmp_recently_viewed') || '[]');
    } catch {
        items = [];
    }

    if (heroViewCounter) {
        heroViewCounter.textContent = items.length > 0 ? items.length + ' Saved' : '0 Views';
    }

    if (!items || items.length === 0) {
        recentShelf.innerHTML = '';
        if (recentEmpty) recentEmpty.style.display = 'block';
        if (clearHistoryBtn) clearHistoryBtn.style.display = 'none';
        return;
    }

    if (recentEmpty) recentEmpty.style.display = 'none';
    if (clearHistoryBtn) clearHistoryBtn.style.display = 'inline-flex';

    // Build cards from stored item metadata
    recentShelf.innerHTML = items.slice(0, 4).map(function(item) {
        const title = item.title || 'Digital Product';
        const initial = title.charAt(0).toUpperCase();
        const price = parseFloat(item.price || 0).toFixed(2);
        const orig = item.orig ? parseFloat(item.orig).toFixed(2) : null;
        const cat = item.category || 'Digital Product';
        const rating = item.rating || '4.9';
        const format = item.format || 'Instant access';
        const url = item.url || '#';
        const thumb = item.thumb;

        const imgMarkup = thumb
            ? '<img src="' + thumb + '" alt="' + title + '" class="market-card-thumb-img">'
            : '<div class="image-placeholder"><span>' + initial + '</span></div>';

        const delMarkup = orig ? '<del>$' + orig + '</del>' : '';

        return '<article class="market-card recently-viewed-card" data-product-id="' + (item.id || '') + '">' +
            '<a class="market-card-image" href="' + url + '">' +
                imgMarkup +
                '<span class="card-category">' + cat + '</span>' +
                '<span class="card-promo-badge viewed-badge">Viewed</span>' +
                '<button class="wishlist" type="button" aria-label="Add to wishlist">♡</button>' +
            '</a>' +
            '<div class="market-card-body">' +
                '<div class="card-meta">' +
                    '<span>' + format + '</span>' +
                    '<span>★ ' + rating + '</span>' +
                '</div>' +
                '<h3><a href="' + url + '">' + title + '</a></h3>' +
                '<p>Viewed in your recent session. Fast digital access.</p>' +
                '<div class="card-buy">' +
                    '<div><strong>$' + price + '</strong>' + delMarkup + '</div>' +
                    '<a href="' + url + '" class="btn-card-view">View →</a>' +
                '</div>' +
            '</div>' +
        '</article>';
    }).join('');

    bindWishlistButtons(recentShelf);
    observeElements(recentShelf);
}

renderRecentlyViewedShelf();

if (clearHistoryBtn) {
    clearHistoryBtn.addEventListener('click', function() {
        localStorage.removeItem('dmp_recently_viewed');
        renderRecentlyViewedShelf();
    });
}

/**
 * =========================================================================
 * 5. LIMITED-TIME DEALS LIVE COUNTDOWN TIMER
 * =========================================================================
 */
const dealsHrs = document.getElementById('deals-hrs');
const dealsMins = document.getElementById('deals-mins');
const dealsSecs = document.getElementById('deals-secs');

if (dealsHrs && dealsMins && dealsSecs) {
    // Keep a continuous countdown targeting midnight or 8 hours from initial load
    let targetTime = parseInt(localStorage.getItem('dmp_flash_deadline'), 10);
    const now = Date.now();

    if (!targetTime || targetTime < now) {
        // Set new deadline 8 hours 42 mins from now
        targetTime = now + (8 * 3600 + 42 * 60 + 19) * 1000;
        localStorage.setItem('dmp_flash_deadline', targetTime);
    }

    function updateDealsCountdown() {
        const remaining = Math.max(0, targetTime - Date.now());
        const totalSeconds = Math.floor(remaining / 1000);

        const h = Math.floor(totalSeconds / 3600);
        const m = Math.floor((totalSeconds % 3600) / 60);
        const s = totalSeconds % 60;

        dealsHrs.textContent = String(h).padStart(2, '0');
        dealsMins.textContent = String(m).padStart(2, '0');
        dealsSecs.textContent = String(s).padStart(2, '0');

        if (remaining <= 0) {
            // Reset to another 12 hours for continuous promo
            targetTime = Date.now() + 12 * 3600 * 1000;
            localStorage.setItem('dmp_flash_deadline', targetTime);
        }
    }

    updateDealsCountdown();
    setInterval(updateDealsCountdown, 1000);
}

/**
 * =========================================================================
 * 6. RECOMMENDED FOR YOU PERSONALIZATION FILTER CHIPS
 * =========================================================================
 */
const recChipsContainer = document.getElementById('recommended-filter-chips');
const recGrid = document.getElementById('recommended-products-grid');

if (recChipsContainer && recGrid) {
    const chips = recChipsContainer.querySelectorAll('.rec-chip');

    chips.forEach(function(chip) {
        chip.addEventListener('click', function() {
            chips.forEach(c => c.classList.remove('active'));
            chip.classList.add('active');

            const filter = (chip.dataset.recFilter || 'all').toLowerCase();
            const cards = recGrid.querySelectorAll('.market-card');

            cards.forEach(function(card) {
                if (filter === 'all') {
                    card.style.display = '';
                    card.style.opacity = '1';
                    return;
                }

                const catBadge = card.querySelector('.card-category');
                const catText = catBadge ? catBadge.textContent.toLowerCase() : '';
                const cardCatData = card.dataset.category || '';

                if (catText.includes(filter) || cardCatData.includes(filter)) {
                    card.style.display = '';
                    card.style.animation = 'popoverIn .3s ease forwards';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });

    // Auto-select user's affinity category if they visited one before
    try {
        const affinity = JSON.parse(localStorage.getItem('dmp_category_affinity') || '{}');
        let topCat = '';
        let maxViews = 0;
        for (const cat in affinity) {
            if (affinity[cat] > maxViews) {
                maxViews = affinity[cat];
                topCat = cat;
            }
        }
        if (topCat && maxViews >= 2) {
            const matchChip = Array.from(chips).find(c => topCat.includes(c.dataset.recFilter || 'xyz'));
            if (matchChip) {
                // Highlight with a subtle pulse
                matchChip.classList.add('affinity-hint');
            }
        }
    } catch {
        // Silently continue
    }
}

/**
 * =========================================================================
 * 7. MARKETPLACE JUMP NAV SCROLLSPY
 * =========================================================================
 */
const jumpNav = document.querySelector('.marketplace-jump-nav');
if (jumpNav) {
    const jumpLinks = jumpNav.querySelectorAll('.jump-chip');
    const sections = Array.from(jumpLinks).map(link => {
        const id = link.getAttribute('href');
        return id && id.startsWith('#') ? document.querySelector(id) : null;
    }).filter(Boolean);

    function onScrollSpy() {
        const scrollPos = window.scrollY + 120;
        let currentSection = null;

        for (let i = 0; i < sections.length; i++) {
            const sec = sections[i];
            const top = sec.offsetTop;
            const bottom = top + sec.offsetHeight;
            if (scrollPos >= top && scrollPos < bottom) {
                currentSection = sec;
                break;
            }
        }

        if (currentSection) {
            const targetHref = '#' + currentSection.id;
            jumpLinks.forEach(link => {
                link.classList.toggle('active', link.getAttribute('href') === targetHref);
            });
        }
    }

    window.addEventListener('scroll', onScrollSpy, { passive: true });
}

});

