<script setup>
import { Head, Link } from '@inertiajs/vue3'
import CustomerLayout from '@/Layouts/CustomerLayout.vue'
import { DocumentTextIcon, DocumentIcon, CurrencyDollarIcon, EyeIcon, ArrowDownTrayIcon } from '@heroicons/vue/24/outline'

defineProps({
    documents: Array,
})

const getIcon = (type) => {
    if (type === 'invoice') return CurrencyDollarIcon
    if (type === 'contract') return DocumentTextIcon
    return DocumentIcon
}
</script>

<template>
    <Head title="Dokumen Saya" />

    <CustomerLayout>
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-slate-900">Dokumen</h1>
            <p class="text-slate-600 mt-2">Invoice, kontrak, dan dokumen penting lainnya dari layanan fotografi Anda.</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div v-if="!documents.length" class="p-12 text-center text-slate-500">
                <DocumentTextIcon class="w-12 h-12 mx-auto text-slate-300 mb-3" />
                <p>Belum ada dokumen yang tersedia untuk Anda.</p>
            </div>

            <ul class="divide-y divide-slate-100" v-else>
                <li v-for="doc in documents" :key="doc.id" class="p-4 sm:p-6 hover:bg-slate-50 transition-colors flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                            <component :is="getIcon(doc.type)" class="w-6 h-6" />
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900">{{ doc.title }}</h3>
                            <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-1 text-sm text-slate-500">
                                <span class="uppercase tracking-wider text-xs font-semibold">{{ doc.type }}</span>
                                <span v-if="doc.session">&bull; Sesi: {{ doc.session.title }}</span>
                                <span>&bull; {{ new Date(doc.created_at).toLocaleDateString() }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-2 mt-2 sm:mt-0 sm:shrink-0">
                        <a 
                            :href="'https://drive.google.com/file/d/' + doc.drive_file_id + '/view'" 
                            target="_blank"
                            class="inline-flex items-center justify-center px-4 py-2 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 bg-white hover:bg-slate-50 transition-colors w-full sm:w-auto"
                        >
                            <EyeIcon class="w-4 h-4 mr-2" />
                            Buka di Google Drive
                        </a>
                    </div>
                </li>
            </ul>
        </div>
    </CustomerLayout>
</template>
