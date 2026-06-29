<script setup>
import { ref, computed } from "vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import Modal from "@/Components/Modal.vue";
import FormField from "@/Components/FormField.vue";
import {
    PlusIcon,
    PencilIcon,
    TrashIcon,
    MagnifyingGlassIcon,
} from "@heroicons/vue/24/outline";
import { useVuelidate } from "@vuelidate/core";
import { useValidationRules } from "@/Composables/useValidationRules";

const props = defineProps({
    customers: Object,
});

const isModalOpen = ref(false);
const isDeleteModalOpen = ref(false);
const editingCustomer = ref(null);
const deletingCustomer = ref(null);

const form = useForm({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
});

const { rules, custom } = useValidationRules();
const validationRules = computed(() => ({
    name: { required: rules.required },
    email: rules.email,
    password: editingCustomer.value ? {} : rules.password,
    password_confirmation: editingCustomer.value
        ? {}
        : {
              required: rules.required,
              sameAsPassword: custom.sameAs(
                  computed(() => form.password),
                  "Password",
              ),
          },
}));

const v$ = useVuelidate(validationRules, form);

const openModal = (customer = null) => {
    editingCustomer.value = customer;
    if (customer) {
        form.name = customer.name;
        form.email = customer.email;
    } else {
        form.reset();
    }
    form.clearErrors();
    v$.value.$reset();
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
    setTimeout(() => {
        form.reset();
        editingCustomer.value = null;
    }, 200);
};

const submit = async () => {
    const isFormValid = await v$.value.$validate();
    if (!isFormValid) return;

    if (editingCustomer.value) {
        form.put(route("admin.customers.update", editingCustomer.value.id), {
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route("admin.customers.store"), {
            onSuccess: () => closeModal(),
        });
    }
};

const confirmDelete = (customer) => {
    deletingCustomer.value = customer;
    isDeleteModalOpen.value = true;
};

const deleteCustomer = () => {
    form.delete(route("admin.customers.destroy", deletingCustomer.value.id), {
        onSuccess: () => {
            isDeleteModalOpen.value = false;
            deletingCustomer.value = null;
        },
    });
};

const toggleActive = (customer) => {
    form.patch(route("admin.customers.toggle-active", customer.id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Customer Management" />

    <AdminLayout>
        <!-- Top Header -->
        <header class="max-w-7xl mx-auto mb-8">
            <div class="flex justify-between items-end mb-8">
                <div>
                    <h1
                        class="text-3xl font-bold text-slate-900 tracking-tight"
                    >
                        Customer Management
                    </h1>
                    <p class="text-base text-slate-500 mt-1">
                        Manage client accounts and access
                    </p>
                </div>
                <button
                    @click="openModal()"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl text-sm font-medium flex items-center gap-2 transition-all shadow-sm hover:shadow-md hover:scale-[1.02] active:scale-95"
                >
                    <PlusIcon class="w-5 h-5" />
                    Add Customer
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
                        placeholder="Search customers..."
                    />
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
                                Name
                            </th>
                            <th
                                class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider"
                            >
                                Email
                            </th>
                            <th
                                class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider"
                            >
                                Status
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
                            v-for="customer in customers.data"
                            :key="customer.id"
                            class="hover:bg-slate-50 transition-colors"
                        >
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium text-slate-900">
                                    {{ customer.name }}
                                </div>
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-slate-600"
                            >
                                {{ customer.email }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <button
                                    @click="toggleActive(customer)"
                                    :class="
                                        customer.is_active
                                            ? 'bg-green-50 text-green-700'
                                            : 'bg-red-50 text-red-700'
                                    "
                                    class="px-3 py-1 rounded-full text-xs font-semibold transition-all hover:scale-105 active:scale-95"
                                >
                                    {{
                                        customer.is_active
                                            ? "Active"
                                            : "Inactive"
                                    }}
                                </button>
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium"
                            >
                                <button
                                    @click="openModal(customer)"
                                    class="p-2 rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-colors mr-2"
                                >
                                    <PencilIcon class="w-5 h-5 inline-block" />
                                </button>
                                <button
                                    @click="confirmDelete(customer)"
                                    class="p-2 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition-colors"
                                >
                                    <TrashIcon class="w-5 h-5 inline-block" />
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- Pagination -->
                <div
                    class="px-6 py-4 border-t border-slate-100 bg-slate-50 flex justify-center"
                    v-if="customers.links.length > 3"
                >
                    <div class="flex items-center gap-1">
                        <template v-for="(link, k) in customers.links" :key="k">
                            <div
                                v-if="link.url === null"
                                class="px-3 py-1 text-sm text-slate-400 border border-slate-100 rounded-lg bg-white"
                                v-html="link.label"
                            />
                            <Link
                                v-else
                                :href="link.url"
                                class="px-3 py-1 text-sm border rounded-lg transition-colors"
                                :class="
                                    link.active
                                        ? 'bg-blue-50 border-blue-200 text-blue-700 font-medium shadow-sm'
                                        : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50'
                                "
                                v-html="link.label"
                            />
                        </template>
                    </div>
                </div>
            </div>
        </section>

        <!-- Add/Edit Modal -->
        <Modal :show="isModalOpen" @close="closeModal">
            <div class="p-6">
                <h2
                    class="text-xl font-bold text-slate-900 mb-6 tracking-tight"
                >
                    {{ editingCustomer ? "Edit Customer" : "Add Customer" }}
                </h2>

                <form @submit.prevent="submit" class="space-y-6">
                    <FormField
                        id="name"
                        label="Name"
                        type="text"
                        v-model="form.name"
                        required
                        @blur="v$.name.$touch()"
                        :error="
                            v$.name.$error
                                ? v$.name.$errors[0].$message
                                : form.errors.name
                        "
                    />

                    <FormField
                        id="email"
                        label="Email"
                        type="email"
                        v-model="form.email"
                        required
                        @blur="v$.email.$touch()"
                        :error="
                            v$.email.$error
                                ? v$.email.$errors[0].$message
                                : form.errors.email
                        "
                    />

                    <FormField
                        id="password"
                        label="Password"
                        type="password"
                        v-model="form.password"
                        :required="!editingCustomer"
                        @blur="v$.password.$touch()"
                        :error="
                            v$.password.$error
                                ? v$.password.$errors[0].$message
                                : form.errors.password
                        "
                    />

                    <FormField
                        id="password_confirmation"
                        label="Confirm Password"
                        type="password"
                        v-model="form.password_confirmation"
                        :required="!editingCustomer"
                        @blur="v$.password_confirmation.$touch()"
                        :error="
                            v$.password_confirmation.$error
                                ? v$.password_confirmation.$errors[0].$message
                                : form.errors.password_confirmation
                        "
                    />

                    <div class="mt-8 flex justify-end gap-3">
                        <button
                            type="button"
                            @click="closeModal"
                            class="px-6 py-3 rounded-xl font-medium text-slate-600 border border-slate-200 hover:bg-slate-50 transition-all active:scale-95"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="px-6 py-3 rounded-xl font-medium text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-70 transition-all active:scale-95 shadow-sm"
                            :disabled="form.processing"
                        >
                            Save Customer
                        </button>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Delete Confirmation Modal -->
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
                    Are you sure you want to delete this customer? This action
                    cannot be undone.
                </p>
                <div class="flex justify-end gap-3">
                    <button
                        @click="isDeleteModalOpen = false"
                        class="px-6 py-2.5 rounded-xl font-medium text-slate-600 border border-slate-200 hover:bg-slate-50 transition-all active:scale-95"
                    >
                        Cancel
                    </button>
                    <button
                        @click="deleteCustomer"
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
