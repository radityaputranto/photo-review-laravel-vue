<script setup>
import { Head, useForm } from "@inertiajs/vue3";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import Modal from "@/Components/Modal.vue";
import FormField from "@/Components/FormField.vue";
import {
    PlusIcon,
    TrashIcon,
    DocumentTextIcon,
    DocumentIcon,
    CurrencyDollarIcon,
    MagnifyingGlassIcon,
} from "@heroicons/vue/24/outline";
import { ref, computed } from "vue";
import { useVuelidate } from "@vuelidate/core";
import { useValidationRules } from "@/Composables/useValidationRules";
import { useFormatDate } from "@/Composables/useFormatDate";

const { formatDate } = useFormatDate();

const props = defineProps({
    documents: Object,
    customers: Array,
    sessions: Array,
});

const isModalOpen = ref(false);
const isDeleteModalOpen = ref(false);
const deletingDocument = ref(null);

const form = useForm({
    customer_id: "",
    session_id: "",
    type: "invoice",
    title: "",
    drive_url: "",
});

const { rules } = useValidationRules();
const validationRules = computed(() => ({
    title: { required: rules.required },
    customer_id: { required: rules.required },
    type: { required: rules.required },
    drive_url: { required: rules.required, url: rules.url },
}));

const v$ = useVuelidate(validationRules, form);

const filteredSessions = computed(() => {
    if (!form.customer_id) return [];
    return props.sessions.filter((s) => s.customer_id == form.customer_id);
});

const openModal = () => {
    form.reset();
    form.clearErrors();
    v$.value.$reset();
    isModalOpen.value = true;
};

const submit = async () => {
    const isFormValid = await v$.value.$validate();
    if (!isFormValid) return;

    form.post(route("admin.documents.store"), {
        onSuccess: () => {
            isModalOpen.value = false;
            form.reset();
        },
    });
};

const confirmDelete = (doc) => {
    deletingDocument.value = doc;
    isDeleteModalOpen.value = true;
};

const deleteDocument = () => {
    form.delete(route("admin.documents.destroy", deletingDocument.value.id), {
        onSuccess: () => {
            isDeleteModalOpen.value = false;
            deletingDocument.value = null;
        },
    });
};

const getIcon = (type) => {
    if (type === "invoice") return CurrencyDollarIcon;
    if (type === "contract") return DocumentTextIcon;
    return DocumentIcon;
};
</script>

<template>
    <Head title="Documents" />

    <AdminLayout>
        <!-- Top Header -->
        <header class="max-w-7xl mx-auto mb-8">
            <div class="flex justify-between items-end mb-8">
                <div>
                    <h1
                        class="text-3xl font-bold text-slate-900 tracking-tight"
                    >
                        Documents
                    </h1>
                    <p class="text-base text-slate-500 mt-1">
                        Manage invoices and contracts for your clients
                    </p>
                </div>
                <button
                    @click="openModal"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl text-sm font-medium flex items-center gap-2 transition-all shadow-sm hover:shadow-md hover:scale-[1.02] active:scale-95"
                >
                    <PlusIcon class="w-5 h-5" />
                    Add Document
                </button>
            </div>

            <div
                class="flex flex-wrap gap-4 items-center bg-white p-4 rounded-xl shadow-sm border border-slate-100"
            >
                <div class="relative flex-grow max-w-md">
                    <MagnifyingGlassIcon
                        class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400"
                    />
                    <input
                        type="text"
                        class="w-full h-12 pl-11 pr-4 bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-blue-600 text-base placeholder:text-slate-400 transition-all"
                        placeholder="Search documents..."
                    />
                </div>
                <div class="flex items-center gap-3">
                    <label class="text-sm font-medium text-slate-500"
                        >Filter by Type:</label
                    >
                    <select
                        class="h-12 px-4 bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-blue-600 text-base min-w-[160px] cursor-pointer"
                    >
                        <option>All Types</option>
                        <option>Invoice</option>
                        <option>Contract</option>
                        <option>Other</option>
                    </select>
                </div>
            </div>
        </header>

        <section class="max-w-7xl mx-auto space-y-4 mb-12">
            <div
                class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden"
            >
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th
                                class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider"
                            >
                                Document
                            </th>
                            <th
                                class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider"
                            >
                                Customer
                            </th>
                            <th
                                class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider"
                            >
                                Related Session
                            </th>
                            <th
                                class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider"
                            >
                                Date Added
                            </th>
                            <th
                                class="px-6 py-4 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider"
                            >
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-100">
                        <tr
                            v-for="doc in documents.data"
                            :key="doc.id"
                            class="hover:bg-slate-50 transition-colors"
                        >
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center"
                                    >
                                        <component
                                            :is="getIcon(doc.type)"
                                            class="w-5 h-5"
                                        />
                                    </div>
                                    <div>
                                        <div
                                            class="font-semibold text-slate-900"
                                        >
                                            {{ doc.title }}
                                        </div>
                                        <div
                                            class="text-xs font-medium text-slate-500 uppercase tracking-wider mt-0.5"
                                        >
                                            {{ doc.type }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-slate-700"
                            >
                                {{ doc.customer?.name }}
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-slate-600"
                            >
                                <span
                                    v-if="doc.session"
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800"
                                >
                                    {{ doc.session.title }}
                                </span>
                                <span v-else class="text-slate-400">-</span>
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-slate-500 text-sm"
                            >
                                {{ formatDate(doc.created_at) }}
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium"
                            >
                                <a
                                    :href="
                                        'https://drive.google.com/file/d/' +
                                        doc.drive_file_id +
                                        '/view'
                                    "
                                    target="_blank"
                                    class="px-4 py-2 rounded-lg text-blue-600 hover:bg-blue-50 transition-colors font-semibold mr-2 inline-block"
                                >
                                    View
                                </a>
                                <button
                                    @click="confirmDelete(doc)"
                                    class="p-2 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition-colors"
                                >
                                    <TrashIcon class="w-5 h-5 inline-block" />
                                </button>
                            </td>
                        </tr>
                        <tr v-if="!documents.data.length">
                            <td
                                colspan="5"
                                class="px-6 py-12 text-center text-slate-500 font-medium"
                            >
                                No documents found.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Add Modal -->
        <Modal :show="isModalOpen" @close="isModalOpen = false">
            <div class="p-6">
                <h2
                    class="text-xl font-bold text-slate-900 mb-6 tracking-tight"
                >
                    Add Document
                </h2>

                <form @submit.prevent="submit" class="space-y-6">
                    <FormField
                        id="title"
                        label="Document Title"
                        type="text"
                        v-model="form.title"
                        placeholder="e.g. Invoice Wedding 50%"
                        required
                        @blur="v$.title.$touch()"
                        :error="
                            v$.title.$error
                                ? v$.title.$errors[0].$message
                                : form.errors.title
                        "
                    />

                    <div class="grid grid-cols-2 gap-4">
                        <FormField
                            id="type"
                            label="Type"
                            required
                            :error="
                                v$.type.$error
                                    ? v$.type.$errors[0].$message
                                    : form.errors.type
                            "
                        >
                            <template #default="{ errorClass }">
                                <select
                                    id="type"
                                    v-model="form.type"
                                    class="w-full h-12 bg-slate-50 border-0 ring-1 ring-slate-200 rounded-xl px-4 focus:ring-2 focus:ring-blue-600 transition-all text-base text-slate-900"
                                    :class="errorClass"
                                    @blur="v$.type.$touch()"
                                >
                                    <option value="invoice">Invoice</option>
                                    <option value="contract">Contract</option>
                                    <option value="other">Other</option>
                                </select>
                            </template>
                        </FormField>

                        <FormField
                            id="customer_id"
                            label="Customer"
                            required
                            :error="
                                v$.customer_id.$error
                                    ? v$.customer_id.$errors[0].$message
                                    : form.errors.customer_id
                            "
                        >
                            <template #default="{ errorClass }">
                                <select
                                    id="customer_id"
                                    v-model="form.customer_id"
                                    class="w-full h-12 bg-slate-50 border-0 ring-1 ring-slate-200 rounded-xl px-4 focus:ring-2 focus:ring-blue-600 transition-all text-base text-slate-900"
                                    :class="errorClass"
                                    @blur="v$.customer_id.$touch()"
                                >
                                    <option value="" disabled>
                                        Select Customer
                                    </option>
                                    <option
                                        v-for="c in customers"
                                        :key="c.id"
                                        :value="c.id"
                                    >
                                        {{ c.name }}
                                    </option>
                                </select>
                            </template>
                        </FormField>
                    </div>

                    <FormField
                        id="session_id"
                        label="Related Session (Optional)"
                        :error="form.errors.session_id"
                    >
                        <template #default="{ errorClass }">
                            <select
                                id="session_id"
                                v-model="form.session_id"
                                class="w-full h-12 bg-slate-50 border-0 ring-1 ring-slate-200 rounded-xl px-4 focus:ring-2 focus:ring-blue-600 transition-all text-base text-slate-900 disabled:opacity-50"
                                :class="errorClass"
                                :disabled="!form.customer_id"
                            >
                                <option value="">None</option>
                                <option
                                    v-for="s in filteredSessions"
                                    :key="s.id"
                                    :value="s.id"
                                >
                                    {{ s.title }}
                                </option>
                            </select>
                        </template>
                    </FormField>

                    <div>
                        <FormField
                            id="drive_url"
                            label="Google Drive File URL"
                            type="url"
                            v-model="form.drive_url"
                            placeholder="https://drive.google.com/file/d/..."
                            required
                            @blur="v$.drive_url.$touch()"
                            :error="
                                v$.drive_url.$error
                                    ? v$.drive_url.$errors[0].$message
                                    : form.errors.drive_url
                            "
                        />
                        <p class="mt-2 text-xs text-slate-500">
                            Must be a direct link to the file. Ensure sharing
                            permissions are correct.
                        </p>
                    </div>

                    <div class="mt-8 flex justify-end gap-3">
                        <button
                            type="button"
                            @click="isModalOpen = false"
                            class="px-6 py-3 rounded-xl font-medium text-slate-600 border border-slate-200 hover:bg-slate-50 transition-all active:scale-95"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="px-6 py-3 rounded-xl font-medium text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-70 transition-all active:scale-95 shadow-sm"
                            :disabled="form.processing"
                        >
                            Save Document
                        </button>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Delete Modal -->
        <Modal
            :show="isDeleteModalOpen"
            @close="isDeleteModalOpen = false"
            maxWidth="sm"
        >
            <div class="p-6">
                <h2 class="text-lg font-bold text-slate-900 mb-2">
                    Confirm Deletion
                </h2>
                <p class="text-sm text-slate-500 mb-6 leading-relaxed">
                    Are you sure you want to delete this document record? The
                    actual file on Google Drive will not be deleted.
                </p>
                <div class="flex justify-end gap-3">
                    <button
                        @click="isDeleteModalOpen = false"
                        class="px-6 py-2.5 rounded-xl font-medium text-slate-600 border border-slate-200 hover:bg-slate-50 transition-all active:scale-95"
                    >
                        Cancel
                    </button>
                    <button
                        @click="deleteDocument"
                        class="px-6 py-2.5 rounded-xl font-medium text-white bg-red-600 hover:bg-red-700 disabled:opacity-70 transition-all active:scale-95 shadow-sm"
                        :disabled="form.processing"
                    >
                        Delete
                    </button>
                </div>
            </div>
        </Modal>
    </AdminLayout>
</template>
