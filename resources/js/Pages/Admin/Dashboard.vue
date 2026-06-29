<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { useFormatDate } from '@/Composables/useFormatDate'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import StatsCard from '@/Components/Admin/StatsCard.vue'
import { UsersIcon, CameraIcon, CheckBadgeIcon, ArchiveBoxArrowDownIcon } from '@heroicons/vue/24/outline'

const { formatDate } = useFormatDate()

defineProps({
    stats: Object,
    recentSessions: Array,
})
</script>

<template>
    <Head title="Admin Dashboard" />

    <AdminLayout>
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-900">Dashboard</h1>
            <p class="text-slate-600 mt-1">Overview of your photography business.</p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <StatsCard title="Total Customers" :value="stats.total_customers" :icon="UsersIcon" color="blue" />
            <StatsCard title="Active Sessions" :value="stats.active_sessions" :icon="CameraIcon" color="amber" />
            <StatsCard title="Pending Review" :value="stats.pending_reviews" :icon="CheckBadgeIcon" color="green" />
            <StatsCard title="Delivered" :value="stats.delivered_sessions" :icon="ArchiveBoxArrowDownIcon" color="purple" />
        </div>

        <!-- Recent Sessions -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-slate-900">Recent Sessions</h2>
                <Link :href="route('admin.sessions.index')" class="text-sm font-medium text-blue-600 hover:text-blue-700">
                    View All
                </Link>
            </div>
            
            <div class="p-6 text-center text-slate-500 py-12" v-if="!recentSessions.length">
                No sessions found. Create a new session to get started.
            </div>

            <table class="min-w-full divide-y divide-slate-200" v-else>
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    <tr v-for="session in recentSessions" :key="session.id">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <Link :href="route('admin.sessions.show', session.id)" class="font-medium text-blue-600 hover:underline">
                                {{ session.title }}
                            </Link>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-slate-600">
                            {{ session.customer?.name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-slate-600">
                            {{ formatDate(session.shoot_date) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium capitalize"
                                :class="{
                                    'bg-amber-100 text-amber-800': session.status === 'active',
                                    'bg-blue-100 text-blue-800': session.status === 'completed',
                                    'bg-green-100 text-green-800': session.status === 'delivered',
                                }"
                            >
                                {{ session.status }}
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AdminLayout>
</template>
