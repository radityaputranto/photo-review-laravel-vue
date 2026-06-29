<script setup>
import { ref } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Modal from '@/Components/Modal.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import DangerButton from '@/Components/DangerButton.vue'
import { PlusIcon, PencilIcon, TrashIcon } from '@heroicons/vue/24/outline'

const props = defineProps({
    customers: Object,
})

const isModalOpen = ref(false)
const isDeleteModalOpen = ref(false)
const editingCustomer = ref(null)
const deletingCustomer = ref(null)

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
})

const openModal = (customer = null) => {
    editingCustomer.value = customer
    if (customer) {
        form.name = customer.name
        form.email = customer.email
    } else {
        form.reset()
    }
    form.clearErrors()
    isModalOpen.value = true
}

const closeModal = () => {
    isModalOpen.value = false
    setTimeout(() => {
        form.reset()
        editingCustomer.value = null
    }, 200)
}

const submit = () => {
    if (editingCustomer.value) {
        form.put(route('admin.customers.update', editingCustomer.value.id), {
            onSuccess: () => closeModal(),
        })
    } else {
        form.post(route('admin.customers.store'), {
            onSuccess: () => closeModal(),
        })
    }
}

const confirmDelete = (customer) => {
    deletingCustomer.value = customer
    isDeleteModalOpen.value = true
}

const deleteCustomer = () => {
    form.delete(route('admin.customers.destroy', deletingCustomer.value.id), {
        onSuccess: () => {
            isDeleteModalOpen.value = false
            deletingCustomer.value = null
        },
    })
}

const toggleActive = (customer) => {
    form.patch(route('admin.customers.toggle-active', customer.id), {
        preserveScroll: true,
    })
}
</script>

<template>
    <Head title="Customer Management" />

    <AdminLayout>
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-slate-900">Customer Management</h1>
            <PrimaryButton @click="openModal()">
                <PlusIcon class="w-5 h-5 mr-2" />
                Add Customer
            </PrimaryButton>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    <tr v-for="customer in customers.data" :key="customer.id">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-medium text-slate-900">{{ customer.name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-slate-600">
                            {{ customer.email }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <button
                                @click="toggleActive(customer)"
                                :class="customer.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                class="px-2.5 py-0.5 rounded-full text-xs font-medium transition-colors hover:opacity-80"
                            >
                                {{ customer.is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button @click="openModal(customer)" class="text-blue-600 hover:text-blue-900 mr-4">
                                <PencilIcon class="w-5 h-5 inline-block" />
                            </button>
                            <button @click="confirmDelete(customer)" class="text-red-600 hover:text-red-900">
                                <TrashIcon class="w-5 h-5 inline-block" />
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
            
            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50" v-if="customers.links.length > 3">
                <div class="flex items-center gap-1">
                    <template v-for="(link, k) in customers.links" :key="k">
                        <div
                            v-if="link.url === null"
                            class="px-3 py-1 text-sm text-slate-400 border border-slate-200 rounded bg-white"
                            v-html="link.label"
                        />
                        <Link
                            v-else
                            :href="link.url"
                            class="px-3 py-1 text-sm border border-slate-200 rounded transition-colors hover:bg-slate-100"
                            :class="link.active ? 'bg-blue-50 border-blue-200 text-blue-600 font-medium' : 'bg-white text-slate-600'"
                            v-html="link.label"
                        />
                    </template>
                </div>
            </div>
        </div>

        <!-- Add/Edit Modal -->
        <Modal :show="isModalOpen" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-slate-900 mb-4">
                    {{ editingCustomer ? 'Edit Customer' : 'Add Customer' }}
                </h2>
                
                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <InputLabel for="name" value="Name" />
                        <TextInput
                            id="name"
                            v-model="form.name"
                            type="text"
                            class="mt-1 block w-full"
                            required
                        />
                        <InputError :message="form.errors.name" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel for="email" value="Email" />
                        <TextInput
                            id="email"
                            v-model="form.email"
                            type="email"
                            class="mt-1 block w-full"
                            required
                        />
                        <InputError :message="form.errors.email" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel for="password" :value="editingCustomer ? 'Password (leave blank to keep current)' : 'Password'" />
                        <TextInput
                            id="password"
                            v-model="form.password"
                            type="password"
                            class="mt-1 block w-full"
                            :required="!editingCustomer"
                        />
                        <InputError :message="form.errors.password" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel for="password_confirmation" value="Confirm Password" />
                        <TextInput
                            id="password_confirmation"
                            v-model="form.password_confirmation"
                            type="password"
                            class="mt-1 block w-full"
                            :required="!editingCustomer"
                        />
                        <InputError :message="form.errors.password_confirmation" class="mt-2" />
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <SecondaryButton @click="closeModal">Cancel</SecondaryButton>
                        <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            Save
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Delete Confirmation Modal -->
        <Modal :show="isDeleteModalOpen" @close="isDeleteModalOpen = false" maxWidth="sm">
            <div class="p-6">
                <h2 class="text-lg font-medium text-slate-900 mb-4">Confirm Deletion</h2>
                <p class="text-sm text-slate-600 mb-6">
                    Are you sure you want to delete this customer? This action cannot be undone.
                </p>
                <div class="flex justify-end gap-3">
                    <SecondaryButton @click="isDeleteModalOpen = false">Cancel</SecondaryButton>
                    <DangerButton @click="deleteCustomer" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Delete
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </AdminLayout>
</template>
