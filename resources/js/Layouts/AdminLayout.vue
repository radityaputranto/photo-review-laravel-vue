<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import {
    HomeIcon,
    CalendarIcon,
    UsersIcon,
    DocumentTextIcon,
    ArrowRightOnRectangleIcon,
    CameraIcon,
    Bars3Icon,
} from '@heroicons/vue/24/outline'
import { ref, computed, onMounted, onUnmounted } from 'vue'

const page = usePage()
const user = page.props.auth.user

const isSidebarOpen = ref(true)

const checkWindowSize = () => {
    isSidebarOpen.value = window.innerWidth >= 1024
}

import { router } from '@inertiajs/vue3'

onMounted(() => {
    checkWindowSize()
    window.addEventListener('resize', checkWindowSize)
    
    // Auto collapse on small screens when navigating
    router.on('navigate', () => {
        if (window.innerWidth < 1024) {
            isSidebarOpen.value = false
        }
    })
})

onUnmounted(() => {
    window.removeEventListener('resize', checkWindowSize)
})

const navItems = computed(() => {
    let items = [
        { label: 'Dashboard', href: route('admin.dashboard'), icon: HomeIcon },
        { label: 'Sessions', href: route('admin.sessions.index'), icon: CalendarIcon },
    ]
    
    if (user.role === 'admin' || user.role === 'super_admin') {
        items.push({ label: 'Customers', href: route('admin.customers.index'), icon: UsersIcon })
        items.push({ label: 'Documents', href: route('admin.documents.index'), icon: DocumentTextIcon })
    }
    
    return items
})

const displayRole = computed(() => {
    switch (user.role) {
        case 'super_admin': return 'Super Administrator'
        case 'admin': return 'Administrator'
        case 'photographer': return 'Photographer'
        default: return 'User'
    }
})

const isActive = (href) => {
    const path = href.replace(/^.*\/\/[^\/]+/, '')
    return page.url.startsWith(path)
}
</script>

<template>
    <div class="min-h-screen bg-slate-50 flex text-slate-900 font-sans">
        <!-- Mobile Overlay -->
        <div v-if="isSidebarOpen" @click="isSidebarOpen = false" class="fixed inset-0 bg-slate-900/50 z-20 lg:hidden"></div>

        <!-- Sidebar -->
        <aside 
            class="fixed inset-y-0 left-0 w-64 bg-white shadow-[4px_0_24px_rgba(0,0,0,0.02)] flex flex-col z-30 transition-transform duration-300 ease-in-out p-4"
            :class="isSidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        >
            <!-- Logo -->
            <div class="mb-8 px-2 pt-2">
                <span class="text-2xl font-bold text-blue-600 tracking-tight">FotoApp Admin</span>
                <p class="text-sm font-medium text-slate-500 mt-0.5">Professional Studio</p>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 space-y-1">
                <Link
                    v-for="item in navItems"
                    :key="item.href"
                    :href="item.href"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm transition-colors"
                    :class="isActive(item.href)
                        ? 'bg-blue-50 text-blue-600 font-bold border-r-4 border-blue-600'
                        : 'text-slate-500 hover:bg-slate-100 font-medium'"
                >
                    <component :is="item.icon" class="w-5 h-5 flex-shrink-0" />
                    {{ item.label }}
                </Link>
            </nav>

            <!-- User Profile -->
            <div class="mt-auto p-4 flex items-center gap-3 border-t border-slate-100 pt-6">
                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center text-blue-700 font-bold flex-shrink-0">
                    {{ user.name.charAt(0).toUpperCase() }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-slate-900 truncate">{{ user.name }}</p>
                    <p class="text-xs text-slate-500 font-medium truncate">{{ displayRole }}</p>
                </div>
                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="p-2 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition-colors"
                    title="Logout"
                >
                    <ArrowRightOnRectangleIcon class="w-5 h-5" />
                </Link>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 min-w-0 transition-all duration-300 ease-in-out" :class="isSidebarOpen ? 'lg:pl-64' : 'pl-0'">
            <!-- Top Header -->
            <header class="sticky top-0 z-20 bg-slate-50/80 backdrop-blur-md px-4 sm:px-8 h-16 flex items-center">
                <button @click="isSidebarOpen = !isSidebarOpen" class="p-2 -ml-2 text-slate-500 hover:text-slate-900 hover:bg-slate-200 rounded-lg transition-colors focus:outline-none">
                    <Bars3Icon class="w-6 h-6" />
                </button>
            </header>

            <main class="p-4 sm:p-8">
                <slot />
            </main>
        </div>
    </div>
</template>
