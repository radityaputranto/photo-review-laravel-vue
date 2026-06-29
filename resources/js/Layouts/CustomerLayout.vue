<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import { CameraIcon, ArrowRightOnRectangleIcon } from '@heroicons/vue/24/outline'

const page = usePage()
const user = page.props.auth.user

const navItems = [
    { label: 'Dashboard', href: route('customer.dashboard') },
    { label: 'Dokumen', href: route('customer.documents.index') },
]

const isExactActive = (href) => {
    const path = href.replace(/^.*\/\/[^\/]+/, '')
    return page.url === path
}
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-white via-blue-50 to-slate-50">
        <!-- Navbar -->
        <nav class="sticky top-0 z-20 bg-white/80 backdrop-blur-md border-b border-slate-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <!-- Logo -->
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                            <CameraIcon class="w-5 h-5 text-white" />
                        </div>
                        <span class="text-lg font-bold text-slate-900">FotoApp</span>
                    </div>

                    <!-- Nav Links -->
                    <div class="hidden md:flex items-center gap-1">
                        <Link
                            v-for="item in navItems"
                            :key="item.href"
                            :href="item.href"
                            class="px-3 py-2 text-sm font-medium rounded-lg transition-colors"
                            :class="isExactActive(item.href)
                                ? 'text-blue-600 bg-blue-50'
                                : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50'"
                        >
                            {{ item.label }}
                        </Link>
                    </div>

                    <!-- User -->
                    <div class="flex items-center gap-3">
                        <span class="text-sm text-slate-600 hidden md:block">{{ user.name }}</span>
                        <div class="w-9 h-9 bg-blue-600 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                            {{ user.name.charAt(0).toUpperCase() }}
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
            </div>
        </nav>

        <!-- Page Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <slot />
        </main>
    </div>
</template>
