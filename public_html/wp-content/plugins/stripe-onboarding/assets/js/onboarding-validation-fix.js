document.addEventListener("DOMContentLoaded", function () {
    const form =
        document.querySelector('form[data-form_id]') ||
        document.querySelector('form[id^="fluentform_"]');

    if (!form) return;

    form.setAttribute("novalidate", "novalidate");

    function getFieldContainer(field) {
        return (
            field.closest(".ff-el-group") ||
            field.closest(".ff-field_container") ||
            field.closest(".ff_upload_wrapper") ||
            field.parentElement
        );
    }

    /**
     * IMPORTANT :
     * On ne doit PAS considérer comme "hidden"
     * un champ simplement masqué par le wizard custom.
     *
     * On ne traite ici QUE les vrais champs conditionnels cachés
     * par Fluent Forms / logique conditionnelle.
     */
    function isConditionallyHidden(field) {
        if (!field) return false;

        if (field.type === "hidden") return true;
        if (field.closest(".ff_conditional_hidden")) return true;
        if (field.closest(".ff-el-is-hidden")) return true;
        if (field.closest(".fluentform-hidden")) return true;
        if (field.closest(".ff_excluded")) return true;
        if (field.hidden) return true;
        if (field.closest("[hidden]")) return true;

        return false;
    }

    function disableConditionalHiddenFieldValidation() {
        const fields = form.querySelectorAll("input, select, textarea");

        fields.forEach(function (field) {
            if (!field.name) return;

            const container = getFieldContainer(field);
            const hiddenByCondition =
                isConditionallyHidden(field) ||
                (container && isConditionallyHidden(container));

            if (hiddenByCondition) {
                if (field.required) {
                    field.dataset.saRequired = "1";
                    field.required = false;
                }

                if (field.hasAttribute("required")) {
                    field.dataset.saHadRequiredAttr = "1";
                    field.removeAttribute("required");
                }

                if (field.getAttribute("aria-required") === "true") {
                    field.dataset.saAriaRequired = "true";
                    field.setAttribute("aria-required", "false");
                }

                /**
                 * On désactive seulement les champs réellement exclus
                 * par la condition Fluent Forms.
                 * Cela évite de casser les uploads et le wizard.
                 */
                if (!field.disabled && field.type !== "hidden") {
                    field.dataset.saDisabledForValidation = "1";
                    field.disabled = true;
                }

                field.setCustomValidity("");
                return;
            }

            if (field.dataset.saDisabledForValidation === "1") {
                field.disabled = false;
                delete field.dataset.saDisabledForValidation;
            }

            if (field.dataset.saRequired === "1") {
                field.required = true;
                delete field.dataset.saRequired;
            }

            if (field.dataset.saHadRequiredAttr === "1") {
                field.setAttribute("required", "required");
                delete field.dataset.saHadRequiredAttr;
            }

            if (field.dataset.saAriaRequired === "true") {
                field.setAttribute("aria-required", "true");
                delete field.dataset.saAriaRequired;
            }

            field.setCustomValidity("");
        });
    }

    function beforeSubmitFix() {
        disableConditionalHiddenFieldValidation();

        const invalidFields = form.querySelectorAll(":invalid");

        invalidFields.forEach(function (field) {
            if (isConditionallyHidden(field)) {
                field.setCustomValidity("");
                field.required = false;
                field.removeAttribute("required");
                field.disabled = true;
            }
        });
    }

    disableConditionalHiddenFieldValidation();

    form.addEventListener("change", function () {
        disableConditionalHiddenFieldValidation();
    });

    form.addEventListener("input", function () {
        disableConditionalHiddenFieldValidation();
    });

    form.addEventListener(
        "submit",
        function () {
            beforeSubmitFix();
        },
        true
    );

    const submitButtons = form.querySelectorAll(
        'button[type="submit"], input[type="submit"]'
    );

    submitButtons.forEach(function (button) {
        button.addEventListener(
            "click",
            function () {
                beforeSubmitFix();
            },
            true
        );
    });

    const observer = new MutationObserver(function () {
        disableConditionalHiddenFieldValidation();
    });

    observer.observe(form, {
        childList: true,
        subtree: true,
        attributes: true,
        attributeFilter: ["class", "hidden", "required", "aria-required"]
    });

    window.setInterval(disableConditionalHiddenFieldValidation, 700);
});