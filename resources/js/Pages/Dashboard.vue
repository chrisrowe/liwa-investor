<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
        </template>

        <div class="py-12 dashboard">
            <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
                
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    
                    <welcome v-if="!$page.user_roles.includes('super-admin') && !$page.user_roles.includes('admin')" />
                    <admin-dashboard-component v-if="$page.user_roles.includes('super-admin') || $page.user_roles.includes('admin')">
                    </admin-dashboard-component>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout'
    import Welcome from '@/Jetstream/Welcome'
    import AdminDashboardComponent from '../Components/AdminDashboardComponent'

    export default {
        components: {
            AppLayout,
            Welcome,
            AdminDashboardComponent
        },
    }
</script>

