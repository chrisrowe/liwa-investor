<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Activity
            </h2>
        </template>

        <div class="pt-6 max-w-6xl mx-auto sm:px-6 lg:px-8 d-flex justify-between align-middle">
            <a class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent
                        rounded-md font-semibold text-xs text-white hover:text-white uppercase tracking-widest
                        hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900
                        focus:shadow-outline-gray transition ease-in-out duration-150 card-link"
                    :href="route('activity-report.export')"
            >
                Export Report
            </a>
        </div>

        <div class="py-12">

            <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg" :class="$page.activities.data.length ? '': 'p-5'">
                    <ul>
                        <li v-for="activity in $page.activities.data" class="border-b p-4 col-12">
                            <div class="col-lg-8 col-xs-12 d-inline-block" style="line-height:1.2">
                                <div>
                                    <div v-if="activity.actionName === 'remove_access' && activity.folder && !activity.subfolder">
                                        Removed access to the "{{ activity.folder }}" folder for user {{ activity.actionDataUserName }} by {{ activity.initiatorName }}.
                                    </div>
                                    <div v-if="activity.actionName === 'remove_access' && activity.folder && activity.subfolder">
                                        Removed access to the "{{ activity.subfolder }}" subfolder for user {{ activity.actionDataUserName }} by {{ activity.initiatorName }}.
                                    </div>
                                    <div v-if="activity.actionName === 'add_access' && activity.folder && !activity.subfolder">
                                        Added access to the "{{ activity.folder }}" folder for user {{ activity.actionDataUserName }} by {{ activity.initiatorName }}.
                                    </div>
                                    <div v-if="activity.actionName === 'add_access' && activity.folder && activity.subfolder">
                                        Added access to the "{{ activity.subfolder }}" subfolder for user {{ activity.actionDataUserName }} by {{ activity.initiatorName }}.
                                    </div>
                                    <div v-if="activity.actionName === 'visit' && activity.folder && activity.subfolder">
                                        User {{ activity.initiatorName }} visited subfolder "{{ activity.subfolder }}" in folder "{{ activity.folder }}".
                                    </div>
                                    <div v-if="activity.actionName === 'visit' && activity.folder && !activity.subfolder">
                                        User {{ activity.initiatorName }} visited folder "{{ activity.folder }}".
                                    </div>
                                    <div v-if="activity.actionName === 'download' && activity.actionDataFileName">
                                        User {{ activity.initiatorName }} downloaded file "{{ activity.actionDataFileName }}"
                                    </div>
                                    <div v-if="activity.actionName === 'view' && activity.actionDataFileName">
                                        User {{ activity.initiatorName }}  viewed file "{{ activity.actionDataFileName }}"
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-xs-12 d-inline-block" style="line-height:1.2">
                                <div style="float: right;font-weight: 800;">
                                    {{activity.created_at}}
                                </div>
                            </div>
                        </li>
                    </ul>
                    <pagination-links v-if="$page.activities.data.length" :urlsArray="$page.paginatedLinks"
                        :previousPageUrl="$page.activities.prev_page_url" :nextPageUrl="$page.activities.next_page_url">
                    </pagination-links>
                    <div v-if="!$page.activities.data.length">
                        No activities yet
                    </div>
                </div>
            </div>
        </div>

    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout'
    import PaginationLinks from '../Components/PaginationLinks'
    export default {
        components: {
            AppLayout,
            PaginationLinks
        },
        data() {
            return {
                activities: this.$page.activities
            }
        }
    }
</script>
