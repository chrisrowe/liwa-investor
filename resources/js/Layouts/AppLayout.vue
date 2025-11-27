<template>
    <div class="min-h-screen bg-gray-100">

        <website-nav></website-nav>

        <nav class="background-blue border-b border-gray-100">
            <!-- Primary Navigation Menu -->
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">

                        <!-- Navigation Links -->
                        <div class="hidden space-x-8 sm:-my-px sm:flex">
                            <jet-nav-link :href="route('dashboard')" :active="route().current('dashboard')">
                                Dashboard
                            </jet-nav-link>

                            <jet-nav-link
                                v-if="$page.user_roles.includes('super-admin') ||
                                    $page.user_roles.includes('admin') ||
                                    $page.user_permissions.includes('view folder research')"
                                :href="route('research')" :active="route().current('research')"
                            >
                                Research
                            </jet-nav-link>

                            <jet-nav-link
                                v-if="$page.user_roles.includes('super-admin') ||
                                    $page.user_roles.includes('admin') ||
                                    $page.user_permissions.includes('view folder videos')"
                                :href="route('videos')" :active="route().current('videos')"
                            >
                                Videos
                            </jet-nav-link>

                            <jet-nav-link
                                v-if="$page.user_roles.includes('super-admin') ||
                                    $page.user_roles.includes('admin') ||
                                    $page.user_permissions.includes('view folder other')"
                                :href="route('liwa-fund-reports')"
                                :active="route().current('liwa-fund-reports')"
                            >
                                Liwa Fund Reports
                            </jet-nav-link>

                            <jet-nav-link
                                v-if="$page.user_roles.includes('super-admin') || $page.user_roles.includes('admin')"
                                :href="route('access-report.index')"
                                :active="route().current('access-report.index')"
                            >
                                Access Report
                            </jet-nav-link>

                            <!-- Support -->
                            <jet-nav-link v-if="$page.user_roles.includes('super-admin') || $page.user_roles.includes('admin')"
                                :href="route('support')"
                            >
                                Support
                            </jet-nav-link>
                        </div>
                    </div>

                    <!-- Settings Dropdown -->
                    <div class="hidden sm:flex sm:items-center sm:ml-6">
                        <div class="ml-3 relative">
                            <jet-dropdown align="right" width="48">
                                <template #trigger>
                                    <button v-if="$page.jetstream.managesProfilePhotos" class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition duration-150 ease-in-out">
                                        <img class="h-8 w-8 rounded-full object-cover" :src="$page.user.profile_photo_url" :alt="$page.user.name" />
                                    </button>

                                    <button v-else class="flex items-center text-sm font-medium text-white hover:text-gray-300 hover:border-gray-300 focus:outline-none transition duration-150 ease-in-out">
                                        <div>{{ $page.user.name }}</div>

                                        <div class="ml-1">
                                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </button>
                                </template>

                                <template #content>
                                    <!-- Account Management -->
                                    <div class="block px-4 py-2 text-xs text-gray-400">
                                        Manage Account
                                    </div>

                                    <jet-dropdown-link :href="route('profile.show')">
                                        Profile
                                    </jet-dropdown-link>


                                    <div class="border-t border-gray-100"></div>

                                    <!-- Team Management -->
                                    <template v-if="$page.user_roles.includes('super-admin') || $page.user_roles.includes('admin')">
                                        <!-- Team Settings -->
                                        <jet-dropdown-link
                                            :href="route('user.index')"
                                        >
                                            Users
                                        </jet-dropdown-link>

                                        <div class="border-t border-gray-100"></div>
                                    </template>

                                    <!-- Authentication -->
                                    <form @submit.prevent="logout" style="margin-top: 0px;">
                                        <jet-dropdown-link as="button">
                                            Logout
                                        </jet-dropdown-link>
                                    </form>
                                </template>
                            </jet-dropdown>
                        </div>
                    </div>

                    <!-- Hamburger -->
                    <div class="-mr-2 flex items-center sm:hidden">
                        <button @click="showingNavigationDropdown = ! showingNavigationDropdown" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path :class="{'hidden': showingNavigationDropdown, 'inline-flex': ! showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                <path :class="{'hidden': ! showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Responsive Navigation Menu -->
            <div :class="{'block': showingNavigationDropdown, 'hidden': ! showingNavigationDropdown}" class="sm:hidden">
                <div class="pt-2 pb-3 space-y-1">
                    <responsive-nav-link :href="route('dashboard')" :active="route().current('dashboard')">
                        Dashboard
                    </responsive-nav-link>
                    <responsive-nav-link :href="route('research')" :active="route().current('research')">
                        Research
                    </responsive-nav-link>
                    <responsive-nav-link :href="route('videos')" :active="route().current('videos')">
                        Videos
                    </responsive-nav-link>
                    <responsive-nav-link :href="route('liwa-fund-reports')" :active="route().current('liwa-fund-reports')">
                        Liwa Fund Reports
                    </responsive-nav-link>
                </div>

                <!-- Responsive Settings Options -->
                <div class="pt-4 pb-1 border-t border-gray-200">
                    <div class="flex items-center px-4">
                        <div class="flex-shrink-0">
                            <img class="h-10 w-10 rounded-full" :src="$page.user.profile_photo_url" :alt="$page.user.name" />
                        </div>

                        <div class="ml-3">
                            <div class="font-medium text-base text-gray-800">{{ $page.user.name }}</div>
                            <div class="font-medium text-sm text-gray-500">{{ $page.user.email }}</div>
                        </div>
                    </div>

                    <div class="mt-3 space-y-1">
                        <responsive-nav-link :href="route('profile.show')" :active="route().current('profile.show')">
                            Profile
                        </responsive-nav-link>

                        <responsive-nav-link :href="route('api-tokens.index')" :active="route().current('api-tokens.index')" v-if="$page.jetstream.hasApiFeatures">
                            API Tokens
                        </responsive-nav-link>

                        <!-- Authentication -->
                        <form method="POST" @submit.prevent="logout">
                            <responsive-nav-link as="button">
                                Logout
                            </responsive-nav-link>
                        </form>

                    </div>
                </div>
            </div>
        </nav>
        <!-- Page Heading -->
        <header class="bg-white shadow">
            <div class="max-w-6xl mx-auto py-6 px-4 sm:px-6 lg:px-8 d-flex justify-between align-middle">
                <slot name="header"></slot>
            </div>
        </header>

        <!-- Page Content -->
        <main>
            <slot></slot>
        </main>

        <!-- Modal Portal -->
        <portal-target name="modal" multiple>
        </portal-target>

        <site-footer></site-footer>
    </div>
</template>

<script>
    import JetApplicationMark from '@/Jetstream/ApplicationMark'
    import JetDropdown from '@/Jetstream/Dropdown'
    import JetDropdownLink from '@/Jetstream/DropdownLink'
    import JetNavLink from '@/Jetstream/NavLink'
    import JetResponsiveNavLink from '@/Jetstream/ResponsiveNavLink'
    import ResponsiveNavLink from '../Components/ResponsiveNavLink'
    import WebsiteNav from '../Components/WebsiteNav'
    import SiteFooter from '../Components/SiteFooter'
    export default {
        components: {
            WebsiteNav,
            SiteFooter,
            JetApplicationMark,
            JetDropdown,
            JetDropdownLink,
            JetNavLink,
            ResponsiveNavLink
        },

        data() {
            return {
                showingNavigationDropdown: false,
            }
        },

        methods: {
            switchToTeam(team) {
                this.$inertia.put(route('current-team.update'), {
                    'team_id': team.id
                }, {
                    preserveState: false
                })
            },

            logout() {
                axios.post(route('logout').url()).then(response => {
                    window.location = '/';
                })
            },
        }
    }
</script>
