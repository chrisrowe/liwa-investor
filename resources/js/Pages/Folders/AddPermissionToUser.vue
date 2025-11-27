<style>
    .multiselect__content-wrapper {
        position: initial;
    }
</style>
<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ '"' +  $page.folderType + '"' + ' folder permissions' }}
            </h2>
        </template>
        <div>
            <div class="max-w-6xl mx-auto py-10 sm:px-6 lg:px-8">
                <jet-form-section @submitted="savePermissions">
                    <template #form>
                        <div class="col-span-6 sm:col-span-4">
                            <jet-label for="investors" value="Investors" />
                            <multiselect v-model="folderInvestors"
                                        :options="$page.allInvestors"
                                        :multiple="true"
                                        :close-on-select="false"
                                        :clear-on-select="false"
                                        :preserve-search="true"
                                        placeholder="Check investors"
                                        label="name"
                                        track-by="id"
                                        :preselect-first="false"
                                        @input="chnageFolderInvestors"
                            >
                                <template slot="selection" slot-scope="{ values, search, isOpen }">
                                    <span class="multiselect__single" v-if="values.length &amp;&amp; !isOpen">{{ values.length }} options selected
                                    </span>
                                </template>
                            </multiselect>
                        </div>
                    </template>

                    <template #actions>
                        <jet-action-message :on="form.recentlySuccessful" class="mr-3">
                            Saved.
                        </jet-action-message>

                        <jet-button :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            Save
                        </jet-button>
                    </template>
                </jet-form-section>
            </div>
        </div>
    </app-layout>
</template>

<script>
    import JetButton from '@/Jetstream/Button'
    import JetFormSection from '@/Jetstream/FormSection'
    import JetInput from '@/Jetstream/Input'
    import JetInputError from '@/Jetstream/InputError'
    import JetLabel from '@/Jetstream/Label'
    import JetActionMessage from '@/Jetstream/ActionMessage'
    import JetSecondaryButton from '@/Jetstream/SecondaryButton'
    import AppLayout from '@/Layouts/AppLayout'
    import Multiselect from 'vue-multiselect'

    export default {
        components: {
            AppLayout,
            JetActionMessage,
            JetButton,
            JetFormSection,
            JetInput,
            JetInputError,
            JetLabel,
            JetSecondaryButton,
            Multiselect
        },

        props: ['user'],

        data() {
            // console.log('this.investor', this.investor);
            return {
                form: this.$inertia.form({
                    '_method': 'POST',
                    investors: []
                }, {
                    bag: 'saveFolder',
                    resetOnSuccess: false,
                }),
                folderInvestors: this.$page.investorsWithPermissions,
                photoPreview: null,
            }
        },

        methods: {
            savePermissions() {
                this.form.post(route(this.$page.folderType + '.save_permission_to_user'), {
                    preserveScroll: true
                })
                .then((response) => {
                    // console.log('response', response);
                    // console.log('form', this.form);
                });
            },
            chnageFolderInvestors(users) {
                let investors = [];

                users.forEach((user) => {
                     investors.push(user.id);
                });

                this.form.investors = investors;
            }
        },
    }
</script>
