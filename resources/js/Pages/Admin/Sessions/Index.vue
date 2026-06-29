<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import { PlusIcon, EyeIcon, PencilIcon, TrashIcon, CalendarIcon } from '@heroicons/vue/24/outline'
import Modal from '@/Components/Modal.vue'
import DangerButton from '@/Components/DangerButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'

defineProps({
    sessions: Object,
})

const isDeleteModalOpen = ref(false)
const deletingSession = ref(null)
const form = useForm({})

const confirmDelete = (session) => {
    deletingSession.value = session
    isDeleteModalOpen.value = true
}

const deleteSession = () => {
    form.delete(route('admin.sessions.destroy', deletingSession.value.id), {
        onSuccess: () => {
            isDeleteModalOpen.value = false
            deletingSession.value = null
        }
    })
}
</script>

<template>
    <Head title="Photo Sessions" />

    <AdminLayout>
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-slate-900">Photo Sessions</h1>
            <Link :href="route('admin.sessions.create')">
                <PrimaryButton>
                    <PlusIcon class="w-5 h-5 mr-2" />
                    New Session
                </PrimaryButton>
            </Link>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Session</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Shoot Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    <tr v-for="session in sessions.data" :key="session.id">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-medium text-slate-900">{{ session.title }}</div>
                            <div class="text-xs text-slate-500 max-w-[200px] truncate" :title="session.drive_folder_url">
                                {{ session.drive_folder_url }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-slate-600">
                            {{ session.customer?.name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-slate-600">
                            <div class="flex items-center gap-1.5">
                                <CalendarIcon class="w-4 h-4 text-slate-400" />
                                {{ session.shoot_date }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium capitalize"
                                :class="{
                                    'bg-amber-100 text-amber-800': session.status === 'active',
                                    'bg-blue-100 text-blue-800': session.status === 'completed',
                                    'bg-green-100 text-green-800': session.status === 'delivered',
                                }"
                            >
                                {{ session.status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <Link :href="route('admin.sessions.show', session.id)" class="text-slate-600 hover:text-slate-900 mr-3" title="View Details">
                                <EyeIcon class="w-5 h-5 inline-block" />
                            </Link>
                            <Link :href="route('admin.sessions.edit', session.id)" class="text-blue-600 hover:text-blue-900 mr-3" title="Edit Session">
                                <PencilIcon class="w-5 h-5 inline-block" />
                            </Link>
                            <button @click="confirmDelete(session)" class="text-red-600 hover:text-red-900" title="Delete Session">
                                <TrashIcon class="w-5 h-5 inline-block" />
                            </button>
                        </td>
                    </tr>
                    <tr v-if="!sessions.data.length">
                        <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                            No photo sessions found.
                        </td>
                    </tr>
                </tbody>
            </table>
            
            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50" v-if="sessions.links.length > 3">
                <div class="flex items-center gap-1">
                    <template v-for="(link, k) in sessions.links" :key="k">
                        <div
                            v-if="link.url === null"
                            class="px-3 py-1 text-sm text-slate-400 border border-slate-200 rounded bg-white"
                            v-html="link.label"
                        />
                        <Link
                            v-else
                            :href="link.url"
                            class="px-3 py-1 text-sm border border-slate-200 rounded transition-colors hover:bg-slate-100"
                            :class="link.active ? 'bg-blue-50 border-blue-200 text-blue-600 font-medium' : 'bg-white text-slate-600'"
                            v-html="link.label"
                        />
                    </template>
                </div>
            </div>
        </div>

        <Modal :show="isDeleteModalOpen" @close="isDeleteModalOpen = false" maxWidth="sm">
            <div class="p-6">
                <h2 class="text-lg font-medium text-slate-900 mb-4">Confirm Deletion</h2>
                <p class="text-sm text-slate-600 mb-6">
                    Are you sure you want to delete this session? This will remove all associated selections.
                </p>
                <div class="flex justify-end gap-3">
                    <SecondaryButton @click="isDeleteModalOpen = false">Cancel</SecondaryButton>
                    <DangerButton @click="deleteSession" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Delete
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </AdminLayout>
</template>
