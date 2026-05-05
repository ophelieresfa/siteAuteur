document.addEventListener("DOMContentLoaded", function () {
    const mobileNav = document.querySelector("#ast-mobile-site-navigation");
    const mobileToggle = document.querySelector(".ast-mobile-menu-trigger-minimal");
    const mobileMenuList = document.querySelector("#ast-hf-mobile-menu .main-header-menu");

    if (!mobileNav || !mobileToggle) return;

    function updateMobileMenuState() {
        const navOpen =
            mobileNav.classList.contains("toggled") ||
            mobileNav.getAttribute("aria-expanded") === "true" ||
            mobileToggle.getAttribute("aria-expanded") === "true" ||
            (mobileMenuList && mobileMenuList.getAttribute("aria-expanded") === "true");

        document.body.classList.toggle("mobile-menu-open", navOpen);
    }

    updateMobileMenuState();

    const observer = new MutationObserver(updateMobileMenuState);

    observer.observe(mobileNav, {
        attributes: true,
        attributeFilter: ["class", "aria-expanded"]
    });

    observer.observe(mobileToggle, {
        attributes: true,
        attributeFilter: ["class", "aria-expanded"]
    });

    if (mobileMenuList) {
        observer.observe(mobileMenuList, {
            attributes: true,
            attributeFilter: ["class", "aria-expanded"]
        });
    }

    mobileToggle.addEventListener("click", function () {
        setTimeout(updateMobileMenuState, 50);
    });

    window.addEventListener("resize", updateMobileMenuState);
});

document.addEventListener("DOMContentLoaded", function () {
    const zoomFigures = document.querySelectorAll("#example-site figure.zoomable");

    if (!zoomFigures.length) return;
    if (document.querySelector(".siteauteur-lightbox")) return;

    const lightbox = document.createElement("div");
    lightbox.className = "siteauteur-lightbox";
    lightbox.setAttribute("aria-hidden", "true");

    lightbox.innerHTML = `
        <div class="siteauteur-lightbox__inner" role="dialog" aria-modal="true" aria-label="Aperçu de l'image">
            <button type="button" class="siteauteur-lightbox__close" aria-label="Fermer">×</button>
            <img class="siteauteur-lightbox__img" src="" alt="">
            <div class="siteauteur-lightbox__hint">Échap pour fermer</div>
        </div>
    `;

    document.body.appendChild(lightbox);

    const lightboxImg = lightbox.querySelector(".siteauteur-lightbox__img");
    const closeBtn = lightbox.querySelector(".siteauteur-lightbox__close");

    let lastFocusedElement = null;

    function openLightbox(img) {
        const fullSrc = img.currentSrc || img.src;
        const altText = img.getAttribute("alt") || "";

        lastFocusedElement = document.activeElement;

        lightboxImg.src = fullSrc;
        lightboxImg.alt = altText;

        lightbox.classList.add("is-active");
        lightbox.setAttribute("aria-hidden", "false");
        document.body.classList.add("siteauteur-lightbox-open");

        closeBtn.focus();
    }

    function closeLightbox() {
        lightbox.classList.remove("is-active");
        lightbox.setAttribute("aria-hidden", "true");
        document.body.classList.remove("siteauteur-lightbox-open");

        setTimeout(() => {
            lightboxImg.src = "";
            lightboxImg.alt = "";
        }, 220);

        if (lastFocusedElement) {
            lastFocusedElement.focus();
        }
    }

    zoomFigures.forEach((figure) => {
        const img = figure.querySelector("img");
        if (!img) return;

        figure.setAttribute("tabindex", "0");
        figure.setAttribute("role", "button");
        figure.setAttribute("aria-label", "Ouvrir l'image en grand");

        figure.addEventListener("click", function () {
            openLightbox(img);
        });

        figure.addEventListener("keydown", function (e) {
            if (e.key === "Enter" || e.key === " ") {
                e.preventDefault();
                openLightbox(img);
            }
        });
    });

    closeBtn.addEventListener("click", closeLightbox);

    lightbox.addEventListener("click", function (e) {
        if (e.target === lightbox) {
            closeLightbox();
        }
    });

    document.addEventListener("keydown", function (e) {
        if (e.key === "Escape" && lightbox.classList.contains("is-active")) {
            closeLightbox();
        }
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const header = document.querySelector("#header-nav");
    const synopsis = document.querySelector("#synopsis");

    if (!header || !synopsis) return;

    function getOffset() {
        const width = window.innerWidth;

        if (width < 768) {
            return 140;
        } else if (width < 1024) {
            return 160;
        } else if (width >= 1025 && width <= 1199) {
            return 160;
        } else {
            return 107;
        }
    }

    function toggleHeader() {
        const rect = synopsis.getBoundingClientRect();
        const offset = getOffset();

        if (rect.top <= offset) {
            header.classList.add("visible");
        } else {
            header.classList.remove("visible");
        }
    }

    toggleHeader();

    window.addEventListener("scroll", toggleHeader);
    window.addEventListener("resize", toggleHeader);
});

document.addEventListener("DOMContentLoaded", function () {
    const header = document.querySelector("#header-nav");
    const menuLinks = document.querySelectorAll('#header-nav a[href^="#"]');
    const btnHero = document.querySelectorAll('#hero a[href^="#"]');
    const btnFooter = document.querySelectorAll('#footer a[href^="#"]');

    if (!header) return;

    function bindSmoothScroll(links) {
        links.forEach(link => {
            link.addEventListener("click", function (e) {
                const targetId = this.getAttribute("href");
                const target = document.querySelector(targetId);

                if (!target) return;

                e.preventDefault();

                const headerHeight = header.offsetHeight;
                const offset = 0;

                const position =
                    target.getBoundingClientRect().top +
                    window.pageYOffset -
                    headerHeight -
                    offset;

                window.scrollTo({
                    top: position,
                    behavior: "smooth"
                });
            });
        });
    }

    bindSmoothScroll(menuLinks);
    bindSmoothScroll(btnHero);
    bindSmoothScroll(btnFooter);
});

document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("contact-form");
    if (!form) return;

    const contactItem = document.getElementById("contact");
    if (!contactItem) return;

    const popup = document.createElement("div");
    popup.className = "contact-popup-dynamic";
    popup.setAttribute("aria-hidden", "true");

    popup.innerHTML = `
        <div class="contact-popup-overlay"></div>
        <div class="contact-popup-box" role="dialog" aria-modal="true" aria-label="Formulaire de contact">
            <button type="button" class="contact-popup-close" aria-label="Fermer">&times;</button>
            <h2 class="contact-popup-title">Contact</h2>
            <div class="title-divider">
                <span class="line1"></span>
                <span class="dot"></span>
                <span class="line2"></span>
            </div>
            <div class="contact-popup-form-wrap"></div>
        </div>
    `;

    document.body.appendChild(popup);

    const formWrap = popup.querySelector(".contact-popup-form-wrap");
    const overlay = popup.querySelector(".contact-popup-overlay");
    const closeBtn = popup.querySelector(".contact-popup-close");

    formWrap.appendChild(form);
    form.classList.remove("hidden");
    form.style.display = "block";

    function openPopup(e) {
        if (e) e.preventDefault();
        popup.classList.add("is-open");
        popup.setAttribute("aria-hidden", "false");
        document.body.classList.add("contact-popup-open");
    }

    function closePopup() {
        popup.classList.remove("is-open");
        popup.setAttribute("aria-hidden", "true");
        document.body.classList.remove("contact-popup-open");
    }

    contactItem.style.cursor = "pointer";
    contactItem.addEventListener("click", openPopup);
    overlay.addEventListener("click", closePopup);
    closeBtn.addEventListener("click", closePopup);

    document.addEventListener("keydown", function (e) {
        if (e.key === "Escape" && popup.classList.contains("is-open")) {
            closePopup();
        }
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector("#fluentform_5");
    if (!form) return;

    const popupBox = form.closest(".contact-popup-box");
    if (!popupBox) return;

    function checkFormState() {
        if (form.classList.contains("ff_force_hide")) {
            popupBox.classList.add("form-success");
        } else {
            popupBox.classList.remove("form-success");
        }
    }

    checkFormState();

    const observer = new MutationObserver(checkFormState);
    observer.observe(form, {
        attributes: true,
        attributeFilter: ["class"]
    });
});

document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".sa-toggle-password").forEach(btn => {
        btn.addEventListener("click", function () {
            const input = document.getElementById(this.dataset.target);
            if (!input) return;

            if (input.type === "password") {
                input.type = "text";
                this.textContent = "Cacher";
            } else {
                input.type = "password";
                this.textContent = "Afficher";
            }
        });
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector("#fluentform_6");
    if (!form) return;

    const STORAGE_KEY = "siteauteur_onboarding_step_6";
    const FORM_DATA_KEY = "siteauteur_onboarding_data_6";

    const ajaxUrl = window.SA_ONBOARDING?.ajax_url || "";
    const ajaxNonce = window.SA_ONBOARDING?.nonce || "";
    const urlParams = new URLSearchParams(window.location.search);
    const sessionId =
        window.SA_ONBOARDING?.session_id ||
        urlParams.get("session_id") ||
        "";

    const baseSteps = [
        { key: "general", title: "Informations générales", shortTitle: "1", section: ".general-information-section", content: ".general-information-content" },
        { key: "hero", title: "Hero", shortTitle: "2", section: ".hero-section", content: ".hero-content" },
        { key: "about-book", title: "Roman", shortTitle: "3", section: ".about-book-section", content: ".about-book-content" },
        { key: "why-read", title: "Arguments", shortTitle: "4", section: ".why-read-section", content: ".why-read-content" },
        { key: "abstract", title: "Extrait", shortTitle: "5", section: ".abstract-section", content: ".abstract-content" },
        { key: "reviews", title: "Avis", shortTitle: "6", section: ".reviews-section", content: ".reviews-content" },
        { key: "author", title: "Auteur", shortTitle: "7", section: ".about-author-section", content: ".about-author-content" },
        { key: "emails", title: "Emails", shortTitle: "8", section: ".catch-email-section", content: ".catch-email-content" },
        { key: "other-books", title: "Autres livres", shortTitle: "9", section: ".other-book-section", content: ".other-book-content" },
        { key: "recap", title: "Récapitulatif", shortTitle: "10", section: ".recap-section", content: ".recap-content" },
        { key: "technical", title: "Technique", shortTitle: "11", section: ".technical-information-section", content: ".technical-information-content" }
    ];

    let steps = [];
    let currentStep = 0;
    let maxVisitedStep = 0;
    let serverDraft = { fields: {}, uploads: {} };
    let saveDraftTimeout = null;
    let activeUploads = 0;
    let isInitializingForm = true;

    const submitButton = form.querySelector('button[type="submit"]');
    const submitWrapper = submitButton ? submitButton.closest(".ff_submit_btn_wrapper") : null;

    const addressLine1Field = form.querySelector('#ff_6_address_1_address_line_1_');
    const cityField = form.querySelector('#ff_6_address_1_city_');
    const zipField = form.querySelector('#ff_6_address_1_zip_');
    const countryField = form.querySelector('#ff_6_address_1_country_');

    let addressAutocompleteBox = null;
    let addressAutocompleteItems = [];
    let addressAutocompleteActiveIndex = -1;
    let addressAutocompleteAbortController = null;

    function debounce(fn, delay = 400) {
        let timer = null;
        return function (...args) {
            clearTimeout(timer);
            timer = setTimeout(() => fn.apply(this, args), delay);
        };
    }

    function ensureAddressAutocompleteBox() {
        if (!addressLine1Field) return null;
        if (addressAutocompleteBox) return addressAutocompleteBox;

        const wrapper =
            addressLine1Field.closest(".ff-el-input--content") ||
            addressLine1Field.parentElement;

        if (!wrapper) return null;

        wrapper.style.position = "relative";

        addressAutocompleteBox = document.createElement("div");
        addressAutocompleteBox.className = "sa-address-autocomplete";
        addressAutocompleteBox.style.position = "absolute";
        addressAutocompleteBox.style.left = "0";
        addressAutocompleteBox.style.right = "0";
        addressAutocompleteBox.style.top = "100%";
        addressAutocompleteBox.style.marginTop = "6px";
        addressAutocompleteBox.style.background = "#fff";
        addressAutocompleteBox.style.border = "1px solid #dcdfe6";
        addressAutocompleteBox.style.borderRadius = "10px";
        addressAutocompleteBox.style.boxShadow = "0 10px 30px rgba(0,0,0,.08)";
        addressAutocompleteBox.style.zIndex = "9999";
        addressAutocompleteBox.style.maxHeight = "260px";
        addressAutocompleteBox.style.overflowY = "auto";
        addressAutocompleteBox.style.display = "none";
        addressAutocompleteBox.style.color = "#1f2a44";

        wrapper.appendChild(addressAutocompleteBox);

        return addressAutocompleteBox;
    }

    function hideAddressSuggestions() {
        if (!addressAutocompleteBox) return;
        addressAutocompleteBox.innerHTML = "";
        addressAutocompleteBox.style.display = "none";
        addressAutocompleteItems = [];
        addressAutocompleteActiveIndex = -1;
    }

    function normalizeCountryCode(code) {
        return (code || "").toUpperCase();
    }

    function formatStreetFromAddress(address) {
        const houseNumber = address.house_number || "";
        const road =
            address.road ||
            address.pedestrian ||
            address.footway ||
            address.cycleway ||
            address.path ||
            "";
        return `${houseNumber} ${road}`.trim();
    }

    function getCityFromAddress(address) {
        return (
            address.city ||
            address.town ||
            address.village ||
            address.municipality ||
            address.hamlet ||
            ""
        );
    }

    function dispatchFieldUpdates(...fields) {
        fields.forEach(field => {
            if (!field) return;
            field.dispatchEvent(new Event("input", { bubbles: true }));
            field.dispatchEvent(new Event("change", { bubbles: true }));
        });
    }

    function applyAddressSuggestion(item) {
        const address = item.address || {};

        const street = formatStreetFromAddress(address);
        const city = getCityFromAddress(address);
        const postcode = address.postcode || "";
        const countryCode = normalizeCountryCode(address.country_code);

        if (addressLine1Field) {
            addressLine1Field.value = street || item.display_name || "";
        }

        if (cityField) {
            cityField.value = city;
        }

        if (zipField) {
            zipField.value = postcode;
        }

        if (countryField && countryCode) {
            const option = Array.from(countryField.options).find(
                opt => (opt.value || "").toUpperCase() === countryCode
            );
            if (option) {
                countryField.value = option.value;
            }
        }

        dispatchFieldUpdates(addressLine1Field, cityField, zipField, countryField);
        hideAddressSuggestions();
    }

    function renderAddressSuggestions(results) {
        const box = ensureAddressAutocompleteBox();
        if (!box) return;

        box.innerHTML = "";
        addressAutocompleteItems = results || [];
        addressAutocompleteActiveIndex = -1;

        if (!addressAutocompleteItems.length) {
            hideAddressSuggestions();
            return;
        }

        addressAutocompleteItems.forEach((item, index) => {
            const row = document.createElement("button");
            row.type = "button";
            row.className = "sa-address-autocomplete__item";
            row.style.display = "block";
            row.style.width = "100%";
            row.style.textAlign = "left";
            row.style.border = "0";
            row.style.background = "#e8f7f2";
            row.style.margin = "0 0 5px 0";
            row.style.border = "1px solid #d9efe7";
            row.style.transition = "all .15s ease";
            row.style.padding = "12px 14px";
            row.style.cursor = "pointer";
            row.style.fontSize = "14px";
            row.style.lineHeight = "1.4";
            row.style.color = "#1f2a44";
            row.style.webkitTextFillColor = "#1f2a44";

            row.textContent = item.display_name || "Adresse";

            row.addEventListener("mouseenter", function () {
                addressAutocompleteActiveIndex = index;
                updateAddressSuggestionHighlight();
            });

            row.addEventListener("mousedown", function (e) {
                e.preventDefault();
                applyAddressSuggestion(item);
            });

            box.appendChild(row);
        });

        box.style.display = "block";
    }

    function updateAddressSuggestionHighlight() {
        if (!addressAutocompleteBox) return;

        const rows = addressAutocompleteBox.querySelectorAll(".sa-address-autocomplete__item");

        rows.forEach((row, index) => {
            if (index === addressAutocompleteActiveIndex) {
                row.style.background = "#d2efe6";
                row.style.border = "1px solid #9fded0";
                row.style.transform = "translateY(-1px)";
            } else {
                row.style.background = "#e8f7f2";
                row.style.border = "1px solid #d9efe7";
                row.style.transform = "translateY(0)";
            }
        });
    }

    async function searchAddressSuggestions(query) {
        if (addressAutocompleteAbortController) {
            addressAutocompleteAbortController.abort();
        }

        addressAutocompleteAbortController = new AbortController();

        const url = new URL("https://nominatim.openstreetmap.org/search");
        url.searchParams.set("format", "jsonv2");
        url.searchParams.set("addressdetails", "1");
        url.searchParams.set("limit", "5");
        url.searchParams.set("countrycodes", "fr,be,ch,es");
        url.searchParams.set("q", query);

        const response = await fetch(url.toString(), {
            method: "GET",
            headers: {
                "Accept": "application/json"
            },
            signal: addressAutocompleteAbortController.signal
        });

        if (!response.ok) {
            throw new Error("Erreur recherche adresse");
        }

        return response.json();
    }

    const debouncedAddressSearch = debounce(async function () {
        if (!addressLine1Field) return;

        const query = (addressLine1Field.value || "").trim();

        if (query.length < 3) {
            hideAddressSuggestions();
            return;
        }

        try {
            const results = await searchAddressSuggestions(query);
            renderAddressSuggestions(results);
        } catch (error) {
            if (error.name === "AbortError") return;
            console.error("Erreur autocomplétion adresse :", error);
            hideAddressSuggestions();
        }
    }, 400);

    function bindAddressAutocomplete() {
        if (!addressLine1Field) return;

        ensureAddressAutocompleteBox();

        addressLine1Field.addEventListener("input", function () {
            debouncedAddressSearch();
        });

        if (zipField) {
            zipField.addEventListener("input", function () {
                this.value = this.value.replace(/\s+/g, "");
            });
        }

        addressLine1Field.addEventListener("focus", function () {
            const query = (addressLine1Field.value || "").trim();
            if (query.length >= 3) {
                debouncedAddressSearch();
            }
        });

        addressLine1Field.addEventListener("keydown", function (e) {
            if (!addressAutocompleteItems.length || !addressAutocompleteBox || addressAutocompleteBox.style.display === "none") {
                return;
            }

            if (e.key === "ArrowDown") {
                e.preventDefault();
                addressAutocompleteActiveIndex++;
                if (addressAutocompleteActiveIndex >= addressAutocompleteItems.length) {
                    addressAutocompleteActiveIndex = 0;
                }
                updateAddressSuggestionHighlight();
            }

            if (e.key === "ArrowUp") {
                e.preventDefault();
                addressAutocompleteActiveIndex--;
                if (addressAutocompleteActiveIndex < 0) {
                    addressAutocompleteActiveIndex = addressAutocompleteItems.length - 1;
                }
                updateAddressSuggestionHighlight();
            }

            if (e.key === "Enter") {
                if (addressAutocompleteActiveIndex >= 0 && addressAutocompleteItems[addressAutocompleteActiveIndex]) {
                    e.preventDefault();
                    applyAddressSuggestion(addressAutocompleteItems[addressAutocompleteActiveIndex]);
                }
            }

            if (e.key === "Escape") {
                hideAddressSuggestions();
            }
        });

        document.addEventListener("click", function (e) {
            if (!addressAutocompleteBox) return;
            if (e.target === addressLine1Field) return;
            if (addressAutocompleteBox.contains(e.target)) return;
            hideAddressSuggestions();
        });
    }

    function getUploadHiddenName(fieldName) {
        const map = {
            "file-upload_2": "sa_uploaded_file_upload_2",
            "file-upload_3": "sa_uploaded_file_upload_3",
            "image-upload_2": "sa_uploaded_image_upload_2",
            "file-upload_4": "sa_uploaded_file_upload_4",
            "file-upload": "sa_uploaded_file_upload",
            "file_upload_6": "sa_uploaded_file_upload_6"
        };

        return map[fieldName] || "";
    }

    function ensureUploadHiddenField(fieldName) {
        const hiddenName = getUploadHiddenName(fieldName);
        if (!hiddenName) return null;

        let hidden = form.querySelector(`input[type="hidden"][name="${hiddenName}"]`);
        if (hidden) return hidden;

        hidden = document.createElement("input");
        hidden.type = "hidden";
        hidden.name = hiddenName;
        form.appendChild(hidden);

        return hidden;
    }

    function setUploadHiddenValue(fieldName, value) {
        const hidden = ensureUploadHiddenField(fieldName);
        if (!hidden) return;
        hidden.value = value || "";
    }

    function clearUploadHiddenValue(fieldName) {
        const hidden = ensureUploadHiddenField(fieldName);
        if (!hidden) return;
        hidden.value = "";
    }

    function shouldShowOtherBooksStep() {
        const checked = form.querySelector('input[name="input_radio_2"]:checked');
        if (!checked) return false;
        return checked.value === "Oui";
    }

    function buildSteps() {
        const includeOtherBooks = shouldShowOtherBooksStep();

        return baseSteps
            .filter(step => !(step.key === "other-books" && !includeOtherBooks))
            .map((step, index) => ({
                ...step,
                shortTitle: String(index + 1)
            }));
    }

    function refreshSteps() {
        steps = buildSteps();

        if (steps.length === 0) {
            currentStep = 0;
            maxVisitedStep = 0;
            return;
        }

        if (currentStep >= steps.length) currentStep = steps.length - 1;
        if (maxVisitedStep >= steps.length) maxVisitedStep = steps.length - 1;
    }

    function getStepElements(step) {
        const elements = [];
        if (step.section) elements.push(...Array.from(form.querySelectorAll(step.section)));
        if (step.content) elements.push(...Array.from(form.querySelectorAll(step.content)));
        return elements;
    }

    function getAllManagedStepElements() {
        const elements = [];
        baseSteps.forEach(step => {
            getStepElements(step).forEach(el => elements.push(el));
        });
        return elements;
    }

    function saveStep(index) {
        try {
            localStorage.setItem(STORAGE_KEY, String(index));
        } catch (e) {}
    }

    function getSavedStep() {
        try {
            const saved = parseInt(localStorage.getItem(STORAGE_KEY), 10);
            if (Number.isInteger(saved) && saved >= 0) return saved;
        } catch (e) {}
        return 0;
    }

    function clearSavedStep() {
        try {
            localStorage.removeItem(STORAGE_KEY);
        } catch (e) {}
    }

    function getFieldStorageKey(field) {
        if (field.name) return field.name;
        if (field.id) return field.id;
        return null;
    }

    function addLinkFieldError(field, message) {
        clearFieldError(field);

        field.classList.add("ff-error-field");

        const container = getFieldErrorContainer(field);
        if (!container) return;

        const error = document.createElement("div");
        error.className = "ff-step-error link-error";
        error.textContent = message;

        container.appendChild(error);
    }

    function collectFormData() {
        const data = {};
        const fields = Array.from(form.querySelectorAll("input, textarea, select"));

        fields.forEach(field => {
            const key = getFieldStorageKey(field);
            if (!key) return;

            const type = (field.type || "").toLowerCase();

            if (type === "password" || type === "file" || type === "submit" || type === "button") {
                return;
            }

            if (type === "radio") {
                if (field.checked) {
                    data[key] = field.value;
                } else if (!(key in data)) {
                    data[key] = "";
                }
                return;
            }

            if (type === "checkbox") {
                if (!data[key]) data[key] = [];
                if (field.checked) data[key].push(field.value);
                return;
            }

            data[key] = field.value;
        });

        return data;
    }

    function saveFormData() {
        try {
            const data = collectFormData();
            localStorage.setItem(FORM_DATA_KEY, JSON.stringify(data));
        } catch (e) {}
    }

    function restoreFormData() {
        try {
            const raw = localStorage.getItem(FORM_DATA_KEY);
            if (!raw) return;

            const data = JSON.parse(raw);
            if (!data || typeof data !== "object") return;

            const fields = Array.from(form.querySelectorAll("input, textarea, select"));

            fields.forEach(field => {
                const key = getFieldStorageKey(field);
                if (!key || !(key in data)) return;

                const type = (field.type || "").toLowerCase();
                const value = data[key];

                if (type === "file" || type === "password" || type === "submit" || type === "button") {
                    return;
                }

                if (type === "radio") {
                    field.checked = field.value === value;
                    return;
                }

                if (type === "checkbox") {
                    field.checked = Array.isArray(value) && value.includes(field.value);
                    return;
                }

                field.value = value;
            });

            form.dispatchEvent(new Event("change", { bubbles: true }));
            form.dispatchEvent(new Event("input", { bubbles: true }));
        } catch (e) {}
    }

    function clearSavedFormData() {
        try {
            localStorage.removeItem(FORM_DATA_KEY);
        } catch (e) {}
    }

    async function apiPost(action, extraData = {}) {
        const formData = new FormData();
        formData.append("action", action);
        formData.append("nonce", ajaxNonce);

        if (sessionId) {
            formData.append("session_id", sessionId);
        }

        Object.entries(extraData).forEach(([key, value]) => {
            if (key === "fields" && value && typeof value === "object" && !Array.isArray(value)) {
                Object.entries(value).forEach(([fieldKey, fieldValue]) => {
                    if (Array.isArray(fieldValue)) {
                        fieldValue.forEach(item => {
                            formData.append(`fields[${fieldKey}][]`, item);
                        });
                    } else {
                        formData.append(`fields[${fieldKey}]`, fieldValue ?? "");
                    }
                });
                return;
            }

            if (Array.isArray(value)) {
                value.forEach(item => formData.append(`${key}[]`, item));
                return;
            }

            formData.append(key, value ?? "");
        });

        const response = await fetch(ajaxUrl, {
            method: "POST",
            body: formData,
            credentials: "same-origin"
        });

        return response.json();
    }

    function setUploadState(isUploading) {
        if (isUploading) {
            activeUploads++;
        } else {
            activeUploads = Math.max(0, activeUploads - 1);
        }

        updateUploadButtonsState();
    }

    function isUploadInProgress() {
        return activeUploads > 0;
    }

    function updateUploadButtonsState() {
        const nextButtons = form.querySelectorAll(".btn-next-clone");
        const prevButtons = form.querySelectorAll(".btn-prev");

        nextButtons.forEach(btn => {
            btn.disabled = isUploadInProgress();
            btn.style.opacity = isUploadInProgress() ? "0.6" : "";
            btn.style.cursor = isUploadInProgress() ? "not-allowed" : "";
        });

        if (submitButton) {
            submitButton.disabled = isUploadInProgress();
            submitButton.style.opacity = isUploadInProgress() ? "0.6" : "";
            submitButton.style.cursor = isUploadInProgress() ? "not-allowed" : "";
        }

        prevButtons.forEach(btn => {
            btn.disabled = isUploadInProgress();
            btn.style.opacity = isUploadInProgress() ? "0.6" : "";
            btn.style.cursor = isUploadInProgress() ? "not-allowed" : "";
        });
    }

    async function clearServerDraft() {
        if (!ajaxUrl || !ajaxNonce) return;

        try {
            await apiPost("sa_clear_onboarding_draft");
        } catch (e) {
            console.error("Erreur suppression brouillon serveur", e);
        }
    }

    async function clearAllFormStateAfterSubmit() {
        clearSavedStep();
        clearSavedFormData();

        await clearServerDraft();

        serverDraft = { fields: {}, uploads: {} };
        currentStep = 0;
        maxVisitedStep = 0;

        try {
            form.reset();
        } catch (e) {}

        form.querySelectorAll(".sa-restored-upload, .sa-upload-progress").forEach(el => el.remove());
        form.querySelectorAll(".sa-upload-slot").forEach(slot => {
            slot.innerHTML = "";
        });
        form.querySelectorAll(".ff-step-error").forEach(el => el.remove());
        form.querySelectorAll(".ff-error-field").forEach(el => el.classList.remove("ff-error-field"));

        [
            "file-upload_2",
            "file-upload_3",
            "image-upload_2",
            "file-upload_4",
            "file-upload",
            "file_upload_6"
        ].forEach(clearUploadHiddenValue);

        setTimeout(() => {
            form.querySelectorAll('input[type="radio"], input[type="checkbox"], select').forEach(field => {
                field.dispatchEvent(new Event("change", { bubbles: true }));
            });

            refreshSteps();
            updateRequiredAsterisks();
            showStep(0);
        }, 50);
    }

    async function fetchServerDraft() {
        if (!ajaxUrl || !ajaxNonce) return;

        try {
            const result = await apiPost("sa_get_onboarding_draft");

            if (result && result.success && result.data) {
                serverDraft = result.data;
            }
        } catch (e) {
            console.error("Erreur récupération brouillon serveur", e);
        }
    }

    function findUploadGroup(field) {
        if (!field) return null;

        const selectors = [
            ".ff-el-group",
            ".ff-field_container",
            ".ff-el-file-upload",
            ".ff-el-image-upload",
            ".ff_upload_wrap",
            ".ff-dropzone"
        ];

        if (field.id) {
            const linkedLabel = form.querySelector(`label[for="${CSS.escape(field.id)}"]`);
            if (linkedLabel) {
                for (const selector of selectors) {
                    const found = linkedLabel.closest(selector);
                    if (found) return found;
                }
            }
        }

        const uploadBtn = form.querySelector(
            `.ff_upload_btn, [data-name="${CSS.escape(field.name || "")}"], [data-target-name="${CSS.escape(field.name || "")}"]`
        );

        if (uploadBtn) {
            for (const selector of selectors) {
                const found = uploadBtn.closest(selector);
                if (found) return found;
            }
        }

        if (field.name) {
            const candidates = Array.from(form.querySelectorAll(".ff-el-group, .ff-field_container, .ff-el-file-upload, .ff-el-image-upload, .ff_upload_wrap, .ff-dropzone"));
            for (const candidate of candidates) {
                if (
                    candidate.querySelector(`[data-name="${CSS.escape(field.name)}"]`) ||
                    candidate.querySelector(`[name="${CSS.escape(field.name)}"]`) ||
                    candidate.querySelector(`#${CSS.escape(field.id || "")}`)
                ) {
                    return candidate;
                }
            }
        }

        return null;
    }

    function ensureUploadSlot(field) {
        const group = findUploadGroup(field);
        if (!group) return null;

        let slot = group.querySelector(".sa-upload-slot");
        if (slot) return slot;

        slot = document.createElement("div");
        slot.className = "sa-upload-slot";

        const helpText =
            group.querySelector(".ff_upload_files_desc") ||
            group.querySelector(".ff-el-help-message") ||
            group.querySelector("small");

        const uploadBtn =
            group.querySelector(".ff_upload_btn") ||
            group.querySelector(".ff-el-form-file") ||
            group.querySelector('label[for="' + field.id + '"]');

        if (helpText) {
            helpText.insertAdjacentElement("afterend", slot);
            return slot;
        }

        if (uploadBtn) {
            uploadBtn.insertAdjacentElement("afterend", slot);
            return slot;
        }

        group.appendChild(slot);
        return slot;
    }

    function initUploadSlots() {
        const fileInputs = Array.from(form.querySelectorAll('input[type="file"]'));
        fileInputs.forEach(field => {
            ensureUploadSlot(field);
        });
    }

    function removeRestoredUploadUi(group) {
        if (!group) return;

        group.querySelectorAll(".sa-restored-upload").forEach(el => el.remove());
        group.querySelectorAll(".sa-upload-progress").forEach(el => el.remove());

        const nativeSelectors = [
            ".ff-uploaded-list",
            ".ff-uploaded-list li",
            ".ff-upload-preview",
            ".ff-upload-preview-item",
            ".ff-el-image-upload-list",
            ".ff-el-image-upload-list li",
            ".ff_file_upload_list",
            ".ff_file_upload_list li",
            ".ff_file_upload_list_item",
            ".dz-preview",
            ".ff-upload-success",
            ".ff-upload-completed"
        ];

        nativeSelectors.forEach(selector => {
            group.querySelectorAll(selector).forEach(el => el.remove());
        });

        const slot = group.querySelector(".sa-upload-slot");
        if (slot) {
            slot.innerHTML = "";
        }
    }

    function renderUploadProgress(field, file) {
        const group = findUploadGroup(field) || field.parentElement || form;
        const slot = ensureUploadSlot(field);

        if (!group || !slot) return null;

        removeRestoredUploadUi(group);

        const wrapper = document.createElement("div");
        wrapper.className = "sa-upload-progress";
        wrapper.innerHTML = `
            <div class="sa-upload-progress__filename">${file.name}</div>
            <div class="sa-upload-progress__bar">
                <div class="sa-upload-progress__fill"></div>
            </div>
            <div class="sa-upload-progress__text">Téléversement en cours...</div>
        `;

        slot.innerHTML = "";
        slot.appendChild(wrapper);

        return wrapper;
    }

    function renderRestoredUpload(field, fileData) {
        if (!field || !fileData || !fileData.url) return;

        const group = findUploadGroup(field) || field.parentElement || form;
        const slot = ensureUploadSlot(field);
        if (!group || !slot) return;

        removeRestoredUploadUi(group);

        const wrapper = document.createElement("div");
        wrapper.className = "sa-restored-upload sa-upload-inline-restored";

        const fileName = fileData.name || "Fichier uploadé";
        const isImage = /\.(jpg|jpeg|png|gif|webp|bmp)$/i.test(fileName) || (fileData.type || "").startsWith("image/");

        wrapper.innerHTML = `
            <div class="sa-upload-inline-restored__inner">
                ${isImage ? `<img src="${fileData.url}" alt="" class="sa-upload-inline-restored__thumb">` : ``}
                <div class="sa-upload-inline-restored__content">
                    <div class="sa-upload-inline-restored__name">${fileName}</div>
                    <div class="sa-upload-inline-restored__actions">
                        <a href="${fileData.url}" target="_blank" rel="noopener" class="sa-upload-view-link">
                            Afficher
                        </a>
                        <button type="button" class="sa-upload-delete-inline">
                            Supprimer
                        </button>
                    </div>
                </div>
            </div>
        `;

        slot.innerHTML = "";
        slot.appendChild(wrapper);

        const deleteBtn = wrapper.querySelector(".sa-upload-delete-inline");
        if (deleteBtn) {
            deleteBtn.addEventListener("click", async function () {
                try {
                    const result = await apiPost("sa_delete_onboarding_file", {
                        field_name: field.name
                    });

                    if (!result || !result.success) {
                        alert(result?.data?.message || "Erreur suppression fichier");
                        return;
                    }

                    removeRestoredUploadUi(group);
                    field.value = "";
                    clearUploadHiddenValue(field.name);

                    if (serverDraft && serverDraft.uploads && field.name) {
                        delete serverDraft.uploads[field.name];
                    }

                    updateFieldAsterisk(field);
                } catch (e) {
                    console.error("Erreur suppression fichier", e);
                    alert("Erreur suppression fichier");
                }
            });
        }
    }

    function restoreServerDraftUploads() {
        if (!serverDraft || !serverDraft.uploads) return;

        const fileInputs = Array.from(form.querySelectorAll('input[type="file"]'));

        fileInputs.forEach(field => {
            const possibleKeys = [
                field.name,
                field.getAttribute("data-name"),
                field.id
            ].filter(Boolean);

            let fileData = null;

            for (const key of possibleKeys) {
                if (serverDraft.uploads[key]) {
                    fileData = serverDraft.uploads[key];
                    break;
                }
            }

            if (!fileData) return;

            setUploadHiddenValue(field.name, JSON.stringify([fileData.url]));
            renderRestoredUpload(field, fileData);
            updateFieldAsterisk(field);
        });
    }

    function bindCustomUploads() {
        if (form.dataset.saUploadBound === "1") return;
        form.dataset.saUploadBound = "1";

        form.addEventListener("change", function (e) {
            const field = e.target;
            if (!(field instanceof HTMLInputElement)) return;
            if ((field.type || "").toLowerCase() !== "file") return;

            const file = field.files && field.files[0] ? field.files[0] : null;
            if (!file) return;

            const group = findUploadGroup(field) || field.parentElement || form;
            if (!group) return;

            setUploadState(true);
            updateUploadButtonsState();

            const progressEl = renderUploadProgress(field, file);
            const progressFill = progressEl ? progressEl.querySelector(".sa-upload-progress__fill") : null;
            const progressText = progressEl ? progressEl.querySelector(".sa-upload-progress__text") : null;

            const formData = new FormData();
            formData.append("action", "sa_upload_onboarding_file");
            formData.append("nonce", ajaxNonce);
            formData.append("field_name", field.name || field.getAttribute("name") || "");
            formData.append("file", file);

            if (sessionId) {
                formData.append("session_id", sessionId);
            }

            try {
                const xhr = new XMLHttpRequest();
                xhr.open("POST", ajaxUrl, true);
                xhr.withCredentials = true;

                xhr.upload.addEventListener("progress", function (evt) {
                    if (!evt.lengthComputable || !progressFill || !progressText) return;

                    const percent = Math.round((evt.loaded / evt.total) * 100);
                    progressFill.style.width = percent + "%";
                    progressText.textContent = percent + "%";
                });

                xhr.onload = function () {
                    let result = null;

                    try {
                        result = JSON.parse(xhr.responseText);
                    } catch (err) {
                        console.error("Réponse serveur invalide :", xhr.responseText);
                        removeRestoredUploadUi(group);
                        setUploadState(false);
                        updateUploadButtonsState();
                        alert("Réponse serveur invalide");
                        return;
                    }

                    if (!result || !result.success || !result.data || !result.data.file) {
                        console.error("Erreur upload :", result);
                        removeRestoredUploadUi(group);
                        setUploadState(false);
                        updateUploadButtonsState();
                        alert(result?.data?.message || "Erreur upload fichier");
                        return;
                    }

                    if (!serverDraft.uploads) {
                        serverDraft.uploads = {};
                    }

                    serverDraft.uploads[field.name] = result.data.file;
                    setUploadHiddenValue(field.name, JSON.stringify([result.data.file.url]));

                    renderRestoredUpload(field, result.data.file);
                    clearFieldError(field);
                    updateFieldAsterisk(field);
                    setUploadState(false);
                    updateUploadButtonsState();
                };

                xhr.onerror = function () {
                    console.error("Erreur réseau upload");
                    removeRestoredUploadUi(group);
                    setUploadState(false);
                    updateUploadButtonsState();
                    alert("Erreur réseau pendant l'upload");
                };

                xhr.send(formData);
            } catch (err) {
                console.error("Erreur upload fichier", err);
                removeRestoredUploadUi(group);
                setUploadState(false);
                updateUploadButtonsState();
                alert("Erreur upload fichier");
            }
        });
    }

    function debounceServerSave() {
        clearTimeout(saveDraftTimeout);

        saveDraftTimeout = setTimeout(async () => {
            if (!ajaxUrl || !ajaxNonce) return;

            try {
                const fields = collectFormData();
                await apiPost("sa_save_onboarding_fields", { fields });
            } catch (e) {
                console.error("Erreur sauvegarde brouillon serveur", e);
            }
        }, 500);
    }

    function hideAllSteps() {
        const hiddenEls = new Set();

        baseSteps.forEach(step => {
            getStepElements(step).forEach(el => {
                if (hiddenEls.has(el)) return;
                el.style.setProperty("display", "none", "important");
                hiddenEls.add(el);
            });
        });
    }

    function rememberRequiredState(field) {
        if (!field.dataset.saRequiredInitialised) {
            field.dataset.saRequiredInitialised = "1";
            field.dataset.saWasRequired = (
                field.required || field.getAttribute("aria-required") === "true"
            ) ? "1" : "0";
        }
    }

    function isActuallyRequired(field) {
        return field.required || field.getAttribute("aria-required") === "true";
    }

    function getFieldLabel(field) {
        if (!field) return null;

        const type = (field.type || "").toLowerCase();

        if ((type === "radio" || type === "checkbox") && field.name) {
            const group = field.closest(".ff-el-group, .ff-field_container");
            if (group) {
                const groupLabel = group.querySelector(":scope > .ff-el-input--label label");
                if (groupLabel) return groupLabel;
            }
        }

        if (field.id) {
            const directLabel = form.querySelector(`label[for="${CSS.escape(field.id)}"]`);
            if (directLabel) {
                const inlineChoiceLabel = directLabel.closest(".ff-el-form-check-label");
                if (!inlineChoiceLabel) return directLabel;
            }
        }

        const group = field.closest(".ff-el-group, .ff-field_container, .ff-name-address-wrapper, .ff-t-cell");
        if (group) {
            const groupLabel = group.querySelector(":scope > .ff-el-input--label label");
            if (groupLabel) return groupLabel;
        }

        const parentGroup = field.closest(".ff-el-group, .ff-field_container");
        if (parentGroup) {
            const label = parentGroup.querySelector(".ff-el-input--label label");
            if (label) return label;
        }

        return null;
    }

    function syncLabelRequiredClass(labelWrapper, isRequired) {
        if (!labelWrapper) return;

        if (isRequired) {
            labelWrapper.classList.add("ff-el-is-required");
            labelWrapper.classList.add("asterisk-right");
        } else {
            labelWrapper.classList.remove("ff-el-is-required");
        }
    }

    function updateFieldAsterisk(field) {
        if (!field) return;

        const label = getFieldLabel(field);
        if (!label) return;

        const labelWrapper = label.closest(".ff-el-input--label");
        const required = isActuallyRequired(field);

        syncLabelRequiredClass(labelWrapper, required);

        let star = label.querySelector(".sa-required-star");

        if (required) {
            if (!star) {
                star = document.createElement("span");
                star.className = "sa-required-star";
                star.setAttribute("aria-hidden", "true");
                star.textContent = "*";
                label.appendChild(star);
            }
        } else if (star) {
            star.remove();
        }
    }

    function updateRadioGroupAsterisk(name) {
        if (!name) return;

        const radios = Array.from(form.querySelectorAll(`input[type="radio"][name="${CSS.escape(name)}"]`));
        if (!radios.length) return;

        const required = radios.some(isActuallyRequired);
        const label = getFieldLabel(radios[0]);
        if (!label) return;

        const labelWrapper = label.closest(".ff-el-input--label");
        syncLabelRequiredClass(labelWrapper, required);

        let star = label.querySelector(".sa-required-star");

        if (required) {
            if (!star) {
                star = document.createElement("span");
                star.className = "sa-required-star";
                star.setAttribute("aria-hidden", "true");
                star.textContent = "*";
                label.appendChild(star);
            }
        } else if (star) {
            star.remove();
        }
    }

    function updateCheckboxGroupAsterisk(name) {
        if (!name) return;

        const checkboxes = Array.from(form.querySelectorAll(`input[type="checkbox"][name="${CSS.escape(name)}"]`));
        if (!checkboxes.length) return;

        const required = checkboxes.some(isActuallyRequired);
        const label = getFieldLabel(checkboxes[0]);
        if (!label) return;

        const labelWrapper = label.closest(".ff-el-input--label");
        syncLabelRequiredClass(labelWrapper, required);

        let star = label.querySelector(".sa-required-star");

        if (required) {
            if (!star) {
                star = document.createElement("span");
                star.className = "sa-required-star";
                star.setAttribute("aria-hidden", "true");
                star.textContent = "*";
                label.appendChild(star);
            }
        } else if (star) {
            star.remove();
        }
    }

    function updateRequiredAsterisks(scope = form) {
        const fields = scope.querySelectorAll("input, textarea, select");
        const processedRadioNames = new Set();
        const processedCheckboxNames = new Set();

        fields.forEach(field => {
            const type = (field.type || "").toLowerCase();

            if (type === "hidden" || type === "submit" || type === "button") {
                return;
            }

            if (type === "radio" && field.name) {
                if (processedRadioNames.has(field.name)) return;
                processedRadioNames.add(field.name);
                updateRadioGroupAsterisk(field.name);
                return;
            }

            if (type === "checkbox" && field.name) {
                if (processedCheckboxNames.has(field.name)) return;
                processedCheckboxNames.add(field.name);
                updateCheckboxGroupAsterisk(field.name);
                return;
            }

            updateFieldAsterisk(field);
        });
    }

    function setFieldRequired(field, required = true) {
        if (!field) return;

        if (required && !isFieldHidden(field)) {
            field.required = true;
            field.setAttribute("aria-required", "true");
            field.setAttribute("required", "required");
        } else {
            field.required = false;
            field.removeAttribute("required");
            field.removeAttribute("aria-required");
            field.setCustomValidity("");
        }

        updateFieldAsterisk(field);
    }

    function setRadioGroupRequired(name, required = true) {
        if (!name) return;

        const radios = form.querySelectorAll(`input[type="radio"][name="${CSS.escape(name)}"]`);
        radios.forEach(radio => {
            if (required) {
                radio.required = true;
                radio.setAttribute("aria-required", "true");
            } else {
                radio.required = false;
                radio.removeAttribute("aria-required");
            }
        });

        if (radios[0]) updateRadioGroupAsterisk(name);
    }

    function setCheckboxGroupRequired(name, required = true) {
        if (!name) return;

        const checkboxes = form.querySelectorAll(`input[type="checkbox"][name="${CSS.escape(name)}"]`);
        checkboxes.forEach(checkbox => {
            if (required && !isFieldHidden(checkbox)) {
                checkbox.required = true;
                checkbox.setAttribute("aria-required", "true");
                checkbox.setAttribute("required", "required");
            } else {
                checkbox.required = false;
                checkbox.removeAttribute("required");
                checkbox.removeAttribute("aria-required");
                checkbox.setCustomValidity("");
            }
        });

        if (checkboxes[0]) updateCheckboxGroupAsterisk(name);
    }

    function isFieldHidden(field) {
        if (!field) return true;

        if (field.type === "hidden") return true;
        if (field.hidden) return true;
        if (field.closest("[hidden]")) return true;
        if (field.closest(".ff_excluded")) return true;
        if (field.closest(".ff-el-group.ff_excluded")) return true;
        if (field.closest(".ff-step-content") && !field.closest(".ff-step-content.active")) return true;
        if (field.closest(".ff_conditional_hidden")) return true;
        if (field.closest(".ff-el-is-hidden")) return true;

        const style = window.getComputedStyle(field);
        if (style.display === "none" || style.visibility === "hidden") return true;

        const rect = field.getBoundingClientRect();
        if (rect.width === 0 && rect.height === 0) return true;

        return false;
    }

    function clearHiddenFieldRequirement(field) {
        if (!field) return;

        field.required = false;
        field.removeAttribute("required");
        field.removeAttribute("aria-required");
        field.setCustomValidity("");

        updateFieldAsterisk(field);
    }

    function clearHiddenGroupRequirement(selector) {
        form.querySelectorAll(selector).forEach(field => {
            if (isFieldHidden(field)) {
                clearHiddenFieldRequirement(field);
            }
        });
    }

    function cleanupHiddenRequiredFieldsBeforeSubmit() {
        form.querySelectorAll("input, select, textarea").forEach(field => {
            if (isFieldHidden(field)) {
                clearHiddenFieldRequirement(field);
            }
        });

        clearHiddenGroupRequirement('input[type="checkbox"][name="checkbox[]"]');
        clearHiddenGroupRequirement('input[type="checkbox"][name="checkbox_1[]"]');
        clearHiddenGroupRequirement('input[type="file"][name="file-upload_2"]');
        clearHiddenGroupRequirement('input[type="file"][name="file-upload_3"]');
        clearHiddenGroupRequirement('input[type="file"][name="file-upload"]');
        clearHiddenGroupRequirement('input[type="file"][name="file_upload_6"]');
        clearHiddenGroupRequirement('input[type="file"][name="file-upload_4"]');
        clearHiddenGroupRequirement('input[type="file"][name="image-upload_2"]');
    }
    
    function syncCustomRequiredFields() {
        setCheckboxGroupRequired("checkbox[]", false);
        setCheckboxGroupRequired("checkbox_1[]", false);

        [
            "file-upload_2",
            "file-upload_3",
            "image-upload_2",
            "file-upload",
            "file_upload_6",
            "file-upload_4"
        ].forEach(name => {
            const field = form.querySelector(`input[type="file"][name="${CSS.escape(name)}"]`);
            if (field) setFieldRequired(field, false);
        });

        const currentStepDef = steps[currentStep];
        if (!currentStepDef) {
            updateRequiredAsterisks();
            return;
        }

        if (currentStepDef.key === "general") {
            setCheckboxGroupRequired("checkbox[]", true);

            const couverture = form.querySelector('input[type="file"][name="file-upload_2"]');
            if (couverture) setFieldRequired(couverture, true);
        }

        if (currentStepDef.key === "hero") {
            const imageHero = form.querySelector('input[type="file"][name="file-upload_3"]');
            if (imageHero) setFieldRequired(imageHero, true);
        }

        if (currentStepDef.key === "author") {
            const photoAuteur = form.querySelector('input[type="file"][name="image-upload_2"]');
            if (photoAuteur) setFieldRequired(photoAuteur, true);
        }

        if (currentStepDef.key === "emails") {
            const radioOui = form.querySelector('input[type="radio"][name="input_radio_3"][value="Oui"]:checked');
            const pdfBonus = form.querySelector('input[type="file"][name="file-upload"]');

            if (radioOui && pdfBonus && !pdfBonus.closest(".ff_excluded")) {
                setFieldRequired(pdfBonus, true);
            }
        }

        if (currentStepDef.key === "other-books") {
            const autresRomansUpload = form.querySelector('input[type="file"][name="file_upload_6"]');
            if (autresRomansUpload) setFieldRequired(autresRomansUpload, true);
        }

        if (currentStepDef.key === "recap") {
            setCheckboxGroupRequired("checkbox_1[]", true);
        }

        if (currentStepDef.key === "technical") {
            const radioLogoOui = form.querySelector('input[type="radio"][name="input_radio_8"][value="Oui"]:checked');
            const logoField = form.querySelector('input[type="file"][name="file-upload_4"]');

            if (radioLogoOui && logoField && !logoField.closest(".ff_excluded")) {
                setFieldRequired(logoField, true);
            }
        }

        updateRequiredAsterisks();
    }

    function disableFieldForWizard(field) {
        if (!field) return;

        const type = (field.type || "").toLowerCase();

        if (type === "hidden") return;
        if (submitButton && field === submitButton) return;

        rememberRequiredState(field);

        if (type === "file") {
            field.required = false;
            field.removeAttribute("aria-required");
            updateFieldAsterisk(field);
            return;
        }

        field.disabled = true;
        field.required = false;
        field.removeAttribute("aria-required");
        updateFieldAsterisk(field);
    }

    function enableFieldForWizard(field) {
        if (!field) return;

        const type = (field.type || "").toLowerCase();

        if (type === "hidden") return;
        if (submitButton && field === submitButton) return;

        const wasRequired = field.dataset.saWasRequired === "1";

        if (type !== "file") {
            field.disabled = false;
        }

        if (wasRequired) {
            field.required = true;
            field.setAttribute("aria-required", "true");
        } else {
            field.required = false;
            field.removeAttribute("aria-required");
        }

        updateFieldAsterisk(field);
    }

    function setFieldsStateInContainer(container, disabled) {
        if (!container) return;

        const fields = container.querySelectorAll("input, textarea, select, button");

        fields.forEach(field => {
            if (disabled) {
                disableFieldForWizard(field);
            } else {
                enableFieldForWizard(field);
            }
        });
    }

    function disableAllManagedStepFields() {
        const allStepElements = getAllManagedStepElements();
        allStepElements.forEach(el => setFieldsStateInContainer(el, true));
    }

    function enableFieldsForStep(index) {
        const step = steps[index];
        if (!step) return;

        const stepEls = getStepElements(step);
        stepEls.forEach(el => setFieldsStateInContainer(el, false));
    }

    function enableAllFieldsBeforeSubmit() {
        const allStepElements = getAllManagedStepElements();
        allStepElements.forEach(el => setFieldsStateInContainer(el, false));
    }

    function showStep(index, shouldScroll = true) {
        refreshSteps();
        createProgressUI();
        hideAllSteps();
        disableAllManagedStepFields();

        const step = steps[index];
        if (!step) return;

        const stepEls = getStepElements(step);

        if (!stepEls.length) {
            console.warn("Aucun élément trouvé pour l’étape :", step);
            return;
        }

        stepEls.forEach(el => {
            el.style.setProperty("display", "block", "important");
        });

        enableFieldsForStep(index);
        syncCustomRequiredFields();

        updateProgressUI(index);
        toggleSubmitButton(index);

        if (index === steps.length - 1) {
            removeNavRows();
            placeSubmitInNav(index);
        } else {
            addNavigationRow(index);
        }

        saveStep(index);

        if (shouldScroll) {
            form.scrollIntoView({
                behavior: "smooth",
                block: "start"
            });
        }
    }

    function createProgressUI() {
        let progress = document.querySelector(".ff-step-progress");

        if (!progress) {
            progress = document.createElement("div");
            progress.className = "ff-step-progress";

            const header = document.createElement("div");
            header.className = "ff-step-progress-header";

            const progressText = document.createElement("div");
            progressText.className = "ff-step-progress-text";

            const progressCount = document.createElement("div");
            progressCount.className = "ff-step-progress-count";

            header.appendChild(progressText);
            header.appendChild(progressCount);

            const dots = document.createElement("div");
            dots.className = "ff-step-dots";

            progress.appendChild(header);
            progress.appendChild(dots);

            const formWrapper = form.parentElement;
            if (formWrapper) formWrapper.insertBefore(progress, form);
        }

        const dotsContainer = progress.querySelector(".ff-step-dots");
        if (!dotsContainer) return;

        dotsContainer.innerHTML = "";

        steps.forEach((step, index) => {
            const dot = document.createElement("button");
            dot.type = "button";
            dot.className = "ff-step-dot";
            dot.setAttribute("data-step", index);

            const top = document.createElement("div");
            top.className = "ff-step-dot-top";

            const circle = document.createElement("div");
            circle.className = "ff-step-dot-circle";
            circle.textContent = step.shortTitle;

            top.appendChild(circle);

            if (index < steps.length - 1) {
                const line = document.createElement("div");
                line.className = "ff-step-dot-line";
                top.appendChild(line);
            }

            const label = document.createElement("div");
            label.className = "ff-step-dot-label";
            label.textContent = step.title;

            dot.appendChild(top);
            dot.appendChild(label);

            dot.addEventListener("click", function (e) {
                e.preventDefault();

                const targetStep = parseInt(dot.getAttribute("data-step"), 10);
                if (!Number.isInteger(targetStep)) return;

                if (targetStep <= maxVisitedStep) {
                    currentStep = targetStep;
                    showStep(currentStep);
                }
            });

            dotsContainer.appendChild(dot);
        });
    }

    function updateProgressUI(index) {
        const progress = document.querySelector(".ff-step-progress");
        if (!progress) return;

        const progressText = progress.querySelector(".ff-step-progress-text");
        const progressCount = progress.querySelector(".ff-step-progress-count");
        const dots = progress.querySelectorAll(".ff-step-dot");

        if (progressText) {
            progressText.textContent = "Formulaire d'informations à compléter.";
        }

        if (progressCount) {
            progressCount.textContent = `Étape ${index + 1} sur ${steps.length}`;
        }

        dots.forEach((dot, dotIndex) => {
            dot.classList.remove("is-active", "is-done", "is-locked");

            if (dotIndex < index) {
                dot.classList.add("is-done");
            } else if (dotIndex === index) {
                dot.classList.add("is-active");
            }

            if (dotIndex > maxVisitedStep) {
                dot.classList.add("is-locked");
            }
        });
    }

    function toggleSubmitButton(index) {
        if (!submitWrapper) return;

        submitWrapper.classList.add("ff-step-final-submit-wrapper");

        if (submitButton) {
            submitButton.classList.add("ff-step-final-submit-btn");
        }

        if (index === steps.length - 1) {
            submitWrapper.style.setProperty("display", "block", "important");
        } else {
            submitWrapper.style.setProperty("display", "none", "important");
        }

        updateUploadButtonsState();
    }

    function removeNavRows() {
        form.querySelectorAll(".ff-step-nav-row").forEach(el => el.remove());
    }

    function createButton(text, className, type = "button") {
        const button = document.createElement("button");
        button.type = type;
        button.className = className;
        button.textContent = text;
        return button;
    }

    function getCurrentStepContainer(index) {
        const step = steps[index];
        if (!step) return null;
        return form.querySelector(step.content);
    }

    function addNavigationRow(index) {
        removeNavRows();

        const currentContent = getCurrentStepContainer(index);
        if (!currentContent) return;

        const nextWrapper = currentContent.querySelector(".ff_submit_btn_wrapper_custom");
        if (!nextWrapper) return;

        const nextButton = nextWrapper.querySelector(".btn-next");
        if (!nextButton) return;

        nextWrapper.style.display = "none";

        const navRow = document.createElement("div");
        navRow.className = "ff-step-nav-row";

        const left = document.createElement("div");
        left.className = "ff-step-nav-left";

        const right = document.createElement("div");
        right.className = "ff-step-nav-right";

        if (index > 0) {
            const prevBtn = createButton("Précédent", "ff-btn ff-btn-md btn-prev ff_btn_style");
            prevBtn.addEventListener("click", function (e) {
                e.preventDefault();
                if (currentStep > 0) {
                    currentStep--;
                    showStep(currentStep);
                }
            });
            left.appendChild(prevBtn);
        }

        const nextBtnClone = createButton("Suivant", "ff-btn ff-btn-md btn-next-clone ff_btn_style");
        nextBtnClone.addEventListener("click", function (e) {
            e.preventDefault();

            const isValid = validateCurrentStep(currentStep);
            if (!isValid) return;

            refreshSteps();

            if (currentStep < steps.length - 1) {
                currentStep++;
                if (currentStep > maxVisitedStep) {
                    maxVisitedStep = currentStep;
                }
                saveStep(currentStep);
                showStep(currentStep);
            }
        });

        right.appendChild(nextBtnClone);
        navRow.appendChild(left);
        navRow.appendChild(right);

        nextWrapper.parentNode.insertBefore(navRow, nextWrapper);
    }

    function placeSubmitInNav(index) {
        if (!submitWrapper || index !== steps.length - 1) return;

        const currentContent = getCurrentStepContainer(index);
        if (!currentContent) return;

        let navRow = currentContent.querySelector(".ff-step-nav-row");

        if (!navRow) {
            navRow = document.createElement("div");
            navRow.className = "ff-step-nav-row";

            const left = document.createElement("div");
            left.className = "ff-step-nav-left";

            const right = document.createElement("div");
            right.className = "ff-step-nav-right";

            navRow.appendChild(left);
            navRow.appendChild(right);
            currentContent.appendChild(navRow);
        }

        const left = navRow.querySelector(".ff-step-nav-left");
        const right = navRow.querySelector(".ff-step-nav-right");

        if (left && !left.querySelector(".btn-prev")) {
            const prevBtn = createButton("Précédent", "ff-btn ff-btn-md btn-prev ff_btn_style");
            prevBtn.addEventListener("click", function (e) {
                e.preventDefault();
                if (currentStep > 0) {
                    currentStep--;
                    showStep(currentStep);
                }
            });
            left.appendChild(prevBtn);
        }

        if (right && submitWrapper.parentNode !== right) {
            right.innerHTML = "";
            right.appendChild(submitWrapper);
        }
    }

    function isVisible(el) {
        return !!(el.offsetWidth || el.offsetHeight || el.getClientRects().length);
    }

    function isExcluded(el) {
        return !!el.closest(".ff_excluded");
    }

    function isFieldEligible(field) {
        if (!field) return false;
        if (!isVisible(field)) return false;
        if (field.disabled) return false;
        if (isExcluded(field)) return false;
        return true;
    }

    function clearStepErrors(stepIndex) {
        const stepContent = getCurrentStepContainer(stepIndex);
        if (!stepContent) return;

        stepContent.querySelectorAll(".ff-step-error").forEach(el => el.remove());
        stepContent.querySelectorAll(".ff-error-field").forEach(el => el.classList.remove("ff-error-field"));
    }

    function getFieldErrorContainer(field) {
        if (!field) return null;

        const type = (field.type || "").toLowerCase();

        if (type === "file") {
            return findUploadGroup(field) || field.parentElement || form;
        }

        const repeaterCell = field.closest("td");
        if (repeaterCell) {
            return repeaterCell;
        }

        return (
            field.closest(".ff-el-group") ||
            field.closest(".ff-el-input--content") ||
            field.parentElement ||
            form
        );
    }

    function addFieldError(field, message) {
        const container = getFieldErrorContainer(field);
        if (!container) return;

        if (field.classList) {
            field.classList.add("ff-error-field");
        }

        if (!container.querySelector(".ff-step-error")) {
            const error = document.createElement("div");
            error.className = "ff-step-error";
            error.textContent = message;
            container.appendChild(error);
        }
    }

    function hasFluentFilePreview(field) {
        const group = findUploadGroup(field) || field.parentElement || form;
        if (!group) return false;

        const previewSelectors = [
            ".ff-uploaded-list li",
            ".ff-upload-preview",
            ".ff-upload-preview-item",
            ".ff-el-image-upload-list li",
            ".ff_file_upload_list li",
            ".ff_file_upload_list_item",
            ".dz-preview",
            ".ff-upload-success",
            ".ff-upload-completed",
            ".sa-restored-upload",
            ".sa-upload-inline-restored",
            ".sa-upload-progress"
        ];

        for (const selector of previewSelectors) {
            const preview = group.querySelector(selector);
            if (preview) {
                return true;
            }
        }

        const uploadedList = group.querySelector(".ff-uploaded-list");
        if (uploadedList) {
            const uploadedText = (uploadedList.textContent || "").toLowerCase();
            if (uploadedText.includes("100%") || uploadedText.includes("terminé")) {
                return true;
            }
        }

        return false;
    }

    function getFieldValue(field) {
        const type = (field.type || "").toLowerCase();
        const tag = field.tagName.toLowerCase();

        if (type === "file") {
            if (field.files && field.files.length > 0) {
                return "[file]";
            }

            if (hasFluentFilePreview(field)) {
                return "[file]";
            }

            return "";
        }

        if (type === "checkbox" || type === "radio") {
            return field.checked ? (field.value || "checked") : "";
        }

        if (tag === "select") {
            return (field.value || "").trim();
        }

        return (field.value || "").trim();
    }

    function isFieldFilled(field) {
        return getFieldValue(field) !== "";
    }

    function validateRadioGroup(scope, name) {
        const radios = Array.from(
            scope.querySelectorAll(`input[type="radio"][name="${CSS.escape(name)}"]`)
        ).filter(isFieldEligible);

        if (!radios.length) return true;

        const isRequired = radios.some(isActuallyRequired);
        if (!isRequired) return true;

        const checked = radios.some(radio => radio.checked);

        if (!checked) {
            addFieldError(radios[0], "Veuillez sélectionner une option.");
            return false;
        }

        return true;
    }

    function validateCheckboxGroup(scope, name) {
        const checkboxes = Array.from(
            scope.querySelectorAll(`input[type="checkbox"][name="${CSS.escape(name)}"]`)
        ).filter(isFieldEligible);

        if (!checkboxes.length) return true;

        const isRequired = checkboxes.some(isActuallyRequired);
        if (!isRequired) return true;

        const checked = checkboxes.some(checkbox => checkbox.checked);

        if (!checked) {
            addFieldError(checkboxes[0], "Veuillez sélectionner au moins une option.");
            return false;
        }

        return true;
    }

    function validateSingleField(field) {
        if (!isFieldEligible(field)) return true;
        if (!isActuallyRequired(field)) return true;

        const type = (field.type || "").toLowerCase();
        const tag = field.tagName.toLowerCase();
        const value = getFieldValue(field);

        if (type === "radio" || type === "checkbox") return true;

        if (type === "file") {
            const hasPreview = hasFluentFilePreview(field);

            if (!value && !hasPreview) {
                addFieldError(field, "Veuillez ajouter un fichier.");
                return false;
            }

            return true;
        }

        if (tag === "select" && value === "") {
            addFieldError(field, "Veuillez sélectionner une option.");
            return false;
        }

        if (value === "") {
            addFieldError(field, "Ce champ est obligatoire.");
            return false;
        }

        if (type === "email") {
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(value)) {
                addFieldError(field, "Veuillez entrer une adresse email valide.");
                return false;
            }
        }

        return true;
    }

    function validateRepeater(stepContent) {
        let isValid = true;
        let firstInvalidField = null;

        const repeaters = stepContent.querySelectorAll(".ff-el-repeater");

        repeaters.forEach(repeater => {
            if (
                repeater.classList.contains("book-links") ||
                repeater.getAttribute("data-name") === "repeater_field_5"
            ) return;

            if (!isVisible(repeater) || isExcluded(repeater)) return;

            const rows = repeater.querySelectorAll("tbody tr");

            rows.forEach(row => {
                const rowFields = Array.from(row.querySelectorAll("input, textarea, select"))
                    .filter(isFieldEligible);

                if (!rowFields.length) return;

                const rowHasAnyValue = rowFields.some(isFieldFilled);
                if (!rowHasAnyValue) return;

                const processedRadioNames = new Set();
                const processedCheckboxNames = new Set();

                rowFields.forEach(field => {
                    const type = (field.type || "").toLowerCase();

                    if (type === "radio") {
                        if (!field.name || processedRadioNames.has(field.name)) return;
                        processedRadioNames.add(field.name);

                        const valid = validateRadioGroup(row, field.name);
                        if (!valid) {
                            isValid = false;
                            if (!firstInvalidField) firstInvalidField = field;
                        }
                        return;
                    }

                    if (type === "checkbox" && field.name) {
                        if (processedCheckboxNames.has(field.name)) return;
                        processedCheckboxNames.add(field.name);

                        const valid = validateCheckboxGroup(row, field.name);
                        if (!valid) {
                            isValid = false;
                            if (!firstInvalidField) firstInvalidField = field;
                        }
                        return;
                    }

                    const valid = validateSingleField(field);
                    if (!valid) {
                        isValid = false;
                        if (!firstInvalidField) firstInvalidField = field;
                    }
                });
            });
        });

        return { isValid, firstInvalidField };
    }

    function validateStandardFields(stepContent) {
        let isValid = true;
        let firstInvalidField = null;

        const fields = Array.from(stepContent.querySelectorAll("input, textarea, select")).filter(field => {
            if (!isFieldEligible(field)) return false;
            if (field.closest(".ff-el-repeater")) return false;
            return true;
        });

        const processedRadioNames = new Set();
        const processedCheckboxNames = new Set();

        fields.forEach(field => {
            const type = (field.type || "").toLowerCase();

            if (type === "radio") {
                if (!field.name || processedRadioNames.has(field.name)) return;
                processedRadioNames.add(field.name);

                const valid = validateRadioGroup(stepContent, field.name);
                if (!valid) {
                    isValid = false;
                    if (!firstInvalidField) firstInvalidField = field;
                }
                return;
            }

            if (type === "checkbox" && field.name) {
                if (processedCheckboxNames.has(field.name)) return;
                processedCheckboxNames.add(field.name);

                const valid = validateCheckboxGroup(stepContent, field.name);
                if (!valid) {
                    isValid = false;
                    if (!firstInvalidField) firstInvalidField = field;
                }
                return;
            }

            const valid = validateSingleField(field);
            if (!valid) {
                isValid = false;
                if (!firstInvalidField) firstInvalidField = field;
            }
        });

        return { isValid, firstInvalidField };
    }

    function validateFormatsDisponibles(stepContent) {
        const checkboxes = Array.from(
            stepContent.querySelectorAll('input[type="checkbox"][name="checkbox[]"]')
        ).filter(isFieldEligible);

        if (!checkboxes.length) {
            return { isValid: true, firstInvalidField: null };
        }

        const hasChecked = checkboxes.some(checkbox => checkbox.checked);

        if (!hasChecked) {
            addFieldError(checkboxes[0], "Veuillez sélectionner au moins un format.");
            return { isValid: false, firstInvalidField: checkboxes[0] };
        }

        return { isValid: true, firstInvalidField: null };
    }

    function validateReseauxSociaux(stepContent) {
        const repeater = stepContent.querySelector('[data-name="repeater_field_3"].ff-el-repeater');
        if (!repeater || !isVisible(repeater) || isExcluded(repeater)) {
            return { isValid: true, firstInvalidField: null };
        }

        const rows = Array.from(repeater.querySelectorAll("tbody tr"));

        let isValid = true;
        let firstInvalidField = null;

        rows.forEach((row) => {
            const inputs = Array.from(
                row.querySelectorAll('input[type="text"], input[type="url"]')
            ).filter(isFieldEligible);

            if (inputs.length < 2) return;

            const nomField = inputs[0];
            const lienField = inputs[1];

            const nomValue = (nomField.value || "").trim();
            const lienValue = (lienField.value || "").trim();

            const rowIsEmpty = nomValue === "" && lienValue === "";
            const rowIsComplete = nomValue !== "" && lienValue !== "";

            if (rowIsEmpty) {
                return;
            }

            if (!rowIsComplete) {
                if (nomValue === "") {
                    addFieldError(nomField, "Veuillez renseigner le nom du réseau.");
                    if (!firstInvalidField) firstInvalidField = nomField;
                    isValid = false;
                }

                if (lienValue === "") {
                    addFieldError(lienField, "Veuillez renseigner le lien.");
                    if (!firstInvalidField) firstInvalidField = lienField;
                    isValid = false;
                }

                return;
            }

            if (!isValidUrlLike(lienValue)) {
                addLinkFieldError(lienField, "Veuillez entrer un lien valide (ex : https://instagram.com).");
                if (!firstInvalidField) firstInvalidField = lienField;
                isValid = false;
            }
        });

        return { isValid, firstInvalidField };
    }

    function validateLogo(stepContent) {
        const radioOui = stepContent.querySelector('input[type="radio"][value="Oui"]:checked');

        if (!radioOui) {
            return { isValid: true, firstInvalidField: null };
        }

        const fileField = stepContent.querySelector('input[type="file"][name="file-upload_4"]');

        if (!fileField || !isFieldEligible(fileField)) {
            return { isValid: true, firstInvalidField: null };
        }

        const hasValue = getFieldValue(fileField) !== "";
        const hasPreview = hasFluentFilePreview(fileField);

        if (!hasValue && !hasPreview) {
            addFieldError(fileField, "Veuillez ajouter votre logo.");
            return { isValid: false, firstInvalidField: fileField };
        }

        return { isValid: true, firstInvalidField: null };
    }

    function validateBlocsIconesRecap(stepContent) {
        const checkboxes = Array.from(
            stepContent.querySelectorAll('input[type="checkbox"][name="checkbox_1[]"]')
        ).filter(isFieldEligible);

        if (!checkboxes.length) {
            return { isValid: true, firstInvalidField: null };
        }

        const hasChecked = checkboxes.some(checkbox => checkbox.checked);

        if (!hasChecked) {
            addFieldError(checkboxes[0], "Veuillez sélectionner au moins un bloc icône.");
            return { isValid: false, firstInvalidField: checkboxes[0] };
        }

        return { isValid: true, firstInvalidField: null };
    }

    function validateDisplayedRating(stepContent) {
        const field = stepContent.querySelector('input[name="input_text_10"]');

        if (!field || !isFieldEligible(field)) {
            return { isValid: true, firstInvalidField: null };
        }

        const value = (field.value || "").trim();

        if (value === "") {
            return { isValid: true, firstInvalidField: null };
        }

        if (!isValidDisplayedRating(value)) {
            addFieldError(field, "Format valide : 4.75/5, 8.5/10 ou 82.45/100");
            return { isValid: false, firstInvalidField: field };
        }

        return { isValid: true, firstInvalidField: null };
    }

    function validateReviewRatings(stepContent) {
        const fields = [
            'input[name="input_text_29"]',
            'input[name="input_text_33"]',
            'input[name="input_text_31"]'
        ];

        let isValid = true;
        let firstInvalidField = null;

        fields.forEach(selector => {
            const field = stepContent.querySelector(selector);

            if (!field || !isFieldEligible(field)) return;

            const value = (field.value || "").trim();

            if (value === "") return;

            if (!isValidDisplayedRating(value)) {
                addFieldError(field, "Format valide : 4.75/5, 8.5/10 ou 82.45/100");
                isValid = false;
                if (!firstInvalidField) firstInvalidField = field;
            }
        });

        return { isValid, firstInvalidField };
    }

    function validateHeroMainButtonLink(stepContent) {
        const field = stepContent.querySelector('input[name="input_text_13"]');

        if (!field || !isFieldEligible(field)) {
            return { isValid: true, firstInvalidField: null };
        }

        const value = (field.value || "").trim();

        if (value === "") {
            addFieldError(field, "Veuillez renseigner le lien du bouton principal.");
            return { isValid: false, firstInvalidField: field };
        }

        if (!isValidUrlLike(value)) {
            addLinkFieldError(field, "Veuillez entrer un lien valide (ex : https://amazon.fr).");
            return { isValid: false, firstInvalidField: field };
        }

        return { isValid: true, firstInvalidField: null };
    }

    function isValidUrlLike(value) {
        const v = (value || "").trim();
        if (v === "") return false;

        const pattern = /^(https?:\/\/)?([\w-]+\.)+[\w-]{2,}(\/[^\s]*)?$/i;
        return pattern.test(v);
    }

    function isValidDisplayedRating(value) {
        const v = (value || "").trim();
        if (v === "") return false;

        const match = v.match(/^(\d+(?:[.,]\d{1,2})?)\/(5|10|100)$/);
        if (!match) return false;

        const note = parseFloat(match[1].replace(",", "."));
        const max = parseInt(match[2], 10);

        if (Number.isNaN(note) || Number.isNaN(max)) return false;
        if (note < 0) return false;
        if (note > max) return false;

        return true;
    }

    function normalizePostalCountry(countryValue) {
        const value = (countryValue || "").trim().toUpperCase();

        if (["FR", "FRANCE", "FRANCE MÉTROPOLITAINE", "FRANCE METROPOLITAINE"].includes(value)) {
            return "FR";
        }

        if (["BE", "BELGIQUE", "BELGIUM"].includes(value)) {
            return "BE";
        }

        if (["CH", "SUISSE", "SWITZERLAND"].includes(value)) {
            return "CH";
        }

        if (["ES", "ESPAGNE", "SPAIN"].includes(value)) {
            return "ES";
        }

        return value;
    }

    function isValidPostalCodeByCountry(postalCode, countryCode) {
        const code = (postalCode || "").trim();
        const country = normalizePostalCountry(countryCode);

        if (code === "") return false;
        if (country === "") return true;

        switch (country) {
            case "FR":
                return /^\d{5}$/.test(code);
            case "BE":
                return /^\d{4}$/.test(code);
            case "CH":
                return /^\d{4}$/.test(code);
            case "ES":
                return /^\d{5}$/.test(code);
            default:
                return true;
        }
    }

    function validateAddressPostalCode(stepContent) {
        const zip = zipField || stepContent.querySelector('#ff_6_address_1_zip_');
        const country = countryField || stepContent.querySelector('#ff_6_address_1_country_');

        if (!zip || !country || !isFieldEligible(zip) || !isFieldEligible(country)) {
            return { isValid: true, firstInvalidField: null };
        }

        const zipValue = (zip.value || "").trim();
        const countryValue = normalizePostalCountry(country.value || "");

        console.log("ZIP =", zip.value);
        console.log("COUNTRY RAW =", country.value);
        console.log("COUNTRY NORMALIZED =", normalizePostalCountry(country.value));

        if (zipValue === "" || countryValue === "") {
            return { isValid: true, firstInvalidField: null };
        }

        if (!isValidPostalCodeByCountry(zipValue, countryValue)) {
            let message = "Veuillez entrer un code postal valide pour le pays sélectionné.";

            switch (countryValue) {
                case "FR":
                    message = "Veuillez entrer un code postal français valide (5 chiffres).";
                    break;
                case "BE":
                    message = "Veuillez entrer un code postal belge valide (4 chiffres).";
                    break;
                case "CH":
                    message = "Veuillez entrer un code postal suisse valide (4 chiffres).";
                    break;
                case "ES":
                    message = "Veuillez entrer un code postal espagnol valide (5 chiffres).";
                    break;
            }

            addFieldError(zip, message);
            return { isValid: false, firstInvalidField: zip };
        }

        return { isValid: true, firstInvalidField: null };
    }

    function validateLiensAchatLivre(stepContent) {
        const repeater = stepContent.querySelector('.book-links.ff-el-repeater');
        if (!repeater || !isVisible(repeater) || isExcluded(repeater)) {
            return { isValid: true, firstInvalidField: null };
        }

        const rows = Array.from(repeater.querySelectorAll("tbody tr"));
        if (!rows.length) {
            return { isValid: false, firstInvalidField: repeater };
        }

        let isValid = true;
        let firstInvalidField = null;

        rows.forEach((row, index) => {
            const textInputs = Array.from(
                row.querySelectorAll('input[type="text"], input[type="url"], textarea')
            ).filter(isFieldEligible);

            if (textInputs.length < 2) return;

            const plateformeField = textInputs[0];
            const lienField = textInputs[1];

            const plateformeValue = (plateformeField.value || "").trim();
            const lienValue = (lienField.value || "").trim();

            const rowIsEmpty = plateformeValue === "" && lienValue === "";
            const rowIsComplete = plateformeValue !== "" && lienValue !== "";

            if (index === 0) {
                if (plateformeValue === "") {
                    addFieldError(plateformeField, "Veuillez renseigner la plateforme.");
                    if (!firstInvalidField) firstInvalidField = plateformeField;
                    isValid = false;
                }

                if (lienValue === "") {
                    addFieldError(lienField, "Veuillez renseigner le lien.");
                    if (!firstInvalidField) firstInvalidField = lienField;
                    isValid = false;
                } else if (!isValidUrlLike(lienValue)) {
                    addLinkFieldError(lienField, "Veuillez entrer un lien valide (ex : https://amazon.fr).");
                    if (!firstInvalidField) firstInvalidField = lienField;
                    isValid = false;
                }

                return;
            }

            if (rowIsEmpty) {
                return;
            }

            if (!rowIsComplete) {
                if (plateformeValue === "") {
                    addFieldError(plateformeField, "Veuillez renseigner la plateforme.");
                    if (!firstInvalidField) firstInvalidField = plateformeField;
                    isValid = false;
                }

                if (lienValue === "") {
                    addFieldError(lienField, "Veuillez renseigner le lien.");
                    if (!firstInvalidField) firstInvalidField = lienField;
                    isValid = false;
                }

                return;
            }

            if (!isValidUrlLike(lienValue)) {
                addLinkFieldError(lienField, "Veuillez entrer un lien valide (ex : https://amazon.fr).");
                if (!firstInvalidField) firstInvalidField = lienField;
                isValid = false;
            }
        });

        return { isValid, firstInvalidField };
    }

    function validateCouvertureLivre(stepContent) {
        const fileField = stepContent.querySelector('input[type="file"][name="file-upload_2"]');
        if (!fileField || !isFieldEligible(fileField)) {
            return { isValid: true, firstInvalidField: null };
        }

        const hasValue = getFieldValue(fileField) !== "";
        const hasPreview = hasFluentFilePreview(fileField);

        if (!hasValue && !hasPreview) {
            addFieldError(fileField, "Veuillez ajouter la couverture du livre.");
            return { isValid: false, firstInvalidField: fileField };
        }

        return { isValid: true, firstInvalidField: null };
    }

    function validateAutresRomans(stepContent) {
        const repeater = stepContent.querySelector('.other-book-content .ff-el-repeater');
        if (!repeater || !isVisible(repeater) || isExcluded(repeater)) {
            return { isValid: true, firstInvalidField: null };
        }

        const rows = Array.from(repeater.querySelectorAll("tbody tr"));
        if (!rows.length) {
            return { isValid: false, firstInvalidField: repeater };
        }

        let isValid = true;
        let firstInvalidField = null;

        rows.forEach((row, index) => {
            const fields = Array.from(row.querySelectorAll("input, select, textarea")).filter(isFieldEligible);
            if (!fields.length) return;

            const textInputs = Array.from(
                row.querySelectorAll('input[type="text"], input[type="url"], textarea')
            ).filter(isFieldEligible);

            const selects = Array.from(row.querySelectorAll("select")).filter(isFieldEligible);

            const titreField = textInputs[0] || null;
            const accrocheField = textInputs[1] || null;
            const lienField = textInputs[2] || null;
            const formatField = selects[0] || null;

            if (!titreField || !accrocheField || !lienField || !formatField) return;

            const titreValue = (titreField.value || "").trim();
            const accrocheValue = (accrocheField.value || "").trim();
            const lienValue = (lienField.value || "").trim();
            const formatValue = (formatField.value || "").trim();

            const rowHasAnyValue =
                titreValue !== "" ||
                accrocheValue !== "" ||
                lienValue !== "" ||
                formatValue !== "";

            const rowIsComplete =
                titreValue !== "" &&
                accrocheValue !== "" &&
                lienValue !== "" &&
                formatValue !== "";

            if (index === 0) {
                if (titreValue === "") {
                    addFieldError(titreField, "Veuillez renseigner le titre du roman.");
                    if (!firstInvalidField) firstInvalidField = titreField;
                    isValid = false;
                }

                if (accrocheValue === "") {
                    addFieldError(accrocheField, "Veuillez renseigner l’accroche.");
                    if (!firstInvalidField) firstInvalidField = accrocheField;
                    isValid = false;
                }

                if (lienValue === "") {
                    addFieldError(lienField, "Veuillez renseigner le lien du livre.");
                    if (!firstInvalidField) firstInvalidField = lienField;
                    isValid = false;
                } else if (!isValidUrlLike(lienValue)) {
                    addLinkFieldError(lienField, "Veuillez entrer un lien valide (ex : https://amazon.fr).");
                    if (!firstInvalidField) firstInvalidField = lienField;
                    isValid = false;
                }

                if (formatValue === "") {
                    addFieldError(formatField, "Veuillez sélectionner un format.");
                    if (!firstInvalidField) firstInvalidField = formatField;
                    isValid = false;
                }

                return;
            }

            if (!rowHasAnyValue) return;

            if (!rowIsComplete) {
                if (titreValue === "") {
                    addFieldError(titreField, "Veuillez renseigner le titre du roman.");
                    if (!firstInvalidField) firstInvalidField = titreField;
                    isValid = false;
                }

                if (accrocheValue === "") {
                    addFieldError(accrocheField, "Veuillez renseigner l’accroche.");
                    if (!firstInvalidField) firstInvalidField = accrocheField;
                    isValid = false;
                }

                if (lienValue === "") {
                    addFieldError(lienField, "Veuillez renseigner le lien du livre.");
                    if (!firstInvalidField) firstInvalidField = lienField;
                    isValid = false;
                } else if (!isValidUrlLike(lienValue)) {
                    addLinkFieldError(lienField, "Veuillez entrer un lien valide (ex : https://amazon.fr).");
                    if (!firstInvalidField) firstInvalidField = lienField;
                    isValid = false;
                }

                if (formatValue === "") {
                    addFieldError(formatField, "Veuillez sélectionner un format.");
                    if (!firstInvalidField) firstInvalidField = formatField;
                    isValid = false;
                }
            }
        });

        return { isValid, firstInvalidField };
    }

    function validateUploadAutresRomans(stepContent) {
        const fileField =
            stepContent.querySelector('input[type="file"][name="file_upload_6"]') ||
            stepContent.querySelector('input[type="file"][data-name="file_upload_6"]') ||
            stepContent.querySelector('input[type="file"][id*="file_upload_6"]') ||
            stepContent.querySelector('input[type="file"]');

        if (!fileField) {
            return { isValid: false, firstInvalidField: null };
        }

        const hasValue = getFieldValue(fileField) !== "";
        const hasPreview = hasFluentFilePreview(fileField);

        if (!hasValue && !hasPreview) {
            addFieldError(fileField, "Veuillez uploader au moins une couverture.");
            return { isValid: false, firstInvalidField: fileField };
        }

        return { isValid: true, firstInvalidField: fileField };
    }

    function validateAuteursRessemblants(stepContent) {
        const repeater = stepContent.querySelector('[data-name="repeater_field_5"].ff-el-repeater');
        if (!repeater || !isVisible(repeater) || isExcluded(repeater)) {
            return { isValid: true, firstInvalidField: null };
        }

        const rows = Array.from(repeater.querySelectorAll("tbody tr"));
        if (!rows.length) {
            return { isValid: false, firstInvalidField: repeater };
        }

        let isValid = true;
        let firstInvalidField = null;

        rows.forEach((row, index) => {
            const textInputs = Array.from(
                row.querySelectorAll('input[type="text"], textarea')
            ).filter(isFieldEligible);

            if (!textInputs.length) return;

            const auteurField = textInputs[0];
            const auteurValue = (auteurField.value || "").trim();

            if (index === 0) {
                if (auteurValue === "") {
                    addFieldError(auteurField, "Veuillez renseigner au moins un auteur.");
                    if (!firstInvalidField) firstInvalidField = auteurField;
                    isValid = false;
                }
            }
        });

        return { isValid, firstInvalidField };
    }

    function validateImagePrincipaleHero(stepContent) {
        const fileField = stepContent.querySelector('input[type="file"][name="file-upload_3"]');
        if (!fileField || !isFieldEligible(fileField)) {
            return { isValid: true, firstInvalidField: null };
        }

        const hasValue = getFieldValue(fileField) !== "";
        const hasPreview = hasFluentFilePreview(fileField);

        if (!hasValue && !hasPreview) {
            addFieldError(fileField, "Veuillez ajouter l’image principale.");
            return { isValid: false, firstInvalidField: fileField };
        }

        return { isValid: true, firstInvalidField: null };
    }

    function validatePhotoAuteur(stepContent) {
        const fileField = stepContent.querySelector('input[type="file"][name="image-upload_2"]');
        if (!fileField || !isFieldEligible(fileField)) {
            return { isValid: true, firstInvalidField: null };
        }

        const hasValue = getFieldValue(fileField) !== "";
        const hasPreview = hasFluentFilePreview(fileField);

        if (!hasValue && !hasPreview) {
            addFieldError(fileField, "Veuillez ajouter la photo de l’auteur.");
            return { isValid: false, firstInvalidField: fileField };
        }

        return { isValid: true, firstInvalidField: null };
    }

    function validatePdfBonus(stepContent) {
        const fileField = stepContent.querySelector('input[type="file"][name="file-upload"]');
        if (!fileField || !isFieldEligible(fileField)) {
            return { isValid: true, firstInvalidField: null };
        }

        const hasValue = getFieldValue(fileField) !== "";
        const hasPreview = hasFluentFilePreview(fileField);

        if (!hasValue && !hasPreview) {
            addFieldError(fileField, "Veuillez ajouter le fichier bonus (PDF).");
            return { isValid: false, firstInvalidField: fileField };
        }

        return { isValid: true, firstInvalidField: null };
    }

    function validateCurrentStep(stepIndex) {
        console.log("validateCurrentStep appelée, étape =", stepIndex);
        clearStepErrors(stepIndex);

        const stepContent = getCurrentStepContainer(stepIndex);
        if (!stepContent) return true;

        const standardValidation = validateStandardFields(stepContent);
        const repeaterValidation = validateRepeater(stepContent);

        let customValidation = { isValid: true, firstInvalidField: null };

        const currentStepDef = steps[stepIndex];
        console.log("currentStepDef =", currentStepDef);

        if (currentStepDef && currentStepDef.key === "general") {
            const formatsValidation = validateFormatsDisponibles(stepContent);
            const liensValidation = validateLiensAchatLivre(stepContent);
            const couvertureValidation = validateCouvertureLivre(stepContent);

            customValidation = {
                isValid:
                    formatsValidation.isValid &&
                    liensValidation.isValid &&
                    couvertureValidation.isValid,
                firstInvalidField:
                    formatsValidation.firstInvalidField ||
                    liensValidation.firstInvalidField ||
                    couvertureValidation.firstInvalidField
            };
        }

        if (currentStepDef && currentStepDef.key === "hero") {
            const auteursValidation = validateAuteursRessemblants(stepContent);
            const imageValidation = validateImagePrincipaleHero(stepContent);
            const displayedRatingValidation = validateDisplayedRating(stepContent);
            const heroMainButtonLinkValidation = validateHeroMainButtonLink(stepContent);

            customValidation = {
                isValid:
                    auteursValidation.isValid &&
                    imageValidation.isValid &&
                    displayedRatingValidation.isValid &&
                    heroMainButtonLinkValidation.isValid,
                firstInvalidField:
                    auteursValidation.firstInvalidField ||
                    imageValidation.firstInvalidField ||
                    displayedRatingValidation.firstInvalidField ||
                    heroMainButtonLinkValidation.firstInvalidField
            };
        }

        if (currentStepDef && currentStepDef.key === "reviews") {
            const reviewRatingValidation = validateReviewRatings(stepContent);

            customValidation = {
                isValid: reviewRatingValidation.isValid,
                firstInvalidField: reviewRatingValidation.firstInvalidField
            };
        }

        if (currentStepDef && currentStepDef.key === "author") {
            const photoAuteurValidation = validatePhotoAuteur(stepContent);

            customValidation = {
                isValid: photoAuteurValidation.isValid,
                firstInvalidField: photoAuteurValidation.firstInvalidField
            };
        }

        if (currentStepDef && currentStepDef.key === "emails") {
            const pdfValidation = validatePdfBonus(stepContent);

            customValidation = {
                isValid: pdfValidation.isValid,
                firstInvalidField: pdfValidation.firstInvalidField
            };
        }

        if (currentStepDef && currentStepDef.key === "other-books") {
            const autresRomansValidation = validateAutresRomans(stepContent);
            const uploadAutresRomansValidation = validateUploadAutresRomans(stepContent);

            customValidation = {
                isValid:
                    autresRomansValidation.isValid &&
                    uploadAutresRomansValidation.isValid,
                firstInvalidField:
                    autresRomansValidation.firstInvalidField ||
                    uploadAutresRomansValidation.firstInvalidField
            };
        }

        if (currentStepDef && currentStepDef.key === "recap") {
            const blocsIconesValidation = validateBlocsIconesRecap(stepContent);

            customValidation = {
                isValid: blocsIconesValidation.isValid,
                firstInvalidField: blocsIconesValidation.firstInvalidField
            };
        }

        if (currentStepDef && currentStepDef.key === "technical") {
            const reseauxValidation = validateReseauxSociaux(stepContent);
            const logoValidation = validateLogo(stepContent);
            const postalCodeValidation = validateAddressPostalCode(stepContent);

            customValidation = {
                isValid:
                    reseauxValidation.isValid &&
                    logoValidation.isValid &&
                    postalCodeValidation.isValid,
                firstInvalidField:
                    reseauxValidation.firstInvalidField ||
                    logoValidation.firstInvalidField ||
                    postalCodeValidation.firstInvalidField
            };
        }

        const isStepValid =
            standardValidation.isValid &&
            repeaterValidation.isValid &&
            customValidation.isValid;

        const firstInvalidField =
            standardValidation.firstInvalidField ||
            repeaterValidation.firstInvalidField ||
            customValidation.firstInvalidField;

        if (!isStepValid && firstInvalidField) {
            firstInvalidField.scrollIntoView({
                behavior: "smooth",
                block: "center"
            });

            setTimeout(() => {
                if (typeof firstInvalidField.focus === "function") {
                    firstInvalidField.focus();
                }
            }, 200);
        }

        return isStepValid;
    }

    function clearFieldError(field) {
        const container = getFieldErrorContainer(field);

        if (field.classList) {
            field.classList.remove("ff-error-field");
        }

        if (container) {
            container.querySelectorAll(".ff-step-error").forEach(el => el.remove());
        }
    }

    function bindLiveValidation() {
        form.addEventListener("input", function (e) {
            const field = e.target;
            if (!(field instanceof HTMLElement)) return;

            if (field.classList.contains("ff-error-field")) {
                clearFieldError(field);
            }
        });

        form.addEventListener("change", function (e) {
            const field = e.target;
            if (!(field instanceof HTMLElement)) return;

            const type = (field.type || "").toLowerCase();

            if (type === "radio" && field.name) {
                form.querySelectorAll(`input[type="radio"][name="${CSS.escape(field.name)}"]`).forEach(clearFieldError);
                return;
            }

            if (type === "checkbox" && field.name) {
                form.querySelectorAll(`input[type="checkbox"][name="${CSS.escape(field.name)}"]`).forEach(clearFieldError);
                return;
            }

            clearFieldError(field);
        });

        form.addEventListener("change", function (e) {
            const target = e.target;
            if (!(target instanceof HTMLInputElement)) return;
            if ((target.type || "").toLowerCase() !== "file") return;

            setTimeout(() => {
                clearFieldError(target);
                updateFieldAsterisk(target);
            }, 150);
        });
    }

    function bindFormPersistence() {
        document.addEventListener("input", function (e) {
            const target = e.target;
            if (!(target instanceof HTMLElement)) return;
            if (!target.closest("#fluentform_6")) return;
            if (isInitializingForm) return;
            if (!e.isTrusted) return;

            saveFormData();
            debounceServerSave();
        });

        document.addEventListener("change", function (e) {
            const target = e.target;
            if (!(target instanceof HTMLElement)) return;
            if (!target.closest("#fluentform_6")) return;

            updateRequiredAsterisks();

            if (isInitializingForm) return;
            if (!e.isTrusted) return;

            saveFormData();
            debounceServerSave();
        });
    }

    function beforeFinalSubmit() {
        cleanupHiddenRequiredFieldsBeforeSubmit();
        enableAllFieldsBeforeSubmit();

        form.querySelectorAll("input, textarea, select, button").forEach(field => {
            const type = (field.type || "").toLowerCase();
            const isExcludedField = field.closest(".ff_excluded");

            if (!isExcludedField) return;
            if (submitButton && field === submitButton) return;
            if (type === "hidden") return;

            if (type === "file") {
                field.required = false;
                field.removeAttribute("aria-required");
                field.disabled = false;
                updateFieldAsterisk(field);
                return;
            }

            field.disabled = true;
            field.required = false;
            field.removeAttribute("aria-required");
            updateFieldAsterisk(field);
        });

        form.querySelectorAll('input[type="file"]').forEach(field => {
            field.disabled = false;
            updateFieldAsterisk(field);
        });

        if (submitButton) {
            submitButton.disabled = false;
        }

        updateRequiredAsterisks();
    }

    function bindSubmitPersistence() {
        if (!submitButton) return;

        submitButton.addEventListener("click", function (e) {
            if (isUploadInProgress()) {
                e.preventDefault();
                alert("Veuillez attendre la fin du téléversement avant d’envoyer le formulaire.");
                return;
            }

            beforeFinalSubmit();
            cleanupHiddenRequiredFieldsBeforeSubmit();

            const isValid = validateCurrentStep(currentStep);
            if (!isValid) {
                e.preventDefault();
                return;
            }

            cleanupHiddenRequiredFieldsBeforeSubmit();

            if (!form.checkValidity()) {
                e.preventDefault();
                form.reportValidity();
                return;
            }
        });

        form.addEventListener("submit", function () {
            // Ne pas vider ici
        });

        function handleSuccessfulSubmission() {
            setTimeout(async () => {
                await clearAllFormStateAfterSubmit();
            }, 300);
        }

        const successObserver = new MutationObserver(function () {
            if (form.classList.contains("ff_force_hide")) {
                handleSuccessfulSubmission();
            }
        });

        successObserver.observe(form, {
            attributes: true,
            attributeFilter: ["class"]
        });
    }

    function bindConditionalSteps() {
        form.addEventListener("change", function (e) {
            const target = e.target;
            if (!(target instanceof HTMLInputElement)) return;

            if (target.name === "input_radio_2") {
                const oldLength = steps.length;

                refreshSteps();
                createProgressUI();

                if (steps.length !== oldLength && currentStep >= steps.length) {
                    currentStep = steps.length - 1;
                }

                if (currentStep > maxVisitedStep) {
                    maxVisitedStep = currentStep;
                }

                syncCustomRequiredFields();
                showStep(currentStep, false);

            } else {
                updateProgressUI(currentStep);
                syncCustomRequiredFields();
            }
        });
    }

    function restoreServerDraftFields() {
        if (!serverDraft || !serverDraft.fields) return;

        const data = serverDraft.fields;
        const fields = Array.from(form.querySelectorAll("input, textarea, select"));

        fields.forEach(field => {
            const key = getFieldStorageKey(field);
            if (!key || !(key in data)) return;

            const type = (field.type || "").toLowerCase();
            const value = data[key];

            if (type === "file" || type === "password" || type === "submit" || type === "button") return;

            if (type === "radio") {
                field.checked = field.value === value;
                return;
            }

            if (type === "checkbox") {
                field.checked = Array.isArray(value) && value.includes(field.value);
                return;
            }

            field.value = value;
        });

        setTimeout(() => {
            form.dispatchEvent(new Event("change", { bubbles: true }));
            form.dispatchEvent(new Event("input", { bubbles: true }));
            syncCustomRequiredFields();
        }, 50);
    }

    async function init() {
        refreshSteps();
        createProgressUI();

        restoreFormData();
        initUploadSlots();

        await fetchServerDraft();
        restoreServerDraftFields();
        restoreServerDraftUploads();

        setTimeout(() => {
            form.querySelectorAll('input[type="radio"], input[type="checkbox"], select').forEach(field => {
                field.dispatchEvent(new Event("change", { bubbles: true }));
            });
            syncCustomRequiredFields();
        }, 100);

        refreshSteps();

        bindLiveValidation();
        bindFormPersistence();
        bindSubmitPersistence();
        bindConditionalSteps();
        bindCustomUploads();
        bindAddressAutocomplete();

        currentStep = getSavedStep();
        refreshSteps();

        if (steps.length > 0 && currentStep >= steps.length) {
            currentStep = steps.length - 1;
        }

        maxVisitedStep = currentStep;
        syncCustomRequiredFields();
        showStep(currentStep);

        setTimeout(() => {
            isInitializingForm = false;
        }, 300);
    }

    init();
});

document.addEventListener("DOMContentLoaded", function () {
    function initSiteAuteurRegisterPasswordUI() {
        const registerForm = document.querySelector(".um-register form, form.um-form");
        if (!registerForm) return false;

        if (registerForm.dataset.saPasswordUiReady === "1") {
            return true;
        }

        const passwordInput =
            registerForm.querySelector('input[name="user_password"]') ||
            registerForm.querySelector('input[name^="user_password-"]') ||
            registerForm.querySelector('.um-field[data-key="user_password"] input') ||
            registerForm.querySelector('.um-field-password input');

        const confirmInput =
            registerForm.querySelector('input[name="confirm_user_password"]') ||
            registerForm.querySelector('input[name^="confirm_user_password-"]') ||
            registerForm.querySelector('.um-field[data-key="confirm_user_password"] input') ||
            registerForm.querySelector('.um-field-password[data-key="confirm_user_password"] input') ||
            registerForm.querySelector('#confirm_user_password-1074');

            const strengthText = registerForm.querySelector("#sa-password-strength-text");
            const strengthBars = registerForm.querySelectorAll(".sa-strength-bars span");
            const ruleItems = registerForm.querySelectorAll("#sa-password-rules li");
            const rulesBox = registerForm.querySelector("#sa-password-rules");
            const strengthBox = registerForm.querySelector("#sa-password-strength");

        if (
            !passwordInput ||
            !confirmInput ||
            !strengthText ||
            !strengthBars.length ||
            !ruleItems.length ||
            !rulesBox ||
            !strengthBox
        ) {
            return false;
        }

        const eyeOpen = `
            <svg class="sa-eye-open" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M3 12C4.8 8.6 8.05 6.5 12 6.5C15.95 6.5 19.2 8.6 21 12C19.2 15.4 15.95 17.5 12 17.5C8.05 17.5 4.8 15.4 3 12Z" stroke="currentColor" stroke-width="1.8"/>
                <circle cx="12" cy="12" r="2.7" stroke="currentColor" stroke-width="1.8"/>
            </svg>
        `;

        const eyeClosed = `
            <svg class="sa-eye-closed" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M3 12C4.8 8.6 8.05 6.5 12 6.5C15.95 6.5 19.2 8.6 21 12C19.2 15.4 15.95 17.5 12 17.5C8.05 17.5 4.8 15.4 3 12Z" stroke="currentColor" stroke-width="1.8"/>
                <circle cx="12" cy="12" r="2.7" stroke="currentColor" stroke-width="1.8"/>
                <path d="M4 4L20 20" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            </svg>
        `;

        function enhancePasswordField(input) {
            if (!input) return;

            const fieldArea = input.closest(".um-field-area-password, .um-field-area");
            if (!fieldArea) return;

            fieldArea.classList.add("sa-password-field-area");

            if (fieldArea.querySelector(".sa-password-toggle")) return;

            const button = document.createElement("button");
            button.type = "button";
            button.className = "sa-password-toggle";
            button.setAttribute("aria-label", "Afficher ou masquer le mot de passe");
            button.innerHTML = eyeOpen + eyeClosed;

            button.addEventListener("click", function () {
                const isPassword = input.type === "password";
                input.type = isPassword ? "text" : "password";
                button.classList.toggle("is-visible", isPassword);
            });

            fieldArea.appendChild(button);
        }

        function getRules(password) {
            return {
                length: password.length >= 8,
                uppercase: /[A-ZÀ-ÖØ-Þ]/.test(password),
                digit: /\d/.test(password),
                special: /[^A-Za-z0-9À-ÖØ-öø-ÿ]/.test(password)
            };
        }

        function getStrengthLevel(password, rules) {
            if (!password) return 0;

            let validCount = 0;

            if (rules.length) validCount++;
            if (rules.uppercase) validCount++;
            if (rules.digit) validCount++;
            if (rules.special) validCount++;

            if (validCount <= 1) return 1;
            if (validCount === 2) return 2;
            if (validCount === 3) return 3;
            return 4;
        }

        function getStrengthLabel(level) {
            if (level <= 1) return "Très faible";
            if (level === 2) return "Faible";
            if (level === 3) return "Moyenne";
            return "Forte";
        }

        function updateRulesUI(rules) {
            ruleItems.forEach(function (item) {
                const key = item.getAttribute("data-rule");
                item.classList.toggle("is-valid", !!rules[key]);
            });
        }

        function updateStrengthUI(level) {
            strengthText.textContent = getStrengthLabel(level);
            strengthText.className = "is-level-" + level;

            strengthBars.forEach(function (bar, index) {
                bar.classList.remove("is-active", "is-level-1", "is-level-2", "is-level-3", "is-level-4");

                if (index < level) {
                    bar.classList.add("is-active", "is-level-" + level);
                }
            });

            rulesBox.classList.remove("is-level-0", "is-level-1", "is-level-2", "is-level-3", "is-level-4");
            rulesBox.classList.add("is-level-" + level);

            strengthBox.classList.remove("is-level-0", "is-level-1", "is-level-2", "is-level-3", "is-level-4");
            strengthBox.classList.add("is-level-" + level);
        }

        function validatePasswordField() {
            const password = passwordInput.value || "";
            const rules = getRules(password);

            const valid =
                rules.length &&
                rules.uppercase &&
                rules.digit &&
                rules.special;

            passwordInput.setCustomValidity(
                valid || password === ""
                    ? ""
                    : "Ton mot de passe doit respecter toutes les règles de sécurité indiquées."
            );

            return valid;
        }

        function validateConfirmField() {
            const password = passwordInput.value || "";
            const confirm = confirmInput.value || "";

            if (confirm === "") {
                confirmInput.setCustomValidity("");
                return false;
            }

            const valid = password === confirm;

            confirmInput.setCustomValidity(
                valid ? "" : "Les mots de passe ne correspondent pas."
            );

            return valid;
        }

        function updateAll() {
            const password = passwordInput.value || "";
            const rules = getRules(password);
            const level = getStrengthLevel(password, rules);

            updateRulesUI(rules);
            updateStrengthUI(level);
            validatePasswordField();
            validateConfirmField();
        }

        enhancePasswordField(passwordInput);
        enhancePasswordField(confirmInput);

        passwordInput.setAttribute("placeholder", "Ex : Password123!");
        confirmInput.setAttribute("placeholder", "Ex : Password123!");

        passwordInput.addEventListener("input", updateAll);
        passwordInput.addEventListener("keyup", updateAll);
        passwordInput.addEventListener("change", updateAll);
        passwordInput.addEventListener("paste", function () {
            setTimeout(updateAll, 0);
        });

        confirmInput.addEventListener("input", function () {
            validateConfirmField();
        });

        confirmInput.addEventListener("keyup", function () {
            validateConfirmField();
        });

        [passwordInput, confirmInput].forEach(function (input) {
            input.addEventListener("invalid", function () {
                if (!input.value) {
                    input.setCustomValidity("Veuillez compléter ce champ.");
                }
            });

            input.addEventListener("input", function () {
                if (!input.value) {
                    input.setCustomValidity("");
                }
            });
        });

        registerForm.addEventListener("submit", function (e) {
            const passwordValid = validatePasswordField();
            const confirmValid = validateConfirmField();

            if (!passwordInput.value) {
                passwordInput.setCustomValidity("Veuillez compléter ce champ.");
                passwordInput.reportValidity();
                e.preventDefault();
                return;
            }

            if (!confirmInput.value) {
                confirmInput.setCustomValidity("Veuillez compléter ce champ.");
                confirmInput.reportValidity();
                e.preventDefault();
                return;
            }

            if (!passwordValid) {
                passwordInput.reportValidity();
                e.preventDefault();
                return;
            }

            if (!confirmValid) {
                confirmInput.reportValidity();
                e.preventDefault();
                return;
            }
        });

        registerForm.dataset.saPasswordUiReady = "1";
        updateAll();

        return true;
    }

    let tries = 0;
    const maxTries = 40;

    const interval = setInterval(function () {
        tries++;

        const ok = initSiteAuteurRegisterPasswordUI();
        if (ok || tries >= maxTries) {
            clearInterval(interval);
        }
    }, 250);

    const observer = new MutationObserver(function () {
        initSiteAuteurRegisterPasswordUI();
    });

    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
});
