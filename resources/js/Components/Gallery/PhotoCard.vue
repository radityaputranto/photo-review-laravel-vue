<script setup>
import { CheckCircleIcon } from '@heroicons/vue/24/solid'
import { ArrowPathIcon, EyeIcon } from '@heroicons/vue/24/outline'
import { ref } from 'vue'

const props = defineProps({
    photo: Object,
    isSelected: Boolean,
    isLoading: Boolean,
    disabled: Boolean,
})

const emit = defineEmits(['toggle'])

// Gunakan Intersection Observer / lazy loading native HTML
const imageLoaded = ref(false)
</script>

<template>
    <div 
        class="group relative aspect-[4/5] sm:aspect-square bg-slate-100 rounded-xl overflow-hidden cursor-pointer border-2 transition-all duration-200"
        :class="isSelected ? 'border-blue-500 shadow-md scale-[0.98]' : 'border-transparent hover:shadow-md'"
        @click="!disabled || isSelected ? emit('toggle') : null"
    >
        <!-- Image -->
        <img 
            :src="photo.thumbnail" 
            :alt="photo.name"
            loading="lazy"
            @load="imageLoaded = true"
            class="w-full h-full object-cover transition-opacity duration-300"
            :class="[
                imageLoaded ? 'opacity-100' : 'opacity-0',
                isSelected ? 'brightness-110' : 'brightness-100 group-hover:brightness-95'
            ]"
        />

        <!-- Loading Skeleton -->
        <div v-if="!imageLoaded" class="absolute inset-0 bg-slate-200 animate-pulse"></div>

        <!-- Overlay Gradient -->
        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-black/10 opacity-0 group-hover:opacity-100 transition-opacity duration-200" :class="{ 'opacity-100': isSelected }"></div>

        <!-- Checkmark Badge -->
        <div 
            class="absolute top-3 right-3 w-8 h-8 rounded-full flex items-center justify-center transition-transform duration-200"
            :class="[
                isSelected ? 'bg-blue-500 scale-100' : 'bg-white/50 backdrop-blur-sm scale-0 group-hover:scale-100',
                { 'opacity-50': disabled && !isSelected }
            ]"
        >
            <ArrowPathIcon v-if="isLoading" class="w-5 h-5 text-white animate-spin" />
            <CheckCircleIcon v-else class="w-8 h-8 text-white drop-shadow-sm" :class="{ 'text-slate-300': !isSelected }" />
        </div>

        <!-- File Name & Preview Button -->
        <div class="absolute bottom-0 inset-x-0 p-3 flex items-end justify-between opacity-0 group-hover:opacity-100 transition-opacity duration-200" :class="{ 'opacity-100': isSelected }">
            <p class="text-xs font-medium text-white truncate drop-shadow-md pr-2">
                {{ photo.name }}
            </p>
            <a 
                :href="photo.view_url" 
                target="_blank" 
                @click.stop
                class="shrink-0 w-8 h-8 bg-white/20 hover:bg-white/40 backdrop-blur-md rounded-full flex items-center justify-center transition-colors"
                title="Preview Penuh"
            >
                <EyeIcon class="w-4 h-4 text-white" />
            </a>
        </div>
    </div>
</template>
