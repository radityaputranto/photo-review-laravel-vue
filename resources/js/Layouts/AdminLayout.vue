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
import { ref, onMounted, onUnmounted } from 'vue'

const page = usePage()
const user = page.props.auth.user

const isSidebarOpen = ref(true)

const checkWindowSize = () => {
    isSidebarOpen.value = window.innerWidth >= 1024
}

onMounted(() => {
    checkWindowSize()
    window.addEventListener('resize', checkWindowSize)
})

onUnmounted(() => {
    window.removeEventListener('resize', checkWindowSize)
})

const navItems = [
    { label: 'Dashboard', href: route('admin.dashboard'), icon: HomeIcon },
    { label: 'Sessions', href: route('admin.sessions.index'), icon: CalendarIcon },
    { label: 'Customers', href: route('admin.customers.index'), icon: UsersIcon },
    { label: 'Documents', href: route('admin.documents.index'), icon: DocumentTextIcon },
]

const isActive = (href) => {
    const path = href.replace(/^.*\/\/[^\/]+/, '')
    return page.url.startsWith(path)
}
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50 flex">
        <!-- Mobile Overlay -->
        <div v-if="isSidebarOpen" @click="isSidebarOpen = false" class="fixed inset-0 bg-slate-900/50 z-20 lg:hidden"></div>

        <!-- Sidebar -->
        <aside 
            class="fixed inset-y-0 left-0 w-64 bg-white border-r border-slate-100 flex flex-col z-30 transition-transform duration-300 ease-in-out"
            :class="isSidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        >
            <!-- Logo -->
            <div class="px-6 py-5 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                        <CameraIcon class="w-5 h-5 text-white" />
                    </div>
                    <span class="text-lg font-bold text-slate-900">FotoApp</span>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-3 py-4 space-y-0.5">
                <Link
                    v-for="item in navItems"
                    :key="item.href"
                    :href="item.href"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors"
                    :class="isActive(item.href)
                        ? 'bg-blue-50 text-blue-700 border-l-2 border-blue-600 pl-2.5'
                        : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'"
                >
                    <component :is="item.icon" class="w-5 h-5 flex-shrink-0" />
                    {{ item.label }}
                </Link>
            </nav>

            <!-- User Profile -->
            <div class="p-4 border-t border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-blue-600 rounded-full flex items-center justify-center text-white text-sm font-semibold flex-shrink-0">
                        {{ user.name.charAt(0).toUpperCase() }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-slate-900 truncate">{{ user.name }}</p>
                        <p class="text-xs text-slate-500 truncate">{{ user.email }}</p>
                    </div>
                    <Link
                        :href="route('logout')"
                        method="post"
                        as="button"
                        class="text-slate-400 hover:text-slate-600 transition-colors"
                        title="Logout"
                    >
                        <ArrowRightOnRectangleIcon class="w-5 h-5" />
                    </Link>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 min-w-0 transition-all duration-300 ease-in-out" :class="isSidebarOpen ? 'lg:pl-64' : 'pl-0'">
            <!-- Top Header -->
            <header class="sticky top-0 z-20 bg-white/80 backdrop-blur-md border-b border-slate-100 px-4 sm:px-6 h-16 flex items-center">
                <button @click="isSidebarOpen = !isSidebarOpen" class="p-2 text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition-colors focus:outline-none">
                    <Bars3Icon class="w-6 h-6" />
                </button>
            </header>

            <main class="p-4 sm:p-8">
                <slot />
            </main>
        </div>
    </div>
</template>
