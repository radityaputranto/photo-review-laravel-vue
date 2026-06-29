<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { useFormatDate } from '@/Composables/useFormatDate'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { PlusIcon, EyeIcon, PencilIcon, TrashIcon, CalendarIcon, UserIcon, MagnifyingGlassIcon } from '@heroicons/vue/24/outline'
import Modal from '@/Components/Modal.vue'
import DangerButton from '@/Components/DangerButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import { ref, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'

const { formatDate } = useFormatDate()

const props = defineProps({
    sessions: Object,
    filters: Object,
})

const isDeleteModalOpen = ref(false)
const deletingSession = ref(null)
const form = useForm({})

const searchQuery = ref(props.filters?.search || "")
let searchTimeout = null
watch(searchQuery, (value) => {
    if (searchTimeout) clearTimeout(searchTimeout)
    searchTimeout = setTimeout(() => {
        router.get(route('admin.sessions.index'), { search: value }, {
            preserveState: true,
            replace: true
        })
    }, 300)
})

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
        <!-- Top Header -->
        <header class="max-w-7xl mx-auto mb-8">
            <div class="flex justify-between items-end mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Sessions</h1>
                    <p class="text-base text-slate-500 mt-1">Manage and track your photography projects</p>
                </div>
                <Link :href="route('admin.sessions.create')" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl text-sm font-medium flex items-center gap-2 transition-all shadow-sm hover:shadow-md hover:scale-[1.02] active:scale-95">
                    <PlusIcon class="w-5 h-5" />
                    New Session
                </Link>
            </div>
            
            <div class="flex flex-wrap gap-4 items-center bg-white p-4 rounded-xl shadow-sm border border-slate-100">
                <div class="relative flex-grow max-w-md">
                    <MagnifyingGlassIcon class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" />
                    <input type="text" v-model="searchQuery" class="w-full h-12 pl-11 pr-4 bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-blue-600 text-base placeholder:text-slate-400 transition-all" placeholder="Search sessions..." />
                </div>
            </div>
        </header>

        <!-- Sessions List Content -->
        <section class="max-w-7xl mx-auto space-y-4 mb-12">
            <template v-if="sessions.data.length">
                <!-- Session Card -->
                <div v-for="session in sessions.data" :key="session.id" class="group bg-white p-6 rounded-xl shadow-sm border border-transparent hover:border-slate-200 transition-all hover:scale-[1.01] hover:shadow-md flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="flex-1 min-w-0 md:pr-8">
                        <h3 class="text-xl font-semibold text-slate-900 mb-1 truncate">{{ session.title }}</h3>
                        <div class="flex flex-wrap items-center gap-4 text-slate-500 text-sm">
                            <span class="flex items-center gap-1.5" v-if="session.customer">
                                <UserIcon class="w-4 h-4" />
                                {{ session.customer.name }}
                            </span>
                            <span class="flex items-center gap-1.5">
                                <CalendarIcon class="w-4 h-4" />
                                {{ formatDate(session.shoot_date) }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="flex-1 flex flex-wrap md:flex-nowrap items-center justify-start md:justify-end gap-6 md:pl-8 md:border-l border-slate-100">
                        <span class="px-4 py-1.5 rounded-full text-xs font-semibold flex items-center gap-1.5 capitalize"
                            :class="{
                                'bg-blue-50 text-blue-700': session.status === 'active',
                                'bg-slate-100 text-slate-700': session.status === 'completed',
                                'bg-green-50 text-green-700': session.status === 'delivered',
                            }"
                        >
                            <span v-if="session.status === 'active'" class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                            {{ session.status }}
                        </span>
                        
                        <div class="flex items-center gap-2">
                            <Link :href="route('admin.sessions.show', session.id)" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 font-medium text-sm hover:bg-slate-50 transition-colors">
                                View Detail
                            </Link>
                            <Link :href="route('admin.sessions.edit', session.id)" class="p-2.5 rounded-xl text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-colors" title="Edit">
                                <PencilIcon class="w-5 h-5" />
                            </Link>
                            <button @click="confirmDelete(session)" class="p-2.5 rounded-xl text-slate-400 hover:text-red-600 hover:bg-red-50 transition-colors" title="Delete">
                                <TrashIcon class="w-5 h-5" />
                            </button>
                        </div>
                    </div>
                </div>
            </template>
            
            <div v-else class="bg-white p-12 rounded-xl shadow-sm text-center border border-slate-100">
                <p class="text-slate-500 font-medium">No photo sessions found.</p>
            </div>
            
            <!-- Pagination -->
            <div class="mt-8 flex justify-center" v-if="sessions.links.length > 3">
                <div class="flex items-center gap-1">
                    <template v-for="(link, k) in sessions.links" :key="k">
                        <div
                            v-if="link.url === null"
                            class="px-3 py-1 text-sm text-slate-400 border border-slate-100 rounded-lg bg-white"
                            v-html="link.label"
                        />
                        <Link
                            v-else
                            :href="link.url"
                            class="px-3 py-1 text-sm border rounded-lg transition-colors"
                            :class="link.active ? 'bg-blue-50 border-blue-200 text-blue-700 font-medium shadow-sm' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50'"
                            v-html="link.label"
                        />
                    </template>
                </div>
            </div>
        </section>

        <!-- Delete Confirmation Modal -->
        <Modal :show="isDeleteModalOpen" @close="isDeleteModalOpen = false" maxWidth="sm">
            <div class="p-6">
                <h2 class="text-lg font-bold text-slate-900 mb-2">Confirm Deletion</h2>
                <p class="text-sm text-slate-500 mb-6 leading-relaxed">
                    Are you sure you want to delete this session? This action cannot be undone and will remove all associated selections.
                </p>
                <div class="flex justify-end gap-3">
                    <SecondaryButton @click="isDeleteModalOpen = false" class="rounded-xl">Cancel</SecondaryButton>
                    <DangerButton @click="deleteSession" class="rounded-xl" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Delete Session
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </AdminLayout>
</template>
