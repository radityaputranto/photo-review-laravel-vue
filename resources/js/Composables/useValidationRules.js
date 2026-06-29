import { required, email, minLength, url, numeric, minValue, sameAs, helpers } from '@vuelidate/validators'

// Reusable custom messages
const msgRequired = helpers.withMessage('Kolom ini wajib diisi.', required)
const msgEmail = helpers.withMessage('Format email tidak valid.', email)
const msgMinLength = (min) => helpers.withMessage(`Minimal ${min} karakter.`, minLength(min))
const msgUrl = helpers.withMessage('Format URL tidak valid.', url)
const msgNumeric = helpers.withMessage('Harus berupa angka.', numeric)
const msgMinValue = (min) => helpers.withMessage(`Nilai minimal adalah ${min}.`, minValue(min))
const msgSameAs = (ref, fieldName) => helpers.withMessage(`Harus sama dengan ${fieldName}.`, sameAs(ref))

export function useValidationRules() {
    return {
        // Common standard rules
        rules: {
            required: msgRequired,
            email: { required: msgRequired, email: msgEmail },
            password: { required: msgRequired, minLength: msgMinLength(8) },
            url: { required: msgRequired, url: msgUrl },
        },
        // Helper to generate dynamic rules
        custom: {
            sameAs: msgSameAs,
            minValue: msgMinValue,
            minLength: msgMinLength,
            numeric: msgNumeric
        }
    }
}
