<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { ArrowLeftIcon, ArrowDownTrayIcon, ArrowPathIcon } from '@heroicons/vue/24/outline'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import DangerButton from '@/Components/DangerButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import Modal from '@/Components/Modal.vue'
import { ref } from 'vue'

const props = defineProps({
    session: Object,
})

const isResetModalOpen = ref(false)
const form = useForm({})

const confirmReset = () => {
    isResetModalOpen.value = true
}

const resetSelections = () => {
    form.delete(route('admin.sessions.selections.reset', props.session.id), {
        onSuccess: () => {
            isResetModalOpen.value = false
        }
    })
}
</script>

<template>
    <Head :title="'Selections: ' + session.title" />

    <AdminLayout>
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <Link :href="route('admin.sessions.show', session.id)" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-full transition-colors">
                    <ArrowLeftIcon class="w-5 h-5" />
                </Link>
                <div>
                    <h1 class="text-2xl font-bold text-slate-900">Customer Selections</h1>
                    <p class="text-slate-500 mt-1">{{ session.title }} - {{ session.customer?.name }}</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <a :href="route('admin.sessions.selections.export', session.id)" target="_blank">
                    <PrimaryButton :disabled="!session.photo_selections?.length">
                        <ArrowDownTrayIcon class="w-5 h-5 mr-2" />
                        Export TXT
                    </PrimaryButton>
                </a>
                <button 
                    @click="confirmReset"
                    class="inline-flex items-center px-4 py-2 bg-white border border-red-300 rounded-md font-medium text-red-700 hover:bg-red-50 transition-colors"
                >
                    <ArrowPathIcon class="w-4 h-4 mr-2" />
                    Reset & Unlock
                </button>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                <div class="flex items-center gap-4">
                    <span class="text-sm font-medium text-slate-700">
                        Total Selected: <span class="font-bold text-slate-900">{{ session.photo_selections?.length || 0 }}</span> / {{ session.max_selections }}
                    </span>
                    <span v-if="session.submitted_at" class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        Submitted & Locked
                    </span>
                    <span v-else class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                        In Progress
                    </span>
                </div>
            </div>

            <div class="p-6">
                <ul v-if="session.photo_selections?.length > 0" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    <li v-for="sel in session.photo_selections" :key="sel.id" class="p-4 bg-white rounded-lg border border-slate-200 shadow-sm flex flex-col justify-center">
                        <span class="text-sm font-medium text-slate-900 break-all">{{ sel.file_name }}</span>
                        <a :href="'https://drive.google.com/file/d/' + sel.drive_file_id + '/view'" target="_blank" class="mt-2 text-xs text-blue-600 hover:underline">
                            View on Drive &rarr;
                        </a>
                    </li>
                </ul>
                <div v-else class="text-center py-12 text-slate-500">
                    No selections have been made yet.
                </div>
            </div>
        </div>

        <Modal :show="isResetModalOpen" @close="isResetModalOpen = false" maxWidth="sm">
            <div class="p-6">
                <h2 class="text-lg font-medium text-slate-900 mb-4">Confirm Reset</h2>
                <p class="text-sm text-slate-600 mb-6">
                    Are you sure you want to reset all selections? This will delete all current selections made by the customer and unlock the session so they can pick again.
                </p>
                <div class="flex justify-end gap-3">
                    <SecondaryButton @click="isResetModalOpen = false">Cancel</SecondaryButton>
                    <DangerButton @click="resetSelections" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Reset Selections
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </AdminLayout>
</template>
