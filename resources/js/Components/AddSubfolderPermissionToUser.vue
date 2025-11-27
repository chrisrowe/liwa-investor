<style>
    .multiselect__tags-wrap {
        display: none;
    }
    .multiselect__strong {
        display: none;
    }
    #addAccessModal___BV_modal_backdrop_, #removeAccessModal___BV_modal_backdrop_ {
        opacity: 0.5;
    }
</style>
<template>
    <div class="col-span-12">
        <multiselect  v-model="mutableFolderInvestors"
                    label="name"
                    track-by="id"
                    placeholder="Type to search"
                    open-direction="bottom"
                    :options="optionInvestors"
                    :multiple="true"
                    :searchable="true"
                    :loading="isLoading"
                    :internal-search="false"
                    :clear-on-select="true"
                    :close-on-select="true"
                    :options-limit="300"
                    :limit="1"
                    :limit-text="limitText"
                    :max-height="600"
                    :show-no-results="false"
                    :show-no-options="false"
                    :hide-selected="true"
                    :block-keys="['Delete']"
                    @input="chnageFolderInvestors"
                    @search-change="searchInvestors"
                    @open="openDropdown"
        >
            <template slot="option" slot-scope="props">
                <div class="flex-grow" style="line-height:1.2">
                    <div style="font-weight: 800;">
                        {{props.option.name}}
                    </div>
                    <div>
                        {{props.option.email}}
                    </div>
                </div>
            </template>
            <span slot="noResult">
                Oops! No elements found. Consider changing the search query.
            </span>
        </multiselect>
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg" :class="mutableFolderInvestors.length ? '': 'p-5'">
            <ul>
                <li v-for="investor in mutableFolderInvestors" :key="investor.id" class="border-b d-flex align-items-center p-4">
                    <div class="flex-grow" style="line-height:1.2">
                        <div style="font-weight: 800;">
                            {{investor.name}}
                        </div>
                        <div>
                            {{investor.email}}
                        </div>
                    </div>
                    <div>
                        <jet-button @click.native="removeInvestor(investor);"
                        >
                            Remove Access
                        </jet-button>
                    </div>
                </li>
            </ul>
            <div v-if="!mutableFolderInvestors.length">
                No investors have been added yet
            </div>
        </div>
        <b-modal v-if="selectedInvestor"
                 ref="removeModal"
                 id="removeAccessModal"
                 hide-footer centered
                 :title="`Remove permission`"
        >
            <div class="d-block text-center">
                <h3>Are you sure you want to remove permission to this user?</h3>
            </div>
            <b-button class="mt-3" variant="outline-danger" block @click="removeInvestor(selectedInvestor);closeDeleteModal()">Yes</b-button>
            <b-button class="mt-2" variant="outline-success" block @click="closeDeleteModal">No</b-button>
        </b-modal>
        <b-modal v-if="usersForSaving.length"
                 ref="addModal"
                 id="addAccessModal"
                 hide-footer centered
                 :title="`Add permission`"
        >
            <div class="d-block text-center">
                <h3>Are you sure you want to add permission to this user?</h3>
            </div>
            <b-button class="mt-3" variant="outline-danger" block @click="chnageFolderInvestors(usersForSaving);closeAddModal()">Yes</b-button>
            <b-button class="mt-2" variant="outline-success" block @click="closeAddModal(false)">No</b-button>
        </b-modal>
    </div>
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

        props: ['user', 'folderInvestors', 'allInvestors', 'folderType', 'folder', 'saveApi'],

        data() {
            return {
                usersForSaving: [],
                selectedInvestor: null,
                form: this.$inertia.form({
                    '_method': 'POST',
                    investors: [],
                }, {
                    bag: 'saveFolder',
                    resetOnSuccess: false,
                }),
                isLoading: false,
                mutableFolderInvestors: this.getMutableFolderInvestors(this.folderInvestors),
                apiForSaving: this.saveApi ?
                    this.saveApi :
                    this.route(this.folderType + '.save_subfolder_permission_to_user', this.folder),
                optionInvestors: this.allInvestors
            }
        },
        methods: {
            getMutableFolderInvestors(folderInvestors) {
                let copiedFolderInvestors = [];
                for (let i = 0; i < folderInvestors.length; i++) {
                    copiedFolderInvestors.push(folderInvestors[i]);
                }

                return copiedFolderInvestors;
            },
            openToast(title) {
                this.$bvToast.toast(title, {
                  // title: title,
                  variant: 'success',
                  toaster: 'b-toaster-top-right',
                  solid: true,
                  appendToast: false
                })
            },
            savePermissions() {
                return new Promise((resolve, reject) => {
                    axios.post(this.apiForSaving, {investors: this.form.investors})
                        .then(response => {
                            resolve(this.mutableFolderInvestors);
                        });
                });
            },
            chnageFolderInvestors(users) {
                let investors = [];
                let folderInvestors = [];

                users.forEach((user) => {
                     investors.push(user.id);
                     folderInvestors.push(user)
                });

                this.form.investors = investors;
                this.mutableFolderInvestors = folderInvestors;

                this.savePermissions().then(result => {
                    this.openToast('Permission was added to user')
                });
            },
            limitText (count) {
              return ``
            },
            clearAll () {
              // this.allInvestors = []
            },
            removeInvestor(investor) {
                var removeIndex = this.mutableFolderInvestors.map(x => {
                  return x.id;
                })
                .indexOf(investor.id);
                this.mutableFolderInvestors.splice(removeIndex, 1);

                let investors = [];
                this.mutableFolderInvestors.forEach((user) => {
                     investors.push(user.id);
                });
                this.form.investors = investors;

                this.$nextTick().then(response => {
                    var investorIndexInOptions = this.optionInvestors.map(x => {
                      return x.id;
                    })
                    .indexOf(investor.id);
                    if (investorIndexInOptions < 0) {
                        this.optionInvestors.push(investor)
                    }
                    this.savePermissions().then(result => {
                        this.openToast('Permission was removed from user')
                    });
                })
            },
            openDropdown() {
                if (!this.optionInvestors.length) {
                    this.optionInvestors = this.allInvestors
                }
            },
            openDeleteModal(investor) {
                this.$set(this, 'selectedInvestor', investor)
                this.$nextTick().then(response => {
                    this.$refs['removeModal'].show()
                })
            },
            openAddModal(users) {
                this.$set(this, 'usersForSaving', users)
                this.$nextTick().then(response => {
                    this.$refs['addModal'].show()
                })
            },
            closeDeleteModal() {
                this.$refs['removeModal'].hide()
            },
            closeAddModal($isYes = true) {
                if (!$isYes) {
                    this.mutableFolderInvestors = this.folderInvestors;
                }
                this.$refs['addModal'].hide()
            },
            searchInvestors(query) {
                this.optionInvestors = this.allInvestors
                    .filter(investor => {
                        return (investor.name.indexOf(query) > -1 || investor.email.indexOf(query) > -1)
                    });
            }
        },
    }
</script>
