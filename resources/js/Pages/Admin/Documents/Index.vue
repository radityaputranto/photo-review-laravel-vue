<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Modal from '@/Components/Modal.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import DangerButton from '@/Components/DangerButton.vue'
import { PlusIcon, TrashIcon, DocumentTextIcon, DocumentIcon, CurrencyDollarIcon } from '@heroicons/vue/24/outline'
import { ref, computed } from 'vue'

const props = defineProps({
    documents: Object,
    customers: Array,
    sessions: Array,
})

const isModalOpen = ref(false)
const isDeleteModalOpen = ref(false)
const deletingDocument = ref(null)

const form = useForm({
    customer_id: '',
    session_id: '',
    type: 'invoice',
    title: '',
    drive_url: '',
})

// Filter sessions based on selected customer
const filteredSessions = computed(() => {
    if (!form.customer_id) return []
    return props.sessions.filter(s => s.customer_id == form.customer_id)
})

const openModal = () => {
    form.reset()
    form.clearErrors()
    isModalOpen.value = true
}

const submit = () => {
    form.post(route('admin.documents.store'), {
        onSuccess: () => {
            isModalOpen.value = false
            form.reset()
        },
    })
}

const confirmDelete = (doc) => {
    deletingDocument.value = doc
    isDeleteModalOpen.value = true
}

const deleteDocument = () => {
    form.delete(route('admin.documents.destroy', deletingDocument.value.id), {
        onSuccess: () => {
            isDeleteModalOpen.value = false
            deletingDocument.value = null
        },
    })
}

const getIcon = (type) => {
    if (type === 'invoice') return CurrencyDollarIcon
    if (type === 'contract') return DocumentTextIcon
    return DocumentIcon
}
</script>

<template>
    <Head title="Documents" />

    <AdminLayout>
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-slate-900">Documents</h1>
            <PrimaryButton @click="openModal">
                <PlusIcon class="w-5 h-5 mr-2" />
                Add Document
            </PrimaryButton>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Title / Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Session</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    <tr v-for="doc in documents.data" :key="doc.id">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                                    <component :is="getIcon(doc.type)" class="w-5 h-5" />
                                </div>
                                <div>
                                    <div class="font-medium text-slate-900">{{ doc.title }}</div>
                                    <div class="text-xs text-slate-500 uppercase tracking-wider">{{ doc.type }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-slate-600">
                            {{ doc.customer?.name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-slate-600">
                            {{ doc.session?.title || '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-slate-500 text-sm">
                            {{ new Date(doc.created_at).toLocaleDateString() }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a :href="'https://drive.google.com/file/d/' + doc.drive_file_id + '/view'" target="_blank" class="text-blue-600 hover:text-blue-900 mr-4">
                                View
                            </a>
                            <button @click="confirmDelete(doc)" class="text-red-600 hover:text-red-900">
                                <TrashIcon class="w-5 h-5 inline-block" />
                            </button>
                        </td>
                    </tr>
                    <tr v-if="!documents.data.length">
                        <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                            No documents found.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Add Modal -->
        <Modal :show="isModalOpen" @close="isModalOpen = false">
            <div class="p-6">
                <h2 class="text-lg font-medium text-slate-900 mb-4">Add Document</h2>
                
                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <InputLabel for="title" value="Document Title" />
                        <TextInput
                            id="title"
                            v-model="form.title"
                            type="text"
                            class="mt-1 block w-full"
                            placeholder="e.g. Invoice Wedding 50%"
                            required
                        />
                        <InputError :message="form.errors.title" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="type" value="Type" />
                            <select id="type" v-model="form.type" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="invoice">Invoice</option>
                                <option value="contract">Contract</option>
                                <option value="other">Other</option>
                            </select>
                            <InputError :message="form.errors.type" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel for="customer_id" value="Customer" />
                            <select id="customer_id" v-model="form.customer_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="" disabled>Select Customer</option>
                                <option v-for="c in customers" :key="c.id" :value="c.id">{{ c.name }}</option>
                            </select>
                            <InputError :message="form.errors.customer_id" class="mt-2" />
                        </div>
                    </div>

                    <div>
                        <InputLabel for="session_id" value="Related Session (Optional)" />
                        <select id="session_id" v-model="form.session_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" :disabled="!form.customer_id">
                            <option value="">None</option>
                            <option v-for="s in filteredSessions" :key="s.id" :value="s.id">{{ s.title }}</option>
                        </select>
                        <InputError :message="form.errors.session_id" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel for="drive_url" value="Google Drive File URL" />
                        <TextInput
                            id="drive_url"
                            v-model="form.drive_url"
                            type="url"
                            class="mt-1 block w-full"
                            placeholder="https://drive.google.com/file/d/..."
                            required
                        />
                        <p class="mt-1 text-xs text-slate-500">Must be a direct link to the file. Ensure sharing permissions are correct.</p>
                        <InputError :message="form.errors.drive_url" class="mt-2" />
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <SecondaryButton type="button" @click="isModalOpen = false">Cancel</SecondaryButton>
                        <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            Save Document
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Delete Modal -->
        <Modal :show="isDeleteModalOpen" @close="isDeleteModalOpen = false" maxWidth="sm">
            <div class="p-6">
                <h2 class="text-lg font-medium text-slate-900 mb-4">Confirm Deletion</h2>
                <p class="text-sm text-slate-600 mb-6">Are you sure you want to delete this document record?</p>
                <div class="flex justify-end gap-3">
                    <SecondaryButton @click="isDeleteModalOpen = false">Cancel</SecondaryButton>
                    <DangerButton @click="deleteDocument" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Delete
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </AdminLayout>
</template>
