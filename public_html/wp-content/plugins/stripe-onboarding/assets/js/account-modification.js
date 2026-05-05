document.addEventListener("DOMContentLoaded", function () {
    if (typeof SA_MODIFICATION === "undefined") return;

    let initialized = false;

    function bootModificationForm() {
        if (initialized) return true;

        const form =
            document.querySelector('form[data-form_id="' + SA_MODIFICATION.formId + '"]') ||
            document.querySelector("#fluentform_" + SA_MODIFICATION.formId);

        if (!form) return false;

        initialized = true;

        const originalData = SA_MODIFICATION.originalData || {};
        form.classList.add("sa-single-edit-mode");
        form.setAttribute("novalidate", "novalidate");

        addHiddenInput(form, "sa_form_mode", "modification_request");
        addHiddenInput(form, "sa_order_id", String(SA_MODIFICATION.orderId || 0));
        addHiddenInput(form, "sa_selected_edit_field", "");
        addHiddenInput(form, "sa_selected_edit_group", "");
        addHiddenInput(form, "sa_modification_confirmed", "0");

        const selectedFieldInput = form.querySelector('input[name="sa_selected_edit_field"]');
        const selectedGroupInput = form.querySelector('input[name="sa_selected_edit_group"]');
        const confirmedInput = form.querySelector('input[name="sa_modification_confirmed"]');
        const topResetBtn = document.querySelector("#sa-reset-edit-selection");
        let syncScheduled = false;

        function addHiddenInput(formEl, name, value) {
            let input = formEl.querySelector('input[name="' + name + '"]');
            if (!input) {
                input = document.createElement("input");
                input.type = "hidden";
                input.name = name;
                formEl.appendChild(input);
            }
            input.value = value;
        }

        function getFieldWrappers() {
            return Array.from(form.querySelectorAll(".ff-el-group"));
        }

        function getControlsByName(fieldName) {
            const escaped = (window.CSS && CSS.escape) ? CSS.escape(fieldName) : fieldName.replace(/"/g, '\\"');

            let controls = [];

            try {
                controls = Array.from(form.querySelectorAll('[name="' + escaped + '"]'));
            } catch (e) {}

            if (!controls.length) {
                try {
                    controls = Array.from(form.querySelectorAll('[data-name="' + escaped + '"]'));
                } catch (e) {}
            }

            if (!controls.length && fieldName.endsWith("[]")) {
                const baseName = fieldName.slice(0, -2);

                try {
                    controls = Array.from(form.querySelectorAll('[name="' + baseName.replace(/"/g, '\\"') + '[]"]'));
                } catch (e) {}

                if (!controls.length) {
                    try {
                        controls = Array.from(form.querySelectorAll('[data-name="' + baseName.replace(/"/g, '\\"') + '"]'));
                    } catch (e) {}
                }
            }

            return controls;
        }

        function getPrimaryControl(wrapper) {
            return wrapper.querySelector(
                "input:not([type='hidden']):not([type='submit']), textarea, select"
            );
        }

        function isNestedInsideRepeater(wrapper) {
            const repeaterRoot = wrapper.closest(".ff-el-repeater");
            if (!repeaterRoot) return false;
            return wrapper !== repeaterRoot;
        }

        function getFieldName(wrapper) {
            const control = getPrimaryControl(wrapper);
            if (!control) return "";

            const dataName = control.getAttribute("data-name");
            if (dataName) return dataName;

            const name = control.getAttribute("name") || "";
            if (!name) return "";

            const bracketMatch = name.match(/^([^\[]+)\[[^\]]+\]$/);
            if (bracketMatch) {
                return bracketMatch[1];
            }

            return name;
        }

        function getGroupFromFieldName(fieldName) {
        if (!fieldName) return "";

        const bracketMatch = fieldName.match(/^([^\[]+)\[[^\]]+\]$/);
        if (bracketMatch) {
            fieldName = bracketMatch[1];
        }

        const groups = {
            names: "contact_details",
            names_1: "contact_details",
            email: "contact_details",
            "file-upload_2": "book_cover"
        };

        return groups[fieldName] || fieldName;
        }

        function normalizeValue(value) {
            if (Array.isArray(value)) return value.map(String);
            if (value === null || value === undefined) return "";
            return String(value);
        }

        function extractFileUrl(value) {
            if (!value) return "";

            if (typeof value === "string") {
                const trimmed = value.trim();
                if (!trimmed) return "";

                if (/^https?:\/\//i.test(trimmed)) {
                    return trimmed;
                }

                try {
                    const parsed = JSON.parse(trimmed);
                    return extractFileUrl(parsed);
                } catch (e) {
                    return trimmed;
                }
            }

            if (Array.isArray(value)) {
                for (const item of value) {
                    const found = extractFileUrl(item);
                    if (found) return found;
                }
                return "";
            }

            if (typeof value === "object") {
                const possibleKeys = ["url", "file", "src", "value", "full_url"];
                for (const key of possibleKeys) {
                    if (value[key]) {
                        const found = extractFileUrl(value[key]);
                        if (found) return found;
                    }
                }
            }

            return "";
        }

        function rememberRequiredState(control) {
            if (!control) return;

            if (!control.hasAttribute("data-sa-required-initialised")) {
                control.setAttribute("data-sa-required-initialised", "1");
                control.setAttribute("data-sa-was-required", control.required ? "1" : "0");
            }
        }

        function rememberNameState(control) {
            if (!control) return;

            if (!control.hasAttribute("data-sa-original-name")) {
                control.setAttribute("data-sa-original-name", control.getAttribute("name") || "");
            }
        }

        function disableBrowserValidationForLockedControl(control) {
            if (!control) return;

            rememberRequiredState(control);
            rememberNameState(control);

            control.required = false;
            control.removeAttribute("required");
            control.setAttribute("aria-required", "false");

            control.disabled = true;
            control.setAttribute("data-sa-locked", "1");
            control.setAttribute("tabindex", "-1");

            // Très important pour Fluent Forms :
            // retirer le name des champs verrouillés pour qu'ils ne passent plus
            // dans la validation front.
            const type = (control.type || "").toLowerCase();
            if (type !== "hidden" && type !== "submit") {
                const originalName = control.getAttribute("data-sa-original-name") || control.getAttribute("name") || "";
                if (originalName) {
                    control.setAttribute("data-sa-original-name", originalName);
                }
                control.removeAttribute("name");
            }

            control.classList.remove("ff-error-field");
            control.setAttribute("aria-invalid", "false");
        }

        function enableBrowserValidationForUnlockedControl(control) {
            if (!control) return;

            rememberRequiredState(control);
            rememberNameState(control);

            const originalName = control.getAttribute("data-sa-original-name");
            if (originalName && !control.getAttribute("name")) {
                control.setAttribute("name", originalName);
            }

            control.disabled = false;
            control.removeAttribute("data-sa-locked");
            control.removeAttribute("tabindex");
            control.classList.remove("ff-error-field");
            control.setAttribute("aria-invalid", "false");

            const wasRequired = control.getAttribute("data-sa-was-required") === "1";
            if (wasRequired) {
                control.required = true;
                control.setAttribute("required", "required");
                control.setAttribute("aria-required", "true");
            } else {
                control.required = false;
                control.removeAttribute("required");
                control.setAttribute("aria-required", "false");
            }
        }

        function lockControl(control) {
            if (!control) return;

            const tag = (control.tagName || "").toLowerCase();
            const type = (control.type || "").toLowerCase();

            if (type === "hidden" || type === "submit") return;

            rememberRequiredState(control);

            if (tag === "input" && ["text", "email", "url", "number", "date", "tel"].includes(type)) {
                control.readOnly = true;
                control.required = false;
                control.removeAttribute("required");
                control.setAttribute("data-sa-locked", "1");
                control.setAttribute("tabindex", "-1");
                return;
            }

            if (tag === "textarea") {
                control.readOnly = true;
                control.required = false;
                control.removeAttribute("required");
                control.setAttribute("data-sa-locked", "1");
                control.setAttribute("tabindex", "-1");
                return;
            }

            disableBrowserValidationForLockedControl(control);
        }

        function unlockControl(control) {
            if (!control) return;

            const tag = (control.tagName || "").toLowerCase();
            const type = (control.type || "").toLowerCase();

            if (type === "hidden" || type === "submit") return;

            rememberRequiredState(control);

            if (tag === "input" && ["text", "email", "url", "number", "date", "tel"].includes(type)) {
                control.readOnly = false;
                control.removeAttribute("data-sa-locked");
                control.removeAttribute("tabindex");

                const wasRequired = control.getAttribute("data-sa-was-required") === "1";
                if (wasRequired) {
                    control.required = true;
                    control.setAttribute("required", "required");
                } else {
                    control.required = false;
                    control.removeAttribute("required");
                }

                return;
            }

            if (tag === "textarea") {
                control.readOnly = false;
                control.removeAttribute("data-sa-locked");
                control.removeAttribute("tabindex");

                const wasRequired = control.getAttribute("data-sa-was-required") === "1";
                if (wasRequired) {
                    control.required = true;
                    control.setAttribute("required", "required");
                } else {
                    control.required = false;
                    control.removeAttribute("required");
                }

                return;
            }

            enableBrowserValidationForUnlockedControl(control);
        }

        function suppressWrapperValidation(wrapper) {
            if (!wrapper) return;

            wrapper.setAttribute("data-sa-validation-suppressed", "1");

            const requiredLabels = wrapper.querySelectorAll(".ff-el-is-required");
            requiredLabels.forEach(label => {
                if (!label.hasAttribute("data-sa-was-required-class")) {
                    label.setAttribute("data-sa-was-required-class", "1");
                }
                label.classList.remove("ff-el-is-required");
            });

            const controls = wrapper.querySelectorAll("input, textarea, select");
            controls.forEach(control => {
                const type = (control.type || "").toLowerCase();
                if (type === "hidden" || type === "submit") return;

                rememberRequiredState(control);
                control.required = false;
                control.removeAttribute("required");
                control.setAttribute("aria-required", "false");

                if (type === "file") {
                    control.disabled = true;
                }
            });
        }

        function restoreWrapperValidation(wrapper) {
            if (!wrapper) return;

            wrapper.removeAttribute("data-sa-validation-suppressed");

            const labels = wrapper.querySelectorAll("[data-sa-was-required-class='1']");
            labels.forEach(label => {
                label.classList.add("ff-el-is-required");
            });

            const controls = wrapper.querySelectorAll("input, textarea, select");
            controls.forEach(control => {
                const type = (control.type || "").toLowerCase();
                if (type === "hidden" || type === "submit") return;

                const wasRequired = control.getAttribute("data-sa-was-required") === "1";

                if (wasRequired) {
                    control.required = true;
                    control.setAttribute("required", "required");
                    control.setAttribute("aria-required", "true");
                } else {
                    control.required = false;
                    control.removeAttribute("required");
                    control.setAttribute("aria-required", "false");
                }

                if (type === "file") {
                    control.disabled = false;
                }
            });
        }

        function clearFluentFormErrors(wrapper) {
            if (!wrapper) return;

            wrapper.classList.remove("ff-el-is-error");

            const errorFields = wrapper.querySelectorAll(".ff-error-field");
            errorFields.forEach(el => {
                el.classList.remove("ff-error-field");
                el.setAttribute("aria-invalid", "false");
            });

            const stepErrors = wrapper.querySelectorAll(".ff-step-error, .text-danger");
            stepErrors.forEach(el => el.remove());
        }

        function setFieldValue(fieldName, value, shouldTriggerEvents = false) {
            const controls = getControlsByName(fieldName);
            if (!controls.length) return;

            const first = controls[0];
            const type = (first.type || "").toLowerCase();
            const normalized = normalizeValue(value);

            if (type === "file") return;

            function normalizeToArray(val) {
                if (Array.isArray(val)) {
                    return val.map(v => String(v).trim()).filter(Boolean);
                }

                if (val === null || val === undefined) {
                    return [];
                }

                if (typeof val === "string") {
                    const trimmed = val.trim();
                    if (!trimmed) return [];

                    try {
                        const parsed = JSON.parse(trimmed);
                        if (Array.isArray(parsed)) {
                            return parsed.map(v => String(v).trim()).filter(Boolean);
                        }
                    } catch (e) {}

                    return trimmed
                        .split(",")
                        .map(v => v.trim())
                        .filter(Boolean);
                }

                return [String(val).trim()].filter(Boolean);
            }

            if (type === "radio") {
                const targetValue = Array.isArray(normalized) ? String(normalized[0]) : String(normalized);

                controls.forEach(control => {
                    const checked = String(control.value).trim() === targetValue.trim();
                    control.checked = checked;
                    control.defaultChecked = checked;
                });

                if (shouldTriggerEvents) {
                    controls.forEach(control => {
                        control.dispatchEvent(new Event("change", { bubbles: true }));
                    });
                }
                return;
            }

            if (type === "checkbox") {
                const values = normalizeToArray(normalized);

                controls.forEach(control => {
                    const checked = values.includes(String(control.value).trim());
                    control.checked = checked;
                    control.defaultChecked = checked;
                });

                if (shouldTriggerEvents) {
                    controls.forEach(control => {
                        control.dispatchEvent(new Event("input", { bubbles: true }));
                        control.dispatchEvent(new Event("change", { bubbles: true }));
                        control.dispatchEvent(new MouseEvent("click", { bubbles: true }));
                    });
                }
                return;
            }

            if (first.tagName === "SELECT") {
                const newValue = Array.isArray(normalized) ? normalized[0] : normalized;
                if (first.value !== newValue) {
                    first.value = newValue;
                    first.defaultValue = newValue;

                    if (shouldTriggerEvents) {
                        first.dispatchEvent(new Event("change", { bubbles: true }));
                    }
                }
                return;
            }

            const finalValue = Array.isArray(normalized) ? normalized.join(", ") : normalized;

            first.value = finalValue;
            first.defaultValue = finalValue;
            first.setAttribute("value", finalValue);

            if (shouldTriggerEvents) {
                first.dispatchEvent(new Event("input", { bubbles: true }));
                first.dispatchEvent(new Event("change", { bubbles: true }));
            }
        }

        function restoreFieldOriginalValue(fieldName) {
            if (!Object.prototype.hasOwnProperty.call(originalData, fieldName)) return;

            const value = originalData[fieldName];
            if (value && typeof value === "object" && !Array.isArray(value)) {
                Object.keys(value).forEach(subKey => {
                    setFieldValue(fieldName + "[" + subKey + "]", value[subKey], false);
                });
                return;
            }

            setFieldValue(fieldName, value, false);
        }

        function prefillAllFields(triggerEvents = false) {
            Object.keys(originalData).forEach(fieldName => {
                const value = originalData[fieldName];

                if (value && typeof value === "object" && !Array.isArray(value)) {
                    Object.keys(value).forEach(subKey => {
                        setFieldValue(fieldName + "[" + subKey + "]", value[subKey], triggerEvents);
                    });
                    return;
                }

                setFieldValue(fieldName, value, triggerEvents);
            });

            const checkboxGroups = form.querySelectorAll('input[type="checkbox"][data-name]');
            const groupedNames = [...new Set(Array.from(checkboxGroups).map(el => el.getAttribute("data-name")).filter(Boolean))];

            groupedNames.forEach(groupName => {
                if (Object.prototype.hasOwnProperty.call(originalData, groupName)) {
                    setFieldValue(groupName, originalData[groupName], triggerEvents);
                }
            });
        }

        function isElementActuallyVisible(el) {
            if (!el) return false;

            let current = el;
            while (current && current !== document.body) {
                const style = window.getComputedStyle(current);
                if (
                    style.display === "none" ||
                    style.visibility === "hidden" ||
                    style.opacity === "0" ||
                    current.hidden ||
                    current.getAttribute("aria-hidden") === "true"
                ) {
                    return false;
                }
                current = current.parentElement;
            }

            return true;
        }

        function syncConditionalContainers() {
            syncScheduled = false;

            const containers = form.querySelectorAll(".other-book-section");

            containers.forEach(container => {
                const visibleInteractiveElements = Array.from(
                    container.querySelectorAll(
                        "input:not([type='hidden']):not([type='submit']), textarea, select, .ff-el-form-check, .ff_repeater_table, .ff-upload-wrap, .ff-upload"
                    )
                ).filter(el => isElementActuallyVisible(el));

                const shouldHide = visibleInteractiveElements.length === 0;
                const isHidden = container.classList.contains("sa-conditionally-hidden");

                if (shouldHide && !isHidden) {
                    container.classList.add("sa-conditionally-hidden");
                } else if (!shouldHide && isHidden) {
                    container.classList.remove("sa-conditionally-hidden");
                }
            });
        }

        function scheduleSyncConditionalContainers() {
            if (syncScheduled) return;
            syncScheduled = true;
            window.requestAnimationFrame(syncConditionalContainers);
        }

        const wrappers = getFieldWrappers().filter(wrapper => {
            const control = getPrimaryControl(wrapper);
            if (!control) return false;

            const type = (control.type || "").toLowerCase();
            if (["hidden", "submit"].includes(type)) return false;
            if (isNestedInsideRepeater(wrapper)) return false;
            return true;
        });

        function getEditBtn(wrapper) {
            return wrapper.querySelector(".sa-edit-trigger");
        }

        function getSaveBtn(wrapper) {
            return wrapper.querySelector(".sa-save-trigger");
        }

        function getCancelBtn(wrapper) {
            return wrapper.querySelector(".sa-cancel-trigger");
        }

        function lockWrapper(wrapper) {
            wrapper.classList.add("sa-field-locked");
            wrapper.classList.remove("sa-field-selected");
            suppressWrapperValidation(wrapper);
            clearFluentFormErrors(wrapper);

            const actionHost = wrapper.querySelector(".sa-inline-action-host");
            if (actionHost) actionHost.classList.remove("sa-editing");

            const controls = wrapper.querySelectorAll("input, textarea, select");
            controls.forEach(lockControl);

            const editBtn = getEditBtn(wrapper);
            const saveBtn = getSaveBtn(wrapper);
            const cancelBtn = getCancelBtn(wrapper);

            if (editBtn) editBtn.style.display = "inline-flex";
            if (saveBtn) saveBtn.style.display = "none";
            if (cancelBtn) cancelBtn.style.display = "none";
        }

        function unlockWrapper(wrapper) {
            wrapper.classList.remove("sa-field-locked");
            wrapper.classList.add("sa-field-selected");
            restoreWrapperValidation(wrapper);
            clearFluentFormErrors(wrapper);

            const actionHost = wrapper.querySelector(".sa-inline-action-host");
            if (actionHost) actionHost.classList.add("sa-editing");

            const controls = wrapper.querySelectorAll("input, textarea, select");
            controls.forEach(unlockControl);

            const editBtn = getEditBtn(wrapper);
            const saveBtn = getSaveBtn(wrapper);
            const cancelBtn = getCancelBtn(wrapper);

            if (editBtn) editBtn.style.display = "none";
            if (saveBtn) saveBtn.style.display = "inline-flex";
            if (cancelBtn) cancelBtn.style.display = "inline-flex";
        }

        function lockAll() {
            wrappers.forEach(lockWrapper);
        }

        function selectWrapper(wrapper) {
            const fieldName = getFieldName(wrapper);
            if (!fieldName) return;

            const groupName = getGroupFromFieldName(fieldName);

            lockAll();

            wrappers.forEach(candidateWrapper => {
                const candidateField = getFieldName(candidateWrapper);
                const candidateGroup = getGroupFromFieldName(candidateField);

                if (candidateGroup === groupName) {
                    unlockWrapper(candidateWrapper);
                }
            });

            if (selectedFieldInput) selectedFieldInput.value = fieldName;
            if (selectedGroupInput) selectedGroupInput.value = groupName;
            if (confirmedInput) confirmedInput.value = "0";

            const control = getPrimaryControl(wrapper);
            if (control && control.type !== "file") {
                control.focus();
            }

            scheduleSyncConditionalContainers();
        }

        function resetSelection() {
            lockAll();
            if (selectedFieldInput) selectedFieldInput.value = "";
            if (selectedGroupInput) selectedGroupInput.value = "";
            if (confirmedInput) confirmedInput.value = "0";
            scheduleSyncConditionalContainers();
        }

        function collectGroupPayload(groupName) {
            const payload = {};

            wrappers.forEach(candidateWrapper => {
                const candidateField = getFieldName(candidateWrapper);
                const candidateGroup = getGroupFromFieldName(candidateField);

                if (candidateGroup !== groupName) return;

                const controls = candidateWrapper.querySelectorAll("input, textarea, select");

                controls.forEach(control => {
                    const type = (control.type || "").toLowerCase();
                    if (type === "hidden" || type === "submit" || type === "file") return;

                    const dataName = control.getAttribute("data-name");
                    const nameAttr = control.getAttribute("name");
                    let fieldName = dataName || nameAttr;

                    if (!fieldName) return;

                    const bracketMatch = fieldName.match(/^([^\[]+)\[([^\]]+)\]$/);

                    if (bracketMatch) {
                        const rootKey = bracketMatch[1];
                        const subKey = bracketMatch[2];

                        if (!payload[rootKey] || typeof payload[rootKey] !== "object" || Array.isArray(payload[rootKey])) {
                            payload[rootKey] = {};
                        }

                        if (type === "radio") {
                            if (control.checked) {
                                payload[rootKey][subKey] = control.value;
                            }
                            return;
                        }

                        if (type === "checkbox") {
                            if (!Array.isArray(payload[rootKey][subKey])) {
                                payload[rootKey][subKey] = [];
                            }
                            if (control.checked) {
                                payload[rootKey][subKey].push(control.value);
                            }
                            return;
                        }

                        payload[rootKey][subKey] = control.value;
                        return;
                    }

                    if (type === "radio") {
                        if (control.checked) {
                            payload[fieldName] = control.value;
                        }
                        return;
                    }

                    if (type === "checkbox") {
                        if (!payload[fieldName]) payload[fieldName] = [];
                        if (control.checked) {
                            payload[fieldName].push(control.value);
                        }
                        return;
                    }

                    payload[fieldName] = control.value;
                });
            });

            return payload;
        }
        
        function submitSelectedField(wrapper) {
            const fieldName = getFieldName(wrapper);
            if (!fieldName) return;

            const groupName = getGroupFromFieldName(fieldName);

            const confirmed = window.confirm(
            "Es-tu sûr(e) de vouloir enregistrer cette modification ? Cette action décomptera 1 de tes " + (SA_MODIFICATION.includedLimit || 5) + " modifications incluses."            );
            if (!confirmed) return;

            const payload = collectGroupPayload(groupName);

            if (selectedFieldInput) selectedFieldInput.value = fieldName;
            if (selectedGroupInput) selectedGroupInput.value = groupName;
            if (confirmedInput) confirmedInput.value = "1";

            const body = new URLSearchParams();
            body.append("action", "sa_save_modification");
            body.append("nonce", SA_MODIFICATION.nonce || "");
            body.append("order_id", String(SA_MODIFICATION.orderId || 0));
            body.append("group_key", groupName);
            body.append("payload", JSON.stringify(payload));

            fetch(SA_MODIFICATION.ajaxUrl, {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8"
                },
                body: body.toString()
            })
            .then(response => response.json())
            .then(result => {
                if (!result || !result.success) {
                    const message = result && result.data && result.data.message
                        ? result.data.message
                        : "Erreur lors de l’enregistrement.";
                    alert(message);
                    return;
                }

                if (result.data && result.data.data) {
                    Object.keys(result.data.data).forEach(key => {
                        originalData[key] = result.data.data[key];
                    });
                }

                if (result.data && typeof result.data.remaining !== "undefined") {
                    const remaining = parseInt(result.data.remaining || 0, 10);
                    const includedLimit = parseInt(SA_MODIFICATION.includedLimit || 5, 10);
                    const displayTotal = remaining > includedLimit ? remaining : includedLimit;

                    document.querySelectorAll(".sa-modification-remaining-value").forEach(el => {
                        el.textContent = remaining + " / " + displayTotal + " restantes";
                    });

                    document.querySelectorAll(".sa-modifications-count").forEach(el => {
                        el.textContent = remaining + "/" + displayTotal;
                    });

                    document.querySelectorAll(".sa-live-action-card--modification span").forEach(el => {
                        el.textContent = "Utiliser une de mes " + displayTotal + " modifications disponibles";
                    });

                    SA_MODIFICATION.remaining = remaining;
                }

                alert(result.data.message || "Modification enregistrée.");
                resetSelection();
                prefillAllFields(false);
                scheduleSyncConditionalContainers();
            })
            .catch(() => {
                alert("Erreur réseau lors de l’enregistrement.");
            });
        }

        function cancelSelectedField(wrapper) {
            const fieldName = getFieldName(wrapper);
            if (fieldName) restoreFieldOriginalValue(fieldName);
            resetSelection();
        }

        function buildActions(wrapper) {
            const fieldName = getFieldName(wrapper);
            if (!fieldName) return;

            const control = getPrimaryControl(wrapper);
            const type = control ? (control.type || "").toLowerCase() : "";

            const isRepeaterField =
                wrapper.classList.contains("ff-el-repeater") ||
                wrapper.matches('[data-type="repeater_field"]') ||
                !!wrapper.querySelector(".ff_repeater_table");

            const isFileField = control && type === "file";

            const isSimpleField =
                control &&
                !isFileField &&
                !isRepeaterField &&
                (
                    control.tagName === "TEXTAREA" ||
                    control.tagName === "SELECT" ||
                    ["text", "email", "url", "date", "number"].includes(type)
                );

            let row = wrapper.querySelector(".sa-field-actions");
            let actionHost = wrapper.querySelector(".sa-inline-action-host");

            if (!row) {
                row = document.createElement("div");
                row.className = "sa-field-actions";

                const editBtn = document.createElement("button");
                editBtn.type = "button";
                editBtn.className = "sa-edit-trigger";
                editBtn.textContent = "Modifier ce champ";

                const saveBtn = document.createElement("button");
                saveBtn.type = "button";
                saveBtn.className = "sa-save-trigger";
                saveBtn.textContent = "Enregistrer";
                saveBtn.style.display = "none";

                const cancelBtn = document.createElement("button");
                cancelBtn.type = "button";
                cancelBtn.className = "sa-cancel-trigger";
                cancelBtn.textContent = "Annuler";
                cancelBtn.style.display = "none";

                editBtn.addEventListener("click", function () {
                    selectWrapper(wrapper);
                });

                saveBtn.addEventListener("click", function () {
                    submitSelectedField(wrapper);
                });

                cancelBtn.addEventListener("click", function () {
                    cancelSelectedField(wrapper);
                });

                row.appendChild(editBtn);
                row.appendChild(saveBtn);
                row.appendChild(cancelBtn);
            }

            if (isFileField) {
                wrapper.classList.add("sa-file-action-field");

                let fileLayout = wrapper.querySelector(".sa-file-layout");
                if (!fileLayout) {
                    fileLayout = document.createElement("div");
                    fileLayout.className = "sa-file-layout";
                }

                let fileLeft = wrapper.querySelector(".sa-file-left");
                if (!fileLeft) {
                    fileLeft = document.createElement("div");
                    fileLeft.className = "sa-file-left";
                }

                let fileRight = wrapper.querySelector(".sa-file-right");
                if (!fileRight) {
                    fileRight = document.createElement("div");
                    fileRight.className = "sa-file-right";
                }

                const label = wrapper.querySelector(":scope > .ff-el-input--label");
                const inputContent = wrapper.querySelector(":scope > .ff-el-input--content");

                let existingPreview = wrapper.querySelector(".sa-file-current-preview");
                if (!existingPreview) {
                    existingPreview = document.createElement("div");
                    existingPreview.className = "sa-file-current-preview";
                }

                const currentValue = originalData[fieldName];
                const imageUrl = extractFileUrl(currentValue);
                existingPreview.innerHTML = "";

                if (imageUrl) {
                    const title = document.createElement("div");
                    title.className = "sa-file-current-title";
                    title.textContent = "Couverture actuelle";

                    const media = document.createElement("div");
                    media.className = "sa-file-current-media";

                    const link = document.createElement("a");
                    link.href = imageUrl;
                    link.target = "_blank";
                    link.rel = "noopener noreferrer";

                    const img = document.createElement("img");
                    img.src = imageUrl;
                    img.alt = "Couverture actuelle";
                    img.className = "sa-file-current-thumb";

                    link.appendChild(img);
                    media.appendChild(link);

                    existingPreview.appendChild(title);
                    existingPreview.appendChild(media);
                }

                fileLeft.innerHTML = "";
                fileRight.innerHTML = "";

                if (label) {
                    fileLeft.appendChild(label);
                }

                if (inputContent) {
                    fileLeft.appendChild(inputContent);
                }

                if (row) {
                    fileLeft.appendChild(row);
                }

                if (existingPreview && imageUrl) {
                    fileRight.appendChild(existingPreview);
                }

                fileLayout.innerHTML = "";
                fileLayout.appendChild(fileLeft);
                fileLayout.appendChild(fileRight);

                wrapper.innerHTML = "";
                wrapper.appendChild(fileLayout);

                return;
            }

            if (isRepeaterField) {
                wrapper.classList.add("sa-repeater-action-field");

                if (actionHost && actionHost.parentNode) {
                    actionHost.parentNode.removeChild(actionHost);
                }

                if (!row.parentNode || row.parentNode !== wrapper) {
                    wrapper.appendChild(row);
                }
                return;
            }

            if (isSimpleField) {
                wrapper.classList.add("sa-inline-action-field");

                const inputContent = wrapper.querySelector(".ff-el-input--content");
                const helpMessage = inputContent?.querySelector(".ff-el-help-message");
                if (helpMessage && helpMessage.parentNode === inputContent) {
                    wrapper.appendChild(helpMessage);
                }

                if (!actionHost) {
                    actionHost = document.createElement("div");
                    actionHost.className = "sa-inline-action-host";
                }

                if (!actionHost.contains(row)) {
                    actionHost.innerHTML = "";
                    actionHost.appendChild(row);
                }

                if (inputContent) {
                    if (actionHost.parentNode !== inputContent) {
                        inputContent.appendChild(actionHost);
                    }
                } else if (actionHost.parentNode !== wrapper) {
                    wrapper.appendChild(actionHost);
                }
            } else if (!row.parentNode || row.parentNode !== wrapper) {
                wrapper.appendChild(row);
            }
        }

        wrappers.forEach(buildActions);

        form.addEventListener("click", function (e) {
            const lockedControl = e.target.closest("[data-sa-locked='1']");
            if (lockedControl) {
                e.preventDefault();
                e.stopPropagation();
            }
        }, true);

        form.addEventListener("change", function () {
            scheduleSyncConditionalContainers();
        });

        form.addEventListener("submit", function (e) {
            if (form.classList.contains("sa-single-edit-mode")) {
                e.preventDefault();
            }
        });

        if (topResetBtn) {
            topResetBtn.addEventListener("click", function () {
                prefillAllFields();
                resetSelection();
            });
        }

        const conditionalObserver = new MutationObserver(function (mutations) {
            const shouldSync = mutations.some(function (mutation) {
                if (mutation.type === "childList") return true;
                if (mutation.type === "attributes") {
                    const target = mutation.target;
                    if (!target || !target.classList) return false;
                    return !target.classList.contains("sa-conditionally-hidden");
                }
                return false;
            });

            if (shouldSync) {
                scheduleSyncConditionalContainers();
            }
        });

        conditionalObserver.observe(form, {
            childList: true,
            subtree: true,
            attributes: true,
            attributeFilter: ["class", "style", "hidden", "aria-hidden"]
        });

        prefillAllFields(false);

        setTimeout(function () {
            prefillAllFields(true);
        }, 200);

        setTimeout(function () {
            prefillAllFields(true);
        }, 600);

        setTimeout(function () {
            const formatControls = form.querySelectorAll('[name="book_formats[]"], [name="book_formats"]');
            formatControls.forEach(control => {
                control.dispatchEvent(new Event("change", { bubbles: true }));
            });
        }, 800);

        resetSelection();
        scheduleSyncConditionalContainers();
        setTimeout(scheduleSyncConditionalContainers, 200);
        setTimeout(scheduleSyncConditionalContainers, 600);

        return true;
    }

    if (bootModificationForm()) return;

    let tries = 0;
    const interval = setInterval(function () {
        tries++;
        if (bootModificationForm() || tries > 20) {
            clearInterval(interval);
        }
    }, 300);
});