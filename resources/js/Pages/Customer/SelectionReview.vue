<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import CustomerLayout from '@/Layouts/CustomerLayout.vue'
import { ArrowLeftIcon, CheckCircleIcon, ExclamationTriangleIcon } from '@heroicons/vue/24/outline'
import Modal from '@/Components/Modal.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import { ref } from 'vue'

const props = defineProps({
    session: Object,
    selections: Array,
    submitted: Boolean,
})

const form = useForm({})
const isConfirmModalOpen = ref(false)

const submitSelections = () => {
    isConfirmModalOpen.value = true
}

const confirmSubmit = () => {
    form.post(route('customer.sessions.submit', props.session.id), {
        onSuccess: () => {
            isConfirmModalOpen.value = false
        }
    })
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
                    <li v-for="sel in selections" :key="sel.id" class="bg-white rounded-lg border border-slate-200 overflow-hidden flex flex-col shadow-sm hover:shadow transition-shadow">
                        <div class="aspect-square bg-slate-100 relative group">
                            <img v-if="sel.thumbnail_url"
                                 :src="sel.thumbnail_url" 
                                 :alt="sel.file_name"
                                 class="w-full h-full object-cover" 
                                 loading="lazy" />
                            <div v-else class="w-full h-full flex items-center justify-center text-slate-400">
                                <span class="text-xs">No preview</span>
                            </div>
                        </div>
                        <div class="p-3 text-center border-t border-slate-100 bg-slate-50">
                            <span class="text-sm font-medium text-slate-700 break-all line-clamp-1" :title="sel.file_name">{{ sel.file_name }}</span>
                        </div>
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
                        Anda baru memilih {{ selections.length }} dari {{ session.max_selections }} foto. Anda tetap bisa mengirim pilihan jika sudah selesai.
                    </p>
                </div>
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <p class="text-sm text-slate-600">
                        Pastikan pilihan Anda sudah benar. Setelah dikirim, sesi akan dikunci.
                    </p>
                    <button 
                        @click="submitSelections"
                        :disabled="form.processing || selections.length === 0"
                        class="w-full sm:w-auto px-6 py-3 bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white font-semibold rounded-xl shadow-sm transition-colors"
                    >
                        {{ form.processing ? 'Mengirim...' : 'Kirim Pilihan Final' }}
                    </button>
                </div>
            </div>
        </div>


        <!-- Confirm Modal -->
        <Modal :show="isConfirmModalOpen" @close="isConfirmModalOpen = false" maxWidth="sm">
            <div class="p-6 text-center">
                <ExclamationTriangleIcon class="mx-auto mb-4 text-slate-400 w-12 h-12" />
                <h3 class="mb-5 text-lg font-normal text-slate-500">Apakah Anda yakin ingin mengirim pilihan ini? Anda tidak dapat mengubahnya lagi setelah dikirim.</h3>
                <div class="flex justify-center gap-3">
                    <button 
                        @click="confirmSubmit"
                        :disabled="form.processing"
                        class="text-white bg-blue-600 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center disabled:opacity-50"
                    >
                        Ya, Kirim Pilihan
                    </button>
                    <SecondaryButton @click="isConfirmModalOpen = false">
                        Batal
                    </SecondaryButton>
                </div>
            </div>
        </Modal>
    </CustomerLayout>
</template>
