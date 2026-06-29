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
    session: Object,
    customers: Array
})

const form = useForm({
    customer_id: props.session.customer_id,
    title: props.session.title,
    shoot_date: props.session.shoot_date ? props.session.shoot_date.split('T')[0] : '',
    drive_folder_url: props.session.drive_folder_url,
    max_selections: props.session.max_selections,
    status: props.session.status,
    download_link: props.session.download_link || '',
})

const { rules, custom } = useValidationRules()
const validationRules = computed(() => ({
    title: { required: rules.required },
    customer_id: { required: rules.required },
    shoot_date: { required: rules.required },
    max_selections: { required: rules.required, numeric: custom.numeric, minValue: custom.minValue(1) },
    drive_folder_url: rules.url,
    download_link: { url: rules.url.url } // optional, but if filled must be url. Wait, rules.url has required. So just custom url?
}))

// Let's adjust download_link to be optional URL
// Actually Vuelidate `url` validator does not enforce `required` by default unless `required` is also passed. So we can just use the url validator directly.
const v$ = useVuelidate(
    computed(() => ({
        ...validationRules.value,
        download_link: { url: rules.url.url } 
    })),
    form
)

const validatingDriveUrl = ref(false)
const driveUrlStatus = ref(null)
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
        driveUrlMessage.value = 'Network error'
    } finally {
        validatingDriveUrl.value = false
    }
}

const submit = async () => {
    const isFormValid = await v$.value.$validate()
    if (!isFormValid) return

    form.put(route('admin.sessions.update', props.session.id))
}
</script>

<template>
    <Head :title="'Edit ' + session.title" />

    <AdminLayout>
        <div class="mb-6 flex items-center gap-4">
            <Link :href="route('admin.sessions.index')" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-full transition-colors">
                <ArrowLeftIcon class="w-5 h-5" />
            </Link>
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Edit Session: {{ session.title }}</h1>
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

                <!-- Status & Download Link -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <FormField
                        id="status"
                        label="Status"
                        required
                        :error="form.errors.status"
                    >
                        <template #default="{ errorClass }">
                            <select
                                id="status"
                                v-model="form.status"
                                class="block w-full rounded-md shadow-sm transition-colors bg-gray-50 text-gray-900"
                                :class="errorClass"
                            >
                                <option value="active">Active</option>
                                <option value="completed">Completed (Pending Review/Edit)</option>
                                <option value="delivered">Delivered</option>
                            </select>
                        </template>
                    </FormField>

                    <FormField
                        id="download_link"
                        label="Final Download Link (Optional)"
                        type="url"
                        v-model="form.download_link"
                        placeholder="https://..."
                        @blur="v$.download_link.$touch()"
                        :error="v$.download_link.$error ? v$.download_link.$errors[0].$message : form.errors.download_link"
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
                                @blur="v$.drive_folder_url.$touch()"
                            />
                            <button
                                type="button"
                                @click="validateDriveUrl"
                                :disabled="!form.drive_folder_url || validatingDriveUrl"
                                class="inline-flex items-center px-4 py-2 bg-slate-100 border border-slate-300 rounded-r-md font-medium text-slate-700 hover:bg-slate-200 focus:outline-none transition-colors disabled:opacity-50"
                                :class="errorClass.includes('border-red-500') ? 'border-red-500 border-l-gray-300' : ''"
                            >
                                <span v-if="validatingDriveUrl">...</span>
                                <span v-else>Validate</span>
                            </button>
                        </template>
                    </FormField>
                    <div v-if="driveUrlStatus" class="mt-2 flex items-start gap-2 text-sm"
                        :class="driveUrlStatus === 'success' ? 'text-green-600' : 'text-red-600'"
                    >
                        <span>{{ driveUrlMessage }}</span>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                    <Link :href="route('admin.sessions.index')">
                        <SecondaryButton type="button">Cancel</SecondaryButton>
                    </Link>
                    <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Save Changes
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
