<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import FormField from "@/Components/FormField.vue";
import {
    ArrowLeftIcon,
    CheckCircleIcon,
    ExclamationCircleIcon,
    InformationCircleIcon,
    UserIcon,
    CloudIcon,
} from "@heroicons/vue/24/outline";
import { ref, computed } from "vue";
import { useVuelidate } from "@vuelidate/core";
import { useValidationRules } from "@/Composables/useValidationRules";

const props = defineProps({
    customers: Array,
});

const form = useForm({
    customer_id: "",
    title: "",
    shoot_date: "",
    drive_folder_url: "",
    max_selections: 30,
});

const { rules, custom } = useValidationRules();
const validationRules = computed(() => ({
    title: { required: rules.required },
    customer_id: { required: rules.required },
    shoot_date: { required: rules.required },
    max_selections: {
        required: rules.required,
        numeric: custom.numeric,
        minValue: custom.minValue(1),
    },
    drive_folder_url: rules.url,
}));

const v$ = useVuelidate(validationRules, form);

const validatingDriveUrl = ref(false);
const driveUrlStatus = ref(null); // null, 'success', 'error'
const driveUrlMessage = ref("");

const validateDriveUrl = async () => {
    if (!form.drive_folder_url) return;

    validatingDriveUrl.value = true;
    driveUrlStatus.value = null;

    try {
        const response = await fetch(
            route("admin.sessions.validate-drive-url"),
            {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN":
                        document
                            .querySelector('meta[name="csrf-token"]')
                            ?.getAttribute("content") || "",
                },
                body: JSON.stringify({ url: form.drive_folder_url }),
            },
        );

        const data = await response.json();

        if (data.success) {
            driveUrlStatus.value = "success";
            driveUrlMessage.value = data.message;
        } else {
            driveUrlStatus.value = "error";
            driveUrlMessage.value = data.message || "Validation failed";
        }
    } catch (error) {
        driveUrlStatus.value = "error";
        driveUrlMessage.value = "Network error during validation";
    } finally {
        validatingDriveUrl.value = false;
    }
};

const submit = async () => {
    const isFormValid = await v$.value.$validate();
    if (!isFormValid) return;

    form.post(route("admin.sessions.store"));
};
</script>

<template>
    <Head title="Create Session" />

    <AdminLayout>
        <div class="max-w-3xl mx-auto space-y-8">
            <!-- Header Section -->
            <header class="space-y-4">
                <Link
                    :href="route('admin.sessions.index')"
                    class="inline-flex items-center text-blue-600 font-medium text-sm hover:underline transition-all group"
                >
                    <ArrowLeftIcon
                        class="w-4 h-4 mr-2 group-hover:-translate-x-1 transition-transform"
                    />
                    Back to Sessions
                </Link>
                <h2 class="text-3xl font-bold text-slate-900 tracking-tight">
                    New Session
                </h2>
            </header>

            <form @submit.prevent="submit" class="space-y-6">
                <!-- Card 1: Session Info -->
                <section
                    class="bg-white rounded-xl shadow-sm p-6 space-y-6 border border-slate-200"
                >
                    <div
                        class="flex items-center space-x-2 border-b border-slate-100 pb-4"
                    >
                        <InformationCircleIcon class="w-6 h-6 text-blue-600" />
                        <h3 class="text-xl font-semibold text-slate-900">
                            Session Info
                        </h3>
                    </div>

                    <div class="space-y-4">
                        <FormField
                            id="title"
                            label="Session Title"
                            type="text"
                            v-model="form.title"
                            placeholder="e.g. Summer Wedding"
                            required
                            @blur="v$.title.$touch()"
                            :error="
                                v$.title.$error
                                    ? v$.title.$errors[0].$message
                                    : form.errors.title
                            "
                        />

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <FormField
                                id="shoot_date"
                                label="Shoot Date"
                                type="date"
                                v-model="form.shoot_date"
                                required
                                @blur="v$.shoot_date.$touch()"
                                :error="
                                    v$.shoot_date.$error
                                        ? v$.shoot_date.$errors[0].$message
                                        : form.errors.shoot_date
                                "
                            />
                        </div>
                    </div>
                </section>

                <!-- Card 2: Customer -->
                <section
                    class="bg-white rounded-xl shadow-sm p-6 space-y-6 border border-slate-200"
                >
                    <div
                        class="flex items-center space-x-2 border-b border-slate-100 pb-4"
                    >
                        <UserIcon class="w-6 h-6 text-blue-600" />
                        <h3 class="text-xl font-semibold text-slate-900">
                            Customer
                        </h3>
                    </div>

                    <div class="space-y-4">
                        <FormField
                            id="customer_id"
                            label="Select Customer"
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
                                        Search for a customer...
                                    </option>
                                    <option
                                        v-for="customer in customers"
                                        :key="customer.id"
                                        :value="customer.id"
                                    >
                                        {{ customer.name }} ({{
                                            customer.email
                                        }})
                                    </option>
                                </select>
                            </template>
                        </FormField>

                        <FormField
                            id="max_selections"
                            label="Max Photo Selection Quota"
                            type="number"
                            v-model="form.max_selections"
                            min="1"
                            required
                            @blur="v$.max_selections.$touch()"
                            :error="
                                v$.max_selections.$error
                                    ? v$.max_selections.$errors[0].$message
                                    : form.errors.max_selections
                            "
                        />
                        <p class="text-sm text-slate-500 mt-1 opacity-80">
                            Set the maximum photos customer can select
                        </p>
                    </div>
                </section>

                <!-- Card 3: Google Drive Integration -->
                <section
                    class="bg-white rounded-xl shadow-sm p-6 space-y-6 border border-slate-200"
                >
                    <div
                        class="flex items-center space-x-2 border-b border-slate-100 pb-4"
                    >
                        <CloudIcon class="w-6 h-6 text-blue-600" />
                        <h3 class="text-xl font-semibold text-slate-900">
                            Google Drive
                        </h3>
                    </div>

                    <div class="space-y-4">
                        <FormField
                            id="drive_folder_url"
                            label="Drive Folder URL"
                            required
                            :error="
                                v$.drive_folder_url.$error
                                    ? v$.drive_folder_url.$errors[0].$message
                                    : form.errors.drive_folder_url
                            "
                        >
                            <template #default="{ errorClass }">
                                <div class="flex items-center w-full">
                                    <input
                                        id="drive_folder_url"
                                        type="url"
                                        v-model="form.drive_folder_url"
                                        class="flex-1 h-12 bg-slate-50 border-0 ring-1 ring-slate-200 rounded-l-xl px-4 focus:ring-2 focus:ring-blue-600 transition-all text-base placeholder:text-slate-400"
                                        :class="errorClass"
                                        placeholder="https://drive.google.com/drive/folders/..."
                                        @blur="v$.drive_folder_url.$touch()"
                                    />
                                    <button
                                        type="button"
                                        @click="validateDriveUrl"
                                        :disabled="
                                            !form.drive_folder_url ||
                                            validatingDriveUrl
                                        "
                                        class="h-12 px-6 bg-slate-100 ring-1 ring-slate-200 rounded-r-xl font-medium text-slate-700 hover:bg-slate-200 focus:outline-none transition-colors disabled:opacity-50 border-l-0"
                                    >
                                        <span v-if="validatingDriveUrl"
                                            >Validating...</span
                                        >
                                        <span v-else>Validate</span>
                                    </button>
                                </div>
                            </template>
                        </FormField>

                        <div
                            v-if="driveUrlStatus"
                            class="mt-2 flex items-start gap-2 text-sm"
                            :class="
                                driveUrlStatus === 'success'
                                    ? 'text-green-600'
                                    : 'text-red-600'
                            "
                        >
                            <CheckCircleIcon
                                v-if="driveUrlStatus === 'success'"
                                class="w-5 h-5 shrink-0"
                            />
                            <ExclamationCircleIcon
                                v-else
                                class="w-5 h-5 shrink-0"
                            />
                            <span>{{ driveUrlMessage }}</span>
                        </div>
                        <p class="text-sm text-slate-500 mt-1 flex items-start">
                            <InformationCircleIcon
                                class="w-4 h-4 mr-1 mt-0.5"
                            />
                            Share your Drive folder to the service account first
                        </p>
                    </div>
                </section>

                <!-- Bottom Actions -->
                <div class="flex justify-end items-center space-x-4 pt-6 pb-12">
                    <Link
                        :href="route('admin.sessions.index')"
                        class="px-6 py-3 rounded-xl font-medium text-slate-600 border border-slate-200 hover:bg-slate-50 transition-all active:scale-95"
                    >
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-6 py-3 rounded-xl font-medium text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-70 transition-all active:scale-95 shadow-sm hover:shadow-md"
                    >
                        {{ form.processing ? "Saving..." : "Create Session" }}
                    </button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
