<script setup>
import { Link } from '@inertiajs/vue3'
import { CheckIcon } from '@heroicons/vue/24/solid'
import { computed } from 'vue'

const props = defineProps({
    currentCount: Number,
    maxCount: Number,
    sessionId: [String, Number],
    isSubmitted: Boolean,
})

const percentage = computed(() => {
    return Math.min(Math.round((props.currentCount / props.maxCount) * 100), 100)
})

const isComplete = computed(() => props.currentCount >= props.maxCount)
</script>

<template>
    <div class="fixed bottom-0 inset-x-0 z-40">
        <!-- Progress Bar (top border of sticky bar) -->
        <div class="h-1.5 w-full bg-slate-200">
            <div 
                class="h-full bg-blue-500 transition-all duration-300 ease-out"
                :style="{ width: percentage + '%' }"
                :class="{ 'bg-green-500': isComplete }"
            ></div>
        </div>

        <!-- Bar Content -->
        <div class="bg-white/90 backdrop-blur-md border-t border-slate-200 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)]">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500">Terpilih</p>
                    <p class="text-xl font-bold text-slate-900 flex items-baseline gap-1">
                        {{ currentCount }}
                        <span class="text-sm font-medium text-slate-400">/ {{ maxCount }} foto</span>
                    </p>
                </div>

                <div class="flex items-center gap-4">
                    <span v-if="isComplete && !isSubmitted" class="hidden sm:flex items-center text-sm font-medium text-green-600">
                        <CheckIcon class="w-5 h-5 mr-1" />
                        Pilihan Selesai
                    </span>
                    <Link
                        v-if="!isSubmitted"
                        :href="route('customer.sessions.review', sessionId)"
                        class="inline-flex items-center px-6 py-2.5 rounded-xl text-sm font-medium transition-colors shadow-sm"
                        :class="isComplete 
                            ? 'bg-blue-600 text-white hover:bg-blue-700' 
                            : 'bg-white text-slate-700 border border-slate-300 hover:bg-slate-50'"
                    >
                        Review & Kirim
                    </Link>
                    <span v-else class="inline-flex items-center px-6 py-2.5 rounded-xl text-sm font-medium bg-slate-100 text-slate-500">
                        Sesi Terkunci
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>
