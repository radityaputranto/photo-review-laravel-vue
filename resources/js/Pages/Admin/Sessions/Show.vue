<script setup>
import { Head, Link } from "@inertiajs/vue3";
import { useFormatDate } from "@/Composables/useFormatDate";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import {
    ArrowLeftIcon,
    PencilIcon,
    CheckCircleIcon,
    DocumentCheckIcon,
    UserIcon,
    CalendarIcon,
    CloudIcon,
    LinkIcon,
} from "@heroicons/vue/24/outline";

const { formatDate } = useFormatDate();
const props = defineProps({
    session: Object,
    totalPhotos: Number,
});
</script>

<template>
    <Head :title="session.title" />

    <AdminLayout>
        <!-- Header -->
        <header class="max-w-7xl mx-auto mb-8">
            <Link
                :href="route('admin.sessions.index')"
                class="inline-flex items-center text-blue-600 font-medium text-sm hover:underline transition-all group mb-4"
            >
                <ArrowLeftIcon
                    class="w-4 h-4 mr-2 group-hover:-translate-x-1 transition-transform"
                />
                Back to Sessions
            </Link>

            <div
                class="flex flex-col md:flex-row md:items-start justify-between gap-6"
            >
                <div>
                    <h1
                        class="text-3xl font-bold text-slate-900 tracking-tight"
                    >
                        {{ session.title }}
                    </h1>
                    <div
                        class="flex flex-wrap items-center gap-4 text-slate-500 mt-2"
                    >
                        <span class="flex items-center gap-1.5 font-medium">
                            <UserIcon class="w-4 h-4" />
                            {{ session.customer?.name }} ({{
                                session.customer?.email
                            }})
                        </span>
                        <span class="flex items-center gap-1.5">
                            <CalendarIcon class="w-4 h-4" />
                            {{ formatDate(session.shoot_date) }}
                        </span>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <Link
                        :href="route('admin.sessions.edit', session.id)"
                        class="bg-white hover:bg-slate-50 text-slate-700 px-5 py-2.5 rounded-xl text-sm font-medium flex items-center gap-2 border border-slate-200 shadow-sm transition-all active:scale-95"
                    >
                        <PencilIcon class="w-4 h-4" />
                        Edit Session
                    </Link>
                </div>
            </div>
        </header>

        <div
            class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-8 pb-12"
        >
            <!-- Left Col: Details -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Info Card -->
                <div
                    class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden"
                >
                    <div
                        class="px-6 py-5 border-b border-slate-100 bg-slate-50"
                    >
                        <h2
                            class="text-lg font-bold text-slate-900 tracking-tight"
                        >
                            Session Details
                        </h2>
                    </div>

                    <div class="p-6">
                        <dl
                            class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-8"
                        >
                            <div>
                                <dt
                                    class="text-sm font-semibold text-slate-500 mb-1"
                                >
                                    Status
                                </dt>
                                <dd class="mt-1">
                                    <span
                                        class="px-3 py-1 rounded-full text-xs font-semibold capitalize inline-flex items-center gap-1.5"
                                        :class="{
                                            'bg-blue-50 text-blue-700':
                                                session.status === 'active',
                                            'bg-slate-100 text-slate-700':
                                                session.status === 'completed',
                                            'bg-green-50 text-green-700':
                                                session.status === 'delivered',
                                        }"
                                    >
                                        <span
                                            v-if="session.status === 'active'"
                                            class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"
                                        ></span>
                                        {{ session.status }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt
                                    class="text-sm font-semibold text-slate-500 mb-1"
                                >
                                    Shoot Date
                                </dt>
                                <dd class="text-slate-900 font-medium">
                                    {{ formatDate(session.shoot_date) }}
                                </dd>
                            </div>
                            <div class="sm:col-span-2">
                                <dt
                                    class="text-sm font-semibold text-slate-500 mb-2 flex items-center gap-1.5"
                                >
                                    <CloudIcon class="w-4 h-4" />
                                    Google Drive Source
                                </dt>
                                <dd class="text-sm">
                                    <a
                                        :href="session.drive_folder_url"
                                        target="_blank"
                                        class="inline-flex items-center px-4 py-3 bg-slate-50 border border-slate-200 rounded-lg text-blue-600 hover:text-blue-700 hover:bg-blue-50 hover:border-blue-200 break-all transition-all w-full group"
                                    >
                                        <span class="truncate flex-1">{{
                                            session.drive_folder_url
                                        }}</span>
                                        <ArrowLeftIcon
                                            class="w-4 h-4 ml-2 rotate-135 opacity-50 group-hover:opacity-100"
                                        />
                                    </a>
                                </dd>
                            </div>
                            <div class="sm:col-span-2">
                                <dt
                                    class="text-sm font-semibold text-slate-500 mb-2 flex items-center gap-1.5"
                                >
                                    <LinkIcon class="w-4 h-4" />
                                    Final Download Link
                                </dt>
                                <dd class="text-sm">
                                    <a
                                        v-if="session.download_link"
                                        :href="session.download_link"
                                        target="_blank"
                                        class="inline-flex items-center px-4 py-3 bg-slate-50 border border-slate-200 rounded-lg text-blue-600 hover:text-blue-700 hover:bg-blue-50 hover:border-blue-200 break-all transition-all w-full group"
                                    >
                                        <span class="truncate flex-1">{{
                                            session.download_link
                                        }}</span>
                                        <ArrowLeftIcon
                                            class="w-4 h-4 ml-2 rotate-135 opacity-50 group-hover:opacity-100"
                                        />
                                    </a>
                                    <span
                                        v-else
                                        class="inline-flex items-center px-4 py-3 bg-slate-50 border border-slate-200 rounded-lg text-slate-400 w-full italic"
                                    >
                                        Not provided yet
                                    </span>
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>

            <!-- Right Col: Stats & Actions -->
            <div class="space-y-8">
                <!-- Selections Stats Card -->
                <div
                    class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden"
                >
                    <div
                        class="px-6 py-5 border-b border-slate-100 bg-slate-50"
                    >
                        <h2
                            class="text-lg font-bold text-slate-900 tracking-tight"
                        >
                            Selections Overview
                        </h2>
                    </div>

                    <div class="p-6 space-y-6">
                        <div class="space-y-4">
                            <div
                                class="flex justify-between items-center pb-4 border-b border-slate-100"
                            >
                                <span class="text-sm font-medium text-slate-600"
                                    >Selected Photos</span
                                >
                                <div class="flex items-baseline gap-1">
                                    <span
                                        class="text-2xl font-bold text-blue-600"
                                        >{{
                                            session.photo_selections?.length ||
                                            0
                                        }}</span
                                    >
                                    <span
                                        class="text-sm text-slate-400 font-medium"
                                        >/ {{ session.max_selections }}</span
                                    >
                                </div>
                            </div>
                            <div
                                class="flex justify-between items-center pb-4 border-b border-slate-100"
                            >
                                <span class="text-sm font-medium text-slate-600"
                                    >Drive Photos Detected</span
                                >
                                <span
                                    class="text-lg font-bold text-slate-900"
                                    >{{ totalPhotos }}</span
                                >
                            </div>
                            <div class="flex justify-between items-center pb-2">
                                <span class="text-sm font-medium text-slate-600"
                                    >Customer Submitted</span
                                >
                                <div
                                    class="flex items-center gap-1.5"
                                    :class="
                                        session.submitted_at
                                            ? 'text-green-600 bg-green-50 px-2 py-1 rounded-md'
                                            : 'text-slate-500'
                                    "
                                >
                                    <CheckCircleIcon class="w-5 h-5" />
                                    <span class="font-bold text-sm">{{
                                        session.submitted_at ? "Yes" : "No"
                                    }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="pt-2">
                            <Link
                                :href="
                                    route(
                                        'admin.sessions.selections',
                                        session.id,
                                    )
                                "
                            >
                                <button
                                    class="w-full inline-flex justify-center items-center px-6 py-3 bg-blue-600 rounded-xl font-medium text-white hover:bg-blue-700 transition-all shadow-sm active:scale-95"
                                >
                                    <DocumentCheckIcon class="w-5 h-5 mr-2" />
                                    Review Selections
                                </button>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
