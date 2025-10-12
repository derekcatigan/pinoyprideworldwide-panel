<script setup>
import { ref } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { useFlashValidation } from '@/composables/useFlashToast.js'

// Components
import Logo from '@/assets/logos/PinoyPrideLogo.png'
import ToastAlert from '@/components/ToastAlert.vue'

const page = usePage()

const form = useForm({
    email: null,
    password: null,
});

const passwordInput = ref(null);

function togglePassword() {
    const input = passwordInput.value;
    input.type = input.type === "password" ? "text" : "password";
}

function submit() {
    form.post('/login', {
        onSuccess: () => {
            form.reset()
        },
        onError: (error) => {
            useFlashValidation(error)
        }
    })
}
</script>

<template>
    <ToastAlert />
    <main class="h-screen flex items-center justify-center bg-gray-50">
        <section class="w-[500px] p-6 bg-white rounded-2xl shadow-md border border-gray-200">
            <form @submit.prevent="submit" class="space-y-6">
                <!-- Logo + title -->
                <header class="flex items-center gap-4">
                    <img :src="Logo" alt="Pinoy Pride Logo"
                        class="w-20 h-20 object-contain rounded-lg border border-gray-200 shadow-sm bg-white p-1" />
                    <div>
                        <h1 class="text-lg font-bold text-gray-800">Pinoy Pride Worldwide LLC - Login</h1>
                        <div class="w-16 h-1 bg-yellow-500 rounded mt-1"></div>
                        <p class="mt-2 text-sm text-gray-500">
                            Please sign in to access your account
                        </p>
                    </div>
                </header>

                <!-- Email -->
                <fieldset class="fieldset">
                    <legend class="fieldset-legend text-sm">Email</legend>
                    <input type="email" v-model="form.email" placeholder="e.g. johndoe@email.com"
                        class="w-full input" />
                </fieldset>

                <!-- Password -->
                <fieldset class="fieldset">
                    <legend class="fieldset-legend">Password</legend>
                    <input id="password" ref="passwordInput" type="password" v-model="form.password"
                        class="w-full input" placeholder="password" />
                </fieldset>

                <!-- Options -->
                <div class="flex justify-between items-center text-sm">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" class="checkbox scale-90" @click="togglePassword" />
                        <span>Show password</span>
                    </label>
                    <Link href="#" class="text-blue-500 hover:underline">Forgot password?</Link>
                </div>

                <!-- Button -->
                <button type="submit" class="btn btn-block btn-primary" :disabled="form.processing">
                    Login
                </button>
            </form>
        </section>
    </main>
</template>
