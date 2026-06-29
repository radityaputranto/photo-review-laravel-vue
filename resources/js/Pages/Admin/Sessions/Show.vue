<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { useFormatDate } from '@/Composables/useFormatDate'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { ArrowLeftIcon, PencilIcon, CheckCircleIcon, DocumentCheckIcon } from '@heroicons/vue/24/outline'

const { formatDate } = useFormatDate()
const props = defineProps({
    session: Object,
    totalPhotos: Number,
})
</script>

<template>
    <Head :title="session.title" />

    <AdminLayout>
        <div class="mb-6 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <Link :href="route('admin.sessions.index')" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-full transition-colors">
                    <ArrowLeftIcon class="w-5 h-5" />
                </Link>
                <div>
                    <h1 class="text-2xl font-bold text-slate-900">{{ session.title }}</h1>
                    <p class="text-slate-500 mt-1">Customer: {{ session.customer?.name }} ({{ session.customer?.email }})</p>
                </div>
            </div>
            
            <div class="flex items-center gap-3">
                <Link :href="route('admin.sessions.edit', session.id)">
                    <button class="inline-flex items-center px-4 py-2 bg-white border border-slate-300 rounded-md font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                        <PencilIcon class="w-4 h-4 mr-2" />
                        Edit Session
                    </button>
                </Link>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Col: Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Info Card -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden p-6">
                    <h2 class="text-lg font-semibold text-slate-900 mb-4">Session Details</h2>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-6">
                        <div>
                            <dt class="text-sm font-medium text-slate-500">Shoot Date</dt>
                            <dd class="mt-1 text-base text-slate-900">{{ formatDate(session.shoot_date) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-slate-500">Status</dt>
                            <dd class="mt-1">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium capitalize"
                                    :class="{
                                        'bg-amber-100 text-amber-800': session.status === 'active',
                                        'bg-blue-100 text-blue-800': session.status === 'completed',
                                        'bg-green-100 text-green-800': session.status === 'delivered',
                                    }"
                                >
                                    {{ session.status }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-slate-500">Google Drive Source</dt>
                            <dd class="mt-1 text-sm text-blue-600 hover:underline break-all">
                                <a :href="session.drive_folder_url" target="_blank">{{ session.drive_folder_url }}</a>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-slate-500">Final Download Link</dt>
                            <dd class="mt-1 text-sm">
                                <a v-if="session.download_link" :href="session.download_link" target="_blank" class="text-blue-600 hover:underline break-all">
                                    {{ session.download_link }}
                                </a>
                                <span v-else class="text-slate-400">Not provided yet</span>
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Right Col: Stats & Actions -->
            <div class="space-y-6">
                <!-- Selections Stats Card -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden p-6">
                    <h2 class="text-lg font-semibold text-slate-900 mb-4">Selections Overview</h2>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between items-center pb-4 border-b border-slate-100">
                            <span class="text-sm text-slate-600">Selected Photos</span>
                            <span class="text-lg font-bold text-slate-900">
                                {{ session.photo_selections?.length || 0 }} / {{ session.max_selections }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center pb-4 border-b border-slate-100">
                            <span class="text-sm text-slate-600">Drive Photos Detected</span>
                            <span class="text-lg font-bold text-slate-900">{{ totalPhotos }}</span>
                        </div>
                        <div class="flex justify-between items-center pb-4 border-b border-slate-100">
                            <span class="text-sm text-slate-600">Submitted</span>
                            <div class="flex items-center gap-1" :class="session.submitted_at ? 'text-green-600' : 'text-amber-600'">
                                <CheckCircleIcon class="w-5 h-5" />
                                <span class="font-medium">{{ session.submitted_at ? 'Yes' : 'No' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6">
                        <Link :href="route('admin.sessions.selections', session.id)">
                            <button class="w-full inline-flex justify-center items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-medium text-white hover:bg-blue-700 transition-colors">
                                <DocumentCheckIcon class="w-5 h-5 mr-2" />
                                View Customer Selections
                            </button>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
