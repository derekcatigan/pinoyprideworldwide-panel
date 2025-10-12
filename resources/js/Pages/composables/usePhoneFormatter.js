import { watch } from 'vue'

export function usePhoneFormatter(valueProxy, countryRef) {
    // Define formatting rules for multiple countries.
    // Each country has a dialing code, expected number of digits, and a formatting function.
    const formats = {
        PH: { code: '+63', digits: 10, format: (d) => d.replace(/(\+63)(\d{3})(\d{3})(\d{4})/, '$1 $2 $3 $4') },
        US: { code: '', digits: 10, format: (d) => d.replace(/(\d{3})(\d{3})(\d{4})/, '($1) $2-$3') },
        UK: { code: '+44', digits: 10, format: (d) => d.replace(/(\+44)(\d{4})(\d{6})/, '$1 $2 $3') },
        CA: { code: '', digits: 10, format: (d) => d.replace(/(\d{3})(\d{3})(\d{4})/, '($1) $2-$3') },
        AU: { code: '+61', digits: 9, format: (d) => d.replace(/(\+61)(\d{1})(\d{4})(\d{4})/, '$1 $2 $3 $4') },
        IN: { code: '+91', digits: 10, format: (d) => d.replace(/(\+91)(\d{5})(\d{5})/, '$1 $2 $3') }
    }

    // Formats a raw phone number according to the selected country's rules.
    function formatPhoneNumber(value, country) {
        if (!value) return ''
        let digits = value.replace(/\D/g, '') // remove any non-numeric characters
        const fmt = formats[country]
        if (!fmt) return value // fallback if the country is not defined

        digits = digits.slice(-fmt.digits) // keep only the last n digits
        if (fmt.code) digits = fmt.code + digits // prepend country code if available

        return fmt.format(digits) // apply country-specific formatting
    }

    // Watch the value proxy (the phone input) for changes
    // Automatically formats the number once it reaches the required length
    watch(
        () => valueProxy.get(),
        (val) => {
            const digits = val.replace(/\D/g, '')
            const fmt = formats[countryRef.value]
            if (!fmt) return

            if (digits.length >= fmt.digits) {
                valueProxy.set(formatPhoneNumber(val, countryRef.value))
            }
        }
    )
}
