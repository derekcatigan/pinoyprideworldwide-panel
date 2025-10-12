import { watchEffect } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { toast } from 'vue-sonner'

export function useFlashToast() {
    const page = usePage()

    watchEffect(() => {
        const flash = page.props.flash
        if (!flash) return

        if (flash.success) toast.success(flash.success)
        if (flash.error) toast.error(flash.error)
        if (flash.warning) toast.warning(flash.warning)
        if (flash.info) toast.info(flash.info)
    })
}

export function useFlashValidation(errors, delay = 100) {
    Object.values(errors)
        .flat()
        .forEach((msg, index) => {
            setTimeout(() => toast.error(msg), index * delay);
        });
}
