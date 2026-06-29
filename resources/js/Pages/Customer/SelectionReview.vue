<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import CustomerLayout from '@/Layouts/CustomerLayout.vue'
import { ArrowLeftIcon, CheckCircleIcon, ExclamationTriangleIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    session: Object,
    selections: Array,
    submitted: Boolean,
})

const form = useForm({})

const submitSelections = () => {
    if (confirm('Apakah Anda yakin ingin mengirim pilihan ini? Anda tidak dapat mengubahnya lagi setelah dikirim.')) {
        form.post(route('customer.sessions.submit', props.session.id))
    }
}
</script>

<template>
    <Head :title="session.title + ' - Review Pilihan'" />

    <CustomerLayout>
        <div class="mb-6">
            <Link :href="route('customer.sessions.gallery', session.id)" class="inline-flex items-center text-sm font-medium text-slate-500 hover:text-slate-900 mb-4 transition-colors">
                <ArrowLeftIcon class="w-4 h-4 mr-1" />
                Kembali ke Galeri
            </Link>
            <h1 class="text-3xl font-bold text-slate-900">Review Pilihan: {{ session.title }}</h1>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mb-8">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Daftar Foto Terpilih</h2>
                    <p class="text-slate-500 text-sm mt-1">
                        Total: <span class="font-bold text-slate-900">{{ selections.length }}</span> / {{ session.max_selections }}
                    </p>
                </div>
                
                <div v-if="submitted" class="flex items-center gap-2 text-green-600 bg-green-50 px-4 py-2 rounded-lg font-medium">
                    <CheckCircleIcon class="w-5 h-5" />
                    Telah Dikirim
                </div>
            </div>
            
            <div class="p-6">
                <ul v-if="selections.length > 0" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                    <li v-for="sel in selections" :key="sel.id" class="p-3 bg-slate-50 rounded-lg border border-slate-200 flex flex-col items-center justify-center text-center">
                        <span class="text-sm font-medium text-slate-700 break-all">{{ sel.file_name }}</span>
                    </li>
                </ul>
                <div v-else class="text-center py-8 text-slate-500">
                    Belum ada foto yang dipilih.
                </div>
            </div>

            <!-- Submit Section -->
            <div v-if="!submitted" class="p-6 bg-slate-50 border-t border-slate-100">
                <div v-if="selections.length < session.max_selections" class="flex items-start gap-3 p-4 bg-amber-50 text-amber-800 rounded-lg mb-4">
                    <ExclamationTriangleIcon class="w-5 h-5 shrink-0 mt-0.5" />
                    <p class="text-sm font-medium">
                        Anda baru memilih {{ selections.length }} dari {{ session.max_selections }} foto. Harap lengkapi pilihan Anda sebelum mengirim.
                    </p>
                </div>
                <div v-else class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <p class="text-sm text-slate-600">
                        Pastikan pilihan Anda sudah benar. Setelah dikirim, sesi akan dikunci.
                    </p>
                    <button 
                        @click="submitSelections"
                        :disabled="form.processing"
                        class="w-full sm:w-auto px-6 py-3 bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white font-semibold rounded-xl shadow-sm transition-colors"
                    >
                        {{ form.processing ? 'Mengirim...' : 'Kirim Pilihan Final' }}
                    </button>
                </div>
            </div>
        </div>
    </CustomerLayout>
</template>
