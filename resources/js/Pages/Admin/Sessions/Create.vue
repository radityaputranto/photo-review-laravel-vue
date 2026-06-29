<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import FormField from '@/Components/FormField.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import { ArrowLeftIcon, CheckCircleIcon, ExclamationCircleIcon } from '@heroicons/vue/24/outline'
import { ref, computed } from 'vue'
import { useVuelidate } from '@vuelidate/core'
import { useValidationRules } from '@/Composables/useValidationRules'

const props = defineProps({
    customers: Array
})

const form = useForm({
    customer_id: '',
    title: '',
    shoot_date: '',
    drive_folder_url: '',
    max_selections: 30,
})

const { rules, custom } = useValidationRules()
const validationRules = computed(() => ({
    title: { required: rules.required },
    customer_id: { required: rules.required },
    shoot_date: { required: rules.required },
    max_selections: { required: rules.required, numeric: custom.numeric, minValue: custom.minValue(1) },
    drive_folder_url: rules.url
}))

const v$ = useVuelidate(validationRules, form)

const validatingDriveUrl = ref(false)
const driveUrlStatus = ref(null) // null, 'success', 'error'
const driveUrlMessage = ref('')

const validateDriveUrl = async () => {
    if (!form.drive_folder_url) return

    validatingDriveUrl.value = true
    driveUrlStatus.value = null
    
    try {
        const response = await fetch(route('admin.sessions.validate-drive-url'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify({ url: form.drive_folder_url })
        })
        
        const data = await response.json()
        
        if (data.success) {
            driveUrlStatus.value = 'success'
            driveUrlMessage.value = data.message
        } else {
            driveUrlStatus.value = 'error'
            driveUrlMessage.value = data.message || 'Validation failed'
        }
    } catch (error) {
        driveUrlStatus.value = 'error'
        driveUrlMessage.value = 'Network error during validation'
    } finally {
        validatingDriveUrl.value = false
    }
}

const submit = async () => {
    const isFormValid = await v$.value.$validate()
    if (!isFormValid) return

    form.post(route('admin.sessions.store'))
}
</script>

<template>
    <Head title="Create Session" />

    <AdminLayout>
        <div class="mb-6 flex items-center gap-4">
            <Link :href="route('admin.sessions.index')" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-full transition-colors">
                <ArrowLeftIcon class="w-5 h-5" />
            </Link>
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Create New Session</h1>
            </div>
        </div>

        <div class="max-w-3xl bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <form @submit.prevent="submit" class="p-6 space-y-6">
                <!-- Title & Customer -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <FormField
                        id="title"
                        label="Session Title"
                        type="text"
                        v-model="form.title"
                        placeholder="e.g. Wedding Andi & Budi"
                        required
                        @blur="v$.title.$touch()"
                        :error="v$.title.$error ? v$.title.$errors[0].$message : form.errors.title"
                    />
                    
                    <FormField
                        id="customer_id"
                        label="Customer"
                        required
                        :error="v$.customer_id.$error ? v$.customer_id.$errors[0].$message : form.errors.customer_id"
                    >
                        <template #default="{ errorClass }">
                            <select
                                id="customer_id"
                                v-model="form.customer_id"
                                class="block w-full rounded-md shadow-sm transition-colors bg-gray-50 text-gray-900"
                                :class="errorClass"
                                @blur="v$.customer_id.$touch()"
                            >
                                <option value="" disabled>Select Customer</option>
                                <option v-for="customer in customers" :key="customer.id" :value="customer.id">
                                    {{ customer.name }} ({{ customer.email }})
                                </option>
                            </select>
                        </template>
                    </FormField>
                </div>

                <!-- Date & Max Selections -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <FormField
                        id="shoot_date"
                        label="Shoot Date"
                        type="date"
                        v-model="form.shoot_date"
                        required
                        @blur="v$.shoot_date.$touch()"
                        :error="v$.shoot_date.$error ? v$.shoot_date.$errors[0].$message : form.errors.shoot_date"
                    />

                    <FormField
                        id="max_selections"
                        label="Max Selections"
                        type="number"
                        v-model="form.max_selections"
                        min="1"
                        required
                        @blur="v$.max_selections.$touch()"
                        :error="v$.max_selections.$error ? v$.max_selections.$errors[0].$message : form.errors.max_selections"
                    />
                </div>

                <!-- Google Drive URL -->
                <div>
                    <FormField
                        id="drive_folder_url"
                        label="Google Drive Folder URL"
                        required
                        :error="v$.drive_folder_url.$error ? v$.drive_folder_url.$errors[0].$message : form.errors.drive_folder_url"
                    >
                        <template #default="{ errorClass }">
                            <input
                                id="drive_folder_url"
                                type="url"
                                v-model="form.drive_folder_url"
                                class="flex-1 rounded-none rounded-l-md shadow-sm transition-colors bg-gray-50 text-gray-900 border-r-0"
                                :class="errorClass"
                                placeholder="https://drive.google.com/drive/folders/..."
                                @blur="v$.drive_folder_url.$touch()"
                            />
                            <button
                                type="button"
                                @click="validateDriveUrl"
                                :disabled="!form.drive_folder_url || validatingDriveUrl"
                                class="inline-flex items-center px-4 py-2 bg-slate-100 border border-slate-300 rounded-r-md font-medium text-slate-700 hover:bg-slate-200 focus:outline-none transition-colors disabled:opacity-50"
                                :class="errorClass.includes('border-red-500') ? 'border-red-500 border-l-gray-300' : ''"
                            >
                                <span v-if="validatingDriveUrl">Validating...</span>
                                <span v-else>Validate</span>
                            </button>
                        </template>
                    </FormField>
                    
                    <!-- Validation Status -->
                    <div v-if="driveUrlStatus" class="mt-2 flex items-start gap-2 text-sm"
                        :class="driveUrlStatus === 'success' ? 'text-green-600' : 'text-red-600'"
                    >
                        <CheckCircleIcon v-if="driveUrlStatus === 'success'" class="w-5 h-5 shrink-0" />
                        <ExclamationCircleIcon v-else class="w-5 h-5 shrink-0" />
                        <span>{{ driveUrlMessage }}</span>
                    </div>
                    <p class="mt-2 text-xs text-slate-500">
                        Ensure the folder is shared ("Anyone with the link can view") or explicitly shared with the service account email.
                    </p>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <Link :href="route('admin.sessions.index')">
                        <SecondaryButton type="button">Cancel</SecondaryButton>
                    </Link>
                    <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Create Session
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
