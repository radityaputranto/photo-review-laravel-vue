<script setup>
import { Head, Link } from "@inertiajs/vue3";
import { useFormatDate } from "@/Composables/useFormatDate";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import StatsCard from "@/Components/Admin/StatsCard.vue";
import {
    UsersIcon,
    CameraIcon,
    CheckBadgeIcon,
    ArchiveBoxArrowDownIcon,
    CalendarIcon,
    UserIcon,
} from "@heroicons/vue/24/outline";

const { formatDate } = useFormatDate();

defineProps({
    stats: Object,
    recentSessions: Array,
});
</script>

<template>
    <Head title="Admin Dashboard" />

    <AdminLayout>
        <!-- Top Header -->
        <header class="max-w-7xl mx-auto mb-8">
            <h1 class="text-3xl font-bold text-slate-900 tracking-tight">
                Dashboard
            </h1>
            <p class="text-base text-slate-500 mt-1">
                Overview of your photography business.
            </p>
        </header>

        <section class="max-w-7xl mx-auto space-y-8 mb-12">
            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <StatsCard
                    title="Total Customers"
                    :value="stats.total_customers"
                    :icon="UsersIcon"
                    color="blue"
                />
                <StatsCard
                    title="Active Sessions"
                    :value="stats.active_sessions"
                    :icon="CameraIcon"
                    color="amber"
                />
                <StatsCard
                    title="Pending Review"
                    :value="stats.pending_reviews"
                    :icon="CheckBadgeIcon"
                    color="green"
                />
                <StatsCard
                    title="Delivered"
                    :value="stats.delivered_sessions"
                    :icon="ArchiveBoxArrowDownIcon"
                    color="purple"
                />
            </div>

            <!-- Recent Sessions -->
            <div
                class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden"
            >
                <div
                    class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50"
                >
                    <h2 class="text-lg font-bold text-slate-900 tracking-tight">
                        Recent Sessions
                    </h2>
                    <Link
                        :href="route('admin.sessions.index')"
                        class="text-sm font-semibold text-blue-600 hover:text-blue-700 transition-colors"
                    >
                        View All →
                    </Link>
                </div>

                <div
                    class="p-12 text-center text-slate-500 font-medium"
                    v-if="!recentSessions.length"
                >
                    No sessions found. Create a new session to get started.
                </div>

                <div class="divide-y divide-slate-100" v-else>
                    <div
                        v-for="session in recentSessions"
                        :key="session.id"
                        class="p-6 hover:bg-slate-50 transition-colors flex flex-col md:flex-row md:items-center justify-between gap-4"
                    >
                        <div class="flex-1">
                            <Link
                                :href="route('admin.sessions.show', session.id)"
                                class="text-lg font-semibold text-blue-600 hover:underline mb-1 inline-block"
                            >
                                {{ session.title }}
                            </Link>
                            <div
                                class="flex flex-wrap items-center gap-4 text-slate-500 text-sm"
                            >
                                <span
                                    class="flex items-center gap-1.5"
                                    v-if="session.customer"
                                >
                                    <UserIcon class="w-4 h-4" />
                                    {{ session.customer.name }}
                                </span>
                                <span class="flex items-center gap-1.5">
                                    <CalendarIcon class="w-4 h-4" />
                                    {{ formatDate(session.shoot_date) }}
                                </span>
                            </div>
                        </div>
                        <div>
                            <span
                                class="px-4 py-1.5 rounded-full text-xs font-semibold flex items-center gap-1.5 capitalize"
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
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </AdminLayout>
</template>
