<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import InputError from '@/Components/InputError.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import { ArrowLeftIcon, CheckCircleIcon, ExclamationCircleIcon } from '@heroicons/vue/24/outline'
import { ref } from 'vue'

const props = defineProps({
    session: Object,
    customers: Array
})

const form = useForm({
    customer_id: props.session.customer_id,
    title: props.session.title,
    shoot_date: props.session.shoot_date,
    drive_folder_url: props.session.drive_folder_url,
    max_selections: props.session.max_selections,
    status: props.session.status,
    download_link: props.session.download_link || '',
})

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

const submit = () => {
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
                    <div>
                        <InputLabel for="title" value="Session Title" />
                        <TextInput
                            id="title"
                            v-model="form.title"
                            type="text"
                            class="mt-1 block w-full"
                            required
                        />
                        <InputError :message="form.errors.title" class="mt-2" />
                    </div>
                    
                    <div>
                        <InputLabel for="customer_id" value="Customer" />
                        <select
                            id="customer_id"
                            v-model="form.customer_id"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            required
                        >
                            <option value="" disabled>Select Customer</option>
                            <option v-for="customer in customers" :key="customer.id" :value="customer.id">
                                {{ customer.name }} ({{ customer.email }})
                            </option>
                        </select>
                        <InputError :message="form.errors.customer_id" class="mt-2" />
                    </div>
                </div>

                <!-- Date & Max Selections -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <InputLabel for="shoot_date" value="Shoot Date" />
                        <TextInput
                            id="shoot_date"
                            v-model="form.shoot_date"
                            type="date"
                            class="mt-1 block w-full"
                            required
                        />
                        <InputError :message="form.errors.shoot_date" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel for="max_selections" value="Max Selections" />
                        <TextInput
                            id="max_selections"
                            v-model="form.max_selections"
                            type="number"
                            min="1"
                            class="mt-1 block w-full"
                            required
                        />
                        <InputError :message="form.errors.max_selections" class="mt-2" />
                    </div>
                </div>

                <!-- Status & Download Link -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <InputLabel for="status" value="Status" />
                        <select
                            id="status"
                            v-model="form.status"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            required
                        >
                            <option value="active">Active</option>
                            <option value="completed">Completed (Pending Review/Edit)</option>
                            <option value="delivered">Delivered</option>
                        </select>
                        <InputError :message="form.errors.status" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel for="download_link" value="Final Download Link (Optional)" />
                        <TextInput
                            id="download_link"
                            v-model="form.download_link"
                            type="url"
                            class="mt-1 block w-full"
                            placeholder="https://..."
                        />
                        <InputError :message="form.errors.download_link" class="mt-2" />
                    </div>
                </div>

                <!-- Google Drive URL -->
                <div>
                    <InputLabel for="drive_folder_url" value="Google Drive Folder URL" />
                    <div class="mt-1 flex rounded-md shadow-sm">
                        <TextInput
                            id="drive_folder_url"
                            v-model="form.drive_folder_url"
                            type="url"
                            class="flex-1 rounded-none rounded-l-md"
                            required
                        />
                        <button
                            type="button"
                            @click="validateDriveUrl"
                            :disabled="!form.drive_folder_url || validatingDriveUrl"
                            class="inline-flex items-center px-4 py-2 bg-slate-100 border border-slate-300 border-l-0 rounded-r-md font-medium text-slate-700 hover:bg-slate-200 focus:outline-none transition-colors disabled:opacity-50"
                        >
                            <span v-if="validatingDriveUrl">...</span>
                            <span v-else>Validate</span>
                        </button>
                    </div>
                    <InputError :message="form.errors.drive_folder_url" class="mt-2" />
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
