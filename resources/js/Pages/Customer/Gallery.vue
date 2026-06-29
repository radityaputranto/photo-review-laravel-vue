<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import CustomerLayout from '@/Layouts/CustomerLayout.vue'
import { ArrowLeftIcon, CheckCircleIcon, ExclamationCircleIcon } from '@heroicons/vue/24/outline'
import StickyBar from '@/Components/Gallery/StickyBar.vue'
import PhotoCard from '@/Components/Gallery/PhotoCard.vue'
import { ref } from 'vue'

const props = defineProps({
    session: Object,
    photos: Array,
    selectedIds: Array,
    submitted: Boolean,
})

const selected = ref([...props.selectedIds])
const loadingToggleId = ref(null)

const toggleSelection = async (photo) => {
    if (props.submitted) return
    
    // Check limit
    if (!selected.value.includes(photo.id) && selected.value.length >= props.session.max_selections) {
        alert('Anda telah mencapai batas maksimal pemilihan foto (' + props.session.max_selections + ').')
        return
    }

    loadingToggleId.value = photo.id

    try {
        const response = await fetch(route('customer.sessions.select', props.session.id), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify({ 
                drive_file_id: photo.id,
                file_name: photo.name 
            })
        })
        
        const data = await response.json()
        
        if (data.status === 'attached') {
            selected.value.push(photo.id)
        } else if (data.status === 'detached') {
            selected.value = selected.value.filter(id => id !== photo.id)
        }
    } catch (error) {
        console.error('Failed to toggle photo selection')
        alert('Terjadi kesalahan saat menyimpan pilihan. Silakan coba lagi.')
    } finally {
        loadingToggleId.value = null
    }
}
</script>

<template>
    <Head :title="session.title + ' - Gallery'" />

    <CustomerLayout>
        <div class="mb-6">
            <Link :href="route('customer.dashboard')" class="inline-flex items-center text-sm font-medium text-slate-500 hover:text-slate-900 mb-4 transition-colors">
                <ArrowLeftIcon class="w-4 h-4 mr-1" />
                Kembali ke Dashboard
            </Link>
            <h1 class="text-3xl font-bold text-slate-900">{{ session.title }}</h1>
            <div class="flex items-center gap-4 mt-2">
                <p class="text-slate-600">
                    Batas Pilihan: <span class="font-bold text-slate-900">{{ session.max_selections }} foto</span>
                </p>
                <div v-if="submitted" class="flex items-center gap-1 text-amber-600 bg-amber-50 px-2 py-1 rounded-md text-sm font-medium">
                    <CheckCircleIcon class="w-4 h-4" />
                    Sesi telah dikunci
                </div>
            </div>
        </div>

        <div v-if="!photos.length" class="bg-white rounded-xl shadow-sm border border-slate-200 p-12 text-center text-slate-500">
            <ExclamationCircleIcon class="w-12 h-12 mx-auto text-slate-300 mb-3" />
            <p>Tidak ada foto ditemukan di folder ini atau folder tidak dapat diakses.</p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-24" v-else>
            <PhotoCard
                v-for="photo in photos"
                :key="photo.id"
                :photo="photo"
                :is-selected="selected.includes(photo.id)"
                :is-loading="loadingToggleId === photo.id"
                :disabled="submitted || (selected.length >= session.max_selections && !selected.includes(photo.id))"
                @toggle="toggleSelection(photo)"
            />
        </div>
    </CustomerLayout>

    <!-- Sticky Bottom Bar -->
    <StickyBar 
        :current-count="selected.length"
        :max-count="session.max_selections"
        :session-id="session.id"
        :is-submitted="submitted"
    />
</template>
