<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import FormField from '@/Components/FormField.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useVuelidate } from '@vuelidate/core';
import { useValidationRules } from '@/Composables/useValidationRules';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const { rules, custom } = useValidationRules();
const validationRules = computed(() => ({
    name: { required: rules.required },
    email: rules.email,
    password: rules.password,
    password_confirmation: {
        required: rules.required,
        sameAsPassword: custom.sameAs(computed(() => form.password), 'Password')
    }
}));

const v$ = useVuelidate(validationRules, form);

const submit = async () => {
    const isFormValid = await v$.value.$validate();
    if (!isFormValid) return;

    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Register" />

        <form @submit.prevent="submit" class="space-y-4">
            <FormField
                id="name"
                label="Name"
                type="text"
                v-model="form.name"
                required
                autofocus
                autocomplete="name"
                @blur="v$.name.$touch()"
                :error="v$.name.$error ? v$.name.$errors[0].$message : form.errors.name"
            />

            <FormField
                id="email"
                label="Email"
                type="email"
                v-model="form.email"
                required
                autocomplete="username"
                @blur="v$.email.$touch()"
                :error="v$.email.$error ? v$.email.$errors[0].$message : form.errors.email"
            />

            <FormField
                id="password"
                label="Password"
                type="password"
                v-model="form.password"
                required
                autocomplete="new-password"
                @blur="v$.password.$touch()"
                :error="v$.password.$error ? v$.password.$errors[0].$message : form.errors.password"
            />

            <FormField
                id="password_confirmation"
                label="Confirm Password"
                type="password"
                v-model="form.password_confirmation"
                required
                autocomplete="new-password"
                @blur="v$.password_confirmation.$touch()"
                :error="v$.password_confirmation.$error ? v$.password_confirmation.$errors[0].$message : form.errors.password_confirmation"
            />

            <div class="mt-4 flex items-center justify-end">
                <Link
                    :href="route('login')"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                >
                    Already registered?
                </Link>

                <PrimaryButton
                    class="ms-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Register
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
