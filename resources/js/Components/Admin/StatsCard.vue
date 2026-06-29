<script setup>
defineProps({
    title: { type: String, required: true },
    value: { type: [String, Number], required: true },
    icon: { type: Object, required: false },
    color: { type: String, default: 'blue' }
})
</script>

<template>
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 flex items-center gap-4">
        <div 
            class="w-12 h-12 rounded-lg flex items-center justify-center flex-shrink-0"
            :class="`bg-${color}-100 text-${color}-600`"
        >
            <component v-if="icon" :is="icon" class="w-6 h-6" />
        </div>
        <div>
            <p class="text-sm font-medium text-slate-500">{{ title }}</p>
            <p class="text-2xl font-bold text-slate-900">{{ value }}</p>
        </div>
    </div>
</template>
