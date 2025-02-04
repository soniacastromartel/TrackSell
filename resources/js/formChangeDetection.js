// public/js/formChangeDetection.js
window.setupFormChangeDetection = function(formSelector, buttonSelector, modalSelector) {
    let initialFormState = {};

    $(modalSelector).on('show.bs.modal', function() {
        initialFormState = getFormValues(formSelector);
        $(buttonSelector).prop('disabled', true);
    });

    $(formSelector + ' input').on('input', function() {
        toggleSaveButton(formSelector, buttonSelector);
    });

    function getFormValues(form) {
        let values = {};
        $(form + ' input').each(function() {
            values[$(this).attr('name')] = $(this).val();
        });
        return values;
    }

    function toggleSaveButton(form, button) {
        let currentValues = getFormValues(form);
        let hasChanges = Object.keys(initialFormState).some(key => initialFormState[key] !== currentValues[key]);
        $(button).prop('disabled', !hasChanges);
    }
};
