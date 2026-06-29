<script setup>
import { ref, computed, watch } from "vue";
import { Head, useForm, Link, router } from "@inertiajs/vue3";
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
    admins: Object,
    filters: Object,
    auth: Object,
});

const isModalOpen = ref(false);
const isDeleteModalOpen = ref(false);
const editingAdmin = ref(null);
const deletingAdmin = ref(null);
const searchQuery = ref(props.filters?.search || "");

watch(searchQuery, (value) => {
    router.get(route('admin.admins.index'), { search: value }, {
        preserveState: true,
        replace: true
    });
});

const form = useForm({
    name: "",
    email: "",
    role: "photographer",
    password: "",
    password_confirmation: "",
});

const { rules, custom } = useValidationRules();
const validationRules = computed(() => ({
    name: { required: rules.required },
    email: rules.email,
    role: { required: rules.required },
    password: editingAdmin.value ? {} : rules.password,
    password_confirmation: editingAdmin.value
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

const openModal = (admin = null) => {
    editingAdmin.value = admin;
    if (admin) {
        form.name = admin.name;
        form.email = admin.email;
        form.role = admin.role;
    } else {
        form.reset();
        form.role = "photographer";
    }
    form.clearErrors();
    v$.value.$reset();
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
    setTimeout(() => {
        form.reset();
        editingAdmin.value = null;
    }, 200);
};

const submit = async () => {
    const isFormValid = await v$.value.$validate();
    if (!isFormValid) return;

    if (editingAdmin.value) {
        form.put(route("admin.admins.update", editingAdmin.value.id), {
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route("admin.admins.store"), {
            onSuccess: () => closeModal(),
        });
    }
};

const confirmDelete = (admin) => {
    deletingAdmin.value = admin;
    isDeleteModalOpen.value = true;
};

const deleteAdmin = () => {
    form.delete(route("admin.admins.destroy", deletingAdmin.value.id), {
        onSuccess: () => {
            isDeleteModalOpen.value = false;
            deletingAdmin.value = null;
        },
    });
};

const toggleActive = (admin) => {
    form.patch(route("admin.admins.toggle-active", admin.id), {
        preserveScroll: true,
    });
};

const displayRole = (role) => {
    switch(role) {
        case 'super_admin': return 'Super Admin';
        case 'admin': return 'Admin';
        case 'photographer': return 'Photographer';
        default: return role;
    }
};

const canEdit = (admin) => {
    if (admin.role === 'super_admin') return false;
    return true;
};

const canDelete = (admin) => {
    if (admin.role === 'super_admin') return false;
    if (admin.id === props.auth.user.id) return false;
    return true;
};

const canToggleStatus = (admin) => {
    if (admin.role === 'super_admin') return false;
    if (admin.id === props.auth.user.id) return false;
    return true;
};
</script>

<template>
    <Head title="Staff Management" />

    <AdminLayout>
        <!-- Top Header -->
        <header class="max-w-7xl mx-auto mb-8">
            <div class="flex justify-between items-end mb-8">
                <div>
                    <h1
                        class="text-3xl font-bold text-slate-900 tracking-tight"
                    >
                        Staff Management
                    </h1>
                    <p class="text-base text-slate-500 mt-1">
                        Manage admins and photographers
                    </p>
                </div>
                <button
                    @click="openModal()"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl text-sm font-medium flex items-center gap-2 transition-all shadow-sm hover:shadow-md hover:scale-[1.02] active:scale-95"
                >
                    <PlusIcon class="w-5 h-5" />
                    Add Staff
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
                        v-model="searchQuery"
                        class="w-full h-12 pl-11 pr-4 bg-slate-50 border-none rounded-xl focus:ring-2 focus:ring-blue-600 text-base placeholder:text-slate-400 transition-all"
                        placeholder="Search staff..."
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
                                Role
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
                            v-for="admin in admins.data"
                            :key="admin.id"
                            class="hover:bg-slate-50 transition-colors"
                        >
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium text-slate-900 flex items-center gap-2">
                                    {{ admin.name }}
                                    <span v-if="admin.id === auth.user.id" class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-700 uppercase">You</span>
                                </div>
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-slate-600"
                            >
                                {{ admin.email }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold"
                                    :class="admin.role === 'admin' ? 'bg-purple-50 text-purple-700' : 'bg-slate-100 text-slate-700'">
                                    {{ displayRole(admin.role) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <button
                                    v-if="canToggleStatus(admin)"
                                    @click="toggleActive(admin)"
                                    :class="
                                        admin.is_active
                                            ? 'bg-green-50 text-green-700'
                                            : 'bg-red-50 text-red-700'
                                    "
                                    class="px-3 py-1 rounded-full text-xs font-semibold transition-all hover:scale-105 active:scale-95"
                                >
                                    {{
                                        admin.is_active
                                            ? "Active"
                                            : "Inactive"
                                    }}
                                </button>
                                <span
                                    v-else
                                    :class="
                                        admin.is_active
                                            ? 'bg-green-50 text-green-700'
                                            : 'bg-red-50 text-red-700'
                                    "
                                    class="px-3 py-1 rounded-full text-xs font-semibold opacity-60 cursor-not-allowed"
                                >
                                    {{ admin.is_active ? "Active" : "Inactive" }}
                                </span>
                            </td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium"
                            >
                                <button
                                    v-if="canEdit(admin)"
                                    @click="openModal(admin)"
                                    class="p-2 rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-colors mr-2"
                                >
                                    <PencilIcon class="w-5 h-5 inline-block" />
                                </button>
                                <button
                                    v-if="canDelete(admin)"
                                    @click="confirmDelete(admin)"
                                    class="p-2 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition-colors"
                                >
                                    <TrashIcon class="w-5 h-5 inline-block" />
                                </button>
                            </td>
                        </tr>
                        <tr v-if="!admins.data.length">
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                No staff found.
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- Pagination -->
                <div
                    class="px-6 py-4 border-t border-slate-100 bg-slate-50 flex justify-center"
                    v-if="admins.links.length > 3"
                >
                    <div class="flex items-center gap-1">
                        <template v-for="(link, k) in admins.links" :key="k">
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
                    {{ editingAdmin ? "Edit Staff" : "Add Staff" }}
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

                    <!-- Role Selection -->
                    <div>
                        <label for="role" class="block text-sm font-semibold text-slate-700 mb-1.5">
                            Role <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="role"
                            v-model="form.role"
                            :disabled="editingAdmin && editingAdmin.id === auth.user.id"
                            class="w-full h-11 px-4 bg-slate-50 border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-600 focus:border-transparent text-base transition-all disabled:opacity-50 disabled:bg-slate-100"
                        >
                            <option value="photographer">Photographer</option>
                            <option value="admin">Admin</option>
                        </select>
                        <div v-if="v$.role.$error || form.errors.role" class="mt-1.5 text-sm text-red-600 font-medium flex items-center gap-1">
                            {{ v$.role.$error ? v$.role.$errors[0].$message : form.errors.role }}
                        </div>
                        <p v-if="editingAdmin && editingAdmin.id === auth.user.id" class="mt-1.5 text-xs text-slate-500">
                            You cannot change your own role.
                        </p>
                    </div>

                    <FormField
                        id="password"
                        label="Password"
                        type="password"
                        v-model="form.password"
                        :required="!editingAdmin"
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
                        :required="!editingAdmin"
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
                            Save Staff
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
                    Are you sure you want to delete this staff member? This action
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
                        @click="deleteAdmin"
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
