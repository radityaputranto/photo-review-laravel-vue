<script setup>
import { Head, Link } from '@inertiajs/vue3'
import CustomerLayout from '@/Layouts/CustomerLayout.vue'
import { PhotoIcon, CalendarIcon } from '@heroicons/vue/24/outline'

defineProps({
    activeSessions: Array,
    completedSessions: Array,
})
</script>

<template>
    <Head title="My Dashboard" />

    <CustomerLayout>
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-slate-900">Selamat Datang, {{ $page.props.auth.user.name }}!</h1>
            <p class="text-slate-600 mt-2">Berikut adalah daftar sesi foto Anda yang perlu direview.</p>
        </div>

        <!-- Active Sessions -->
        <h2 class="text-xl font-semibold text-slate-900 mb-4">Sesi Aktif (Perlu Review)</h2>
        
        <div v-if="!activeSessions.length" class="bg-white rounded-xl shadow-sm border border-slate-200 p-8 text-center text-slate-500 mb-8">
            <PhotoIcon class="w-12 h-12 mx-auto text-slate-300 mb-3" />
            <p>Tidak ada sesi foto aktif saat ini.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8" v-else>
            <div 
                v-for="session in activeSessions" 
                :key="session.id"
                class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-md transition-shadow flex flex-col"
            >
                <div class="p-6 flex-1">
                    <div class="flex items-center gap-2 text-sm text-slate-500 mb-2">
                        <CalendarIcon class="w-4 h-4" />
                        {{ session.shoot_date }}
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">{{ session.title }}</h3>
                    <p class="text-sm text-slate-600">
                        Max Pilihan: <span class="font-medium text-slate-900">{{ session.max_selections }} foto</span>
                    </p>
                </div>
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100">
                    <Link
                        :href="route('customer.sessions.gallery', session.id)"
                        class="block w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 text-white font-medium text-sm text-center rounded-lg transition-colors"
                    >
                        Review Foto Sekarang
                    </Link>
                </div>
            </div>
        </div>

        <!-- Completed Sessions -->
        <h2 class="text-xl font-semibold text-slate-900 mb-4">Sesi Terdahulu</h2>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div v-if="!completedSessions.length" class="p-8 text-center text-slate-500">
                Belum ada riwayat sesi foto.
            </div>
            
            <ul class="divide-y divide-slate-100" v-else>
                <li v-for="session in completedSessions" :key="session.id" class="p-4 sm:px-6 hover:bg-slate-50 transition-colors flex items-center justify-between">
                    <div>
                        <p class="font-medium text-slate-900">{{ session.title }}</p>
                        <p class="text-sm text-slate-500">{{ session.shoot_date }}</p>
                    </div>
                    <div class="flex flex-col items-end gap-2">
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium"
                            :class="session.status === 'delivered' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'"
                        >
                            {{ session.status === 'delivered' ? 'Selesai & Dikirim' : 'Menunggu Edit' }}
                        </span>
                        <a v-if="session.download_link" :href="session.download_link" target="_blank" class="text-sm font-medium text-blue-600 hover:underline">
                            Download Hasil
                        </a>
                    </div>
                </li>
            </ul>
        </div>
    </CustomerLayout>
</template>
