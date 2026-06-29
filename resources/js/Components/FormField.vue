<script setup>
import { computed } from 'vue';

const props = defineProps({
    id: { type: String, required: true },
    label: { type: String, required: true },
    type: { type: String, default: 'text' },
    modelValue: { type: [String, Number], default: '' },
    error: { type: String, default: '' },
    required: { type: Boolean, default: false },
    placeholder: { type: String, default: '' },
    autofocus: { type: Boolean, default: false },
    autocomplete: { type: String, default: '' },
    min: { type: [String, Number], default: '' },
    max: { type: [String, Number], default: '' }
});

const emit = defineEmits(['update:modelValue', 'blur']);

const hasError = computed(() => !!props.error);
</script>

<template>
    <div>
        <label :for="id" class="block text-sm font-medium transition-colors" :class="hasError ? 'text-red-600' : 'text-gray-700'">
            {{ label }} <span v-if="required" class="text-red-500">*</span>
        </label>
        
        <div v-if="$slots.default" class="mt-1 flex">
            <slot :has-error="hasError" :error-class="hasError ? 'ring-2 ring-red-500 focus:ring-red-500' : 'ring-1 ring-slate-200 focus:ring-2 focus:ring-blue-600'" />
        </div>
        <input
            v-else
            :id="id"
            :type="type"
            :value="modelValue"
            @input="emit('update:modelValue', $event.target.value)"
            @blur="emit('blur', $event)"
            class="mt-1 block w-full h-12 px-4 rounded-xl transition-all bg-slate-50 text-slate-900 text-base placeholder:text-slate-400"
            :class="[
                hasError 
                    ? 'border-0 ring-2 ring-red-500 focus:ring-red-500 text-red-900' 
                    : 'border-0 ring-1 ring-slate-200 focus:ring-2 focus:ring-blue-600 focus:bg-white'
            ]"
            :placeholder="placeholder"
            :autofocus="autofocus"
            :autocomplete="autocomplete"
            :min="min"
            :max="max"
        />
        
        <p v-if="hasError" class="mt-2 text-sm text-red-600">
            {{ error }}
        </p>
    </div>
</template>
