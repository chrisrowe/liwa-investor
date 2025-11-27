<style>
    .multiselect__content-wrapper {
        position: initial;
    }
</style>
<style>
    #saveUserModal___BV_modal_backdrop_,
    #addSubfolderAccessModal___BV_modal_backdrop_, #removeSubfolderAccessModal___BV_modal_backdrop_,
    #addFolderAccessModal___BV_modal_backdrop_, #removeFolderAccessModal___BV_modal_backdrop_  {
        opacity: 0.5;
    }
</style>
<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ !$page.userForEdit ? 'Create User' : 'Update User' }}
            </h2>

            <jet-nav-link :href="route('user.index')">
                <js-button>
                    Back
                </js-button>
            </jet-nav-link>
        </template>
        <div>
            <div class="max-w-6xl mx-auto py-10 sm:px-6 lg:px-8">
                <jet-form-section>
                    <template #title>
                        Profile Information
                    </template>

                    <template #description>
                        Update user's profile information and email address.
                    </template>

                    <template #form>
                        <!-- Name -->
                        <div class="col-span-6 sm:col-span-4">
                            <jet-label for="name" value="Name" />
                            <jet-input id="name" type="text" class="mt-1 block w-full" v-model="form.name"
                                autocomplete="name" />
                            <jet-input-error v-if="$page.errors.name" :message="$page.errors.name[0]" class="mt-2" />
                        </div>

                        <!-- Email -->
                        <div class="col-span-6 sm:col-span-4">
                            <jet-label for="email" value="Email" />
                            <jet-input id="email" type="email" class="mt-1 block w-full" v-model="form.email" />
                            <jet-input-error v-if="$page.errors.email" :message="$page.errors.email[0]" class="mt-2" />
                        </div>

                        <div class="col-span-6 sm:col-span-4" v-if="superAdmin">
                            <jet-label for="roles" value="Role" />
                            <multiselect v-model="role" :options="$page.roles" :multiple="false" :close-on-select="true"
                                :clear-on-select="false" :preserve-search="true" placeholder="Check roles" label="name"
                                :custom-label="getUserRoleLabelName" track-by="id" :preselect-first="true"
                                @input="changeRole">
                                <template slot="selection" slot-scope="{ values, search, isOpen }">
                                    <span class="multiselect__single"
                                        v-if="values.length &amp;&amp; !isOpen">{{ values.length }} options selected
                                    </span>
                                </template>
                            </multiselect>
                            <jet-input-error v-if="$page.errors.role" :message="$page.errors.role[0]" class="mt-2" />
                        </div>
                        <div v-if="showSendWelcomeEmailCheckbox()"
                            class="col-span-6 sm:col-span-4"
                        >
                            <jet-label for="send_welcome_email" value="Send Welcome Email" />
                            <b-form-checkbox id="send_welcome_email" name="send_welcome_email" :value="1"
                                :unchecked-value="0" v-model="form.send_welcome_email" />
                        </div>
                    </template>

                    <template #actions>
                        <jet-action-message :on="form.recentlySuccessful" class="mr-3">
                            Saved.
                        </jet-action-message>

                        <js-button
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                            @click.native="storeUser()"
                        >
                            Save
                        </js-button>

                        <button v-if="$page.userForEdit" @click="sendResetPassword" type="button"
                            class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray transition ease-in-out duration-150"
                            style="margin-left: 15px;">
                            Send Password Reset Email
                        </button>

                        <button v-if="$page.userForEdit" @click="copyPasswordResetLink" type="button"
                            class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray transition ease-in-out duration-150"
                            style="margin-left: 15px;">
                            Copy Password Reset Link
                        </button>

                        <js-danger-button v-if="$page.userForEdit" @click.native="showDeleteModal($page.userForEdit)"
                            style="margin-left: 15px;">
                            Delete
                        </js-danger-button>
                    </template>
                </jet-form-section>

                <jet-action-section v-if="(form.role === 'investor')">
                    <template #title>
                        Investor folders
                    </template>

                    <template v-if="$page.folders.length" #description>
                        Folders the investor has access to.
                    </template>

                    <!-- Start Investor Folder Permissions -->
                    <template #content>
                        <div class="grid grid-cols-6 gap-6 mb-3">
                            <div class="col-span-12 sm:col-span-12">
                                <jet-label for="folders" value="Permission to folders" />
                                 <!-- Folder Permissions First Level -->
                                <div v-for="folder in $page.allFolders" :key="folder.name">
                                     <label :for="`checkFolder${folder.name}`" :value="folder.name" class="flex items-center">
                                        <input type="checkbox" class="form-checkbox"  :id="`checkFolder${folder.name}`" :value="folder.name" v-model="form.folders">
                                        <span class="ml-2 text-sm text-gray-600" style="text-transform:capitalize">{{folder.name}}</span>
                                    </label>
                                     <!-- Folder Permissions Second Level -->
                                    <div class="pl-4">
                                        <div v-for="subfolder in folder.subfolders" :key="subfolder.name">
                                             <label :for="`checkSubolder${subfolder.id}`" :value="subfolder.name" class="flex items-center">
                                                <input type="checkbox" class="form-checkbox"  :id="`checkSubolder${subfolder.id}`" :value="subfolder.id" v-model="form.subfolders">
                                                <span class="ml-2 text-sm text-gray-600" style="text-transform:capitalize">{{subfolder.name}}</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <jet-input-error v-if="$page.errors.role" :message="$page.errors.folders[0]" class="mt-2" />
                                <jet-input-error v-if="$page.errors.role" :message="$page.errors.subfolders[0]" class="mt-2" />
                            </div>

                            <div class="col-span-12 sm:col-span-12 text-right">
                                <jet-action-message :on="form.recentlySuccessful" class="mr-3 d-inline-block">
                                    Saved.
                                </jet-action-message>

                                <js-button
                                    :class="{ 'opacity-25': form.processing }"
                                    :disabled="form.processing"
                                    @click.native="$page.userForEdit ? saveFolders() : storeUser()"
                                >
                                    Save
                                </js-button>
                            </div>
                        </div>
                    </template>
                     <!-- End Investor Folder Permissions -->
                </jet-action-section>
            </div>
        </div>

        <b-modal v-if="$page.userForEdit" ref="deleteModal" hide-footer centered
            :title="`Delete ${$page.userForEdit.name}`">
            <div class="d-block text-center">
                <h3>Are you sure you want to remove this user?</h3>
            </div>
            <b-button class="mt-3" variant="outline-danger" block @click="deleteUser">Yes</b-button>
            <b-button class="mt-2" variant="outline-success" block @click="closeDeleteModal">No</b-button>
        </b-modal>
        <b-modal v-if="form.name"
                 ref="saveModal"
                 id="saveUserModal"
                 hide-footer centered :title="`Save ${$inertia.form.name}`">
            <div class="d-block text-center">
                <h3>Are you sure you want to save this user?</h3>
            </div>
            <b-button class="mt-3" variant="outline-danger" block @click="storeUser">Yes</b-button>
            <b-button class="mt-2" variant="outline-success" block @click="closeSaveUserModal">No</b-button>
        </b-modal>

        <b-modal v-if="selectedSubfolder && subfoldersForSaving"
                 ref="removeSubfolderModal"
                 id="removeSubfolderAccessModal"
                 hide-footer centered
                 :title="`Remove permission`"
                 @hidden="closeDeleteSubfolderPermissionModal(false)"
        >
            <div class="d-block text-center">
                <h3>Are you sure you want to remove permission to this subfolder?</h3>
            </div>
            <b-button class="mt-3"
                      variant="outline-danger"
                      block
                      @click="changeSubfolderPermission(subfoldersForSaving);closeDeleteSubfolderPermissionModal()"
            >
                Yes
            </b-button>
            <b-button class="mt-2"
                      variant="outline-success"
                      block
                      @click="closeDeleteSubfolderPermissionModal(false)"
            >
                No
            </b-button>
        </b-modal>
        <b-modal v-if="!selectedSubfolder && subfoldersForSaving.length"
                 ref="addSubfolderModal"
                 id="addSubfolderAccessModal"
                 hide-footer centered
                 :title="`Add permission`"
                 @hidden="closeAddSubfolderPermissionModal(false)"
        >
            <div class="d-block text-center">
                <h3>Are you sure you want to add permission to this subfolder?</h3>
            </div>
            <b-button class="mt-3"
                      variant="outline-danger"
                      block
                      @click="changeSubfolderPermission(subfoldersForSaving);closeAddSubfolderPermissionModal()"
            >
                Yes
            </b-button>
            <b-button class="mt-2"
                      variant="outline-success"
                      block
                      @click="closeAddSubfolderPermissionModal(false)"
            >
                No
            </b-button>
        </b-modal>

        <b-modal v-if="selectedFolder && foldersForSaving"
             ref="removeFolderModal"
             id="removeFolderAccessModal"
             hide-footer centered
             :title="`Remove permission`"
             @hidden="closeDeleteFolderPermissionModal(false)"
        >
            <div class="d-block text-center">
                <h3>Are you sure you want to remove permission to this folder?</h3>
            </div>
            <b-button class="mt-3"
                      variant="outline-danger"
                      block
                      @click="changeFolderPermission(foldersForSaving);closeDeleteFolderPermissionModal()"
            >
                Yes
            </b-button>
            <b-button class="mt-2"
                      variant="outline-success"
                      block
                      @click="closeDeleteFolderPermissionModal(false)"
            >
                No
            </b-button>
        </b-modal>
        <b-modal v-if="!selectedFolder && foldersForSaving.length"
                 ref="addFolderModal"
                 id="addFolderAccessModal"
                 hide-footer centered
                 :title="`Add permission`"
                 @hidden="closeAddFolderPermissionModal(false)"
        >
            <div class="d-block text-center">
                <h3>Are you sure you want to add permission to this folder?</h3>
            </div>
            <b-button class="mt-3"
                      variant="outline-danger"
                      block
                      @click="changeFolderPermission(foldersForSaving);closeAddFolderPermissionModal()"
            >
                Yes
            </b-button>
            <b-button class="mt-2"
                      variant="outline-success"
                      block
                      @click="closeAddFolderPermissionModal(false)"
            >
                No
            </b-button>
        </b-modal>
    </app-layout>
</template>

<script>
    import JetFormSection from '@/Jetstream/FormSection'
    import JetActionSection from '@/Jetstream/ActionSection'
    import JetInput from '@/Jetstream/Input'
    import JetInputError from '@/Jetstream/InputError'
    import JetLabel from '@/Jetstream/Label'
    import JetActionMessage from '@/Jetstream/ActionMessage'
    import JetSecondaryButton from '@/Jetstream/SecondaryButton'
    import AppLayout from '@/Layouts/AppLayout'
    import Multiselect from 'vue-multiselect'
    import JsButton from '../../../Jetstream/Button'
    import JsDangerButton from '../../../Jetstream/DangerButton'
    import JetNavLink from '@/Jetstream/NavLink'

    export default {
        components: {
            AppLayout,
            JetActionMessage,
            JsButton,
            JsDangerButton,
            JetFormSection,
            JetInput,
            JetInputError,
            JetLabel,
            JetSecondaryButton,
            Multiselect,
            JetNavLink,
            JetActionSection
        },

        props: ['user'],

        data() {
            return {
                form: this.$inertia.form({
                    '_method': 'POST',
                    name: this.$page.userForEdit ? this.$page.userForEdit.name : '',
                    email: this.$page.userForEdit ? this.$page.userForEdit.email : '',
                    role: this.$page.userForEdit ? this.$page.userForEdit.roles[0].name : 'investor',
                    folders: this.$page.userForEdit && this.$page.folders ? this.$page.folders : [],
                    subfolders: this.$page.userForEdit && this.$page.subfolders ? this.$page.subfolders.map(({ id }) => id) : [],
                    send_welcome_email: 1
                }, {
                    bag: 'storeUser',
                    resetOnSuccess: false,
                }),
                role: this.$page.userForEdit ? this.$page.userForEdit.roles[0] : 'investor',
                folders: this.$page.userForEdit && this.$page.folders ? this.$page.folders : null,
                subfolders: this.$page.userForEdit && this.$page.subfolders ? this.$page.subfolders : null,
                oldFolders: this.$page.userForEdit && this.$page.folders ? this.$page.folders : null,
                oldSubfolders: this.$page.userForEdit && this.$page.subfolders ? this.$page.subfolders : null,
                selectedSubfolder: null,
                selectedFolder: null,
                subfoldersForSaving: [],
                foldersForSaving: []
            }
        },
        computed: {
            superAdmin() {
                return this.user && this.user.roles[0].name == 'super-admin'
            }
        },

        methods: {
            openToast(title, type = 'success') {
                // console.log('in toaster', title);
                this.$bvToast.toast(title, {
                  // title: title,
                  variant: type,
                  toaster: 'b-toaster-top-right',
                  solid: true,
                  appendToast: false
                })
            },
            storeUser() {
                console.log(this.$page)
                if (!this.$page.userForEdit) {
                    this.form.post(route('user.store').url(), {
                        preserveScroll: true
                    }).then(response => {
                        if (!this.$page.errors.hasOwnProperty('email') &&
                            !this.$page.errors.hasOwnProperty('folders') &&
                            !this.$page.errors.hasOwnProperty('name') &&
                            !this.$page.errors.hasOwnProperty('role') &&
                            !this.$page.errors.hasOwnProperty('subfolders')
                        ) {
                            this.openToast('User has been created.')
                        } else {
                            this.openToast('Please, check errors.', 'danger')
                        }
                    });
                } else {
                    this.form.post(route('user.update', this.$page.userForEdit), {
                        preserveScroll: true
                    })
                    .then(response => {
                        this.openToast('User has been updated.')
                    });
                }
            },
            saveFolders() {
                if (!this.$page.userForEdit) {
                    return;
                }
                axios.post(
                    `/admin/users/${this.$page.userForEdit.id}/save-folders`,
                    {
                        'folders': this.form.folders,
                        'subfolders': this.form.subfolders
                    }
                )
                    .then(response => {
                        this.openToast('Folder permissions was saved')
                    })
                    .catch(error => {
                        //
                    })
            },
            saveSubfolders() {
                if (!this.$page.userForEdit) {
                    return;
                }
                axios.post(
                    `/admin/users/${this.$page.userForEdit.id}/save-subfolders`,
                    {
                        'subfolders': this.form.subfolders
                    }
                )
                    .then(response => {
                        this.openToast('Subolder permissions was saved')
                    })
                    .catch(error => {
                        //
                    })
            },
            changeRole(role) {
                if (role) {
                    this.form.role = role.name;
                }
            },
            showSendWelcomeEmailCheckbox() {
                return !this.$page.userForEdit
            },
            showDeleteModal(user) {
                this.$nextTick().then(response => {
                    this.$refs['deleteModal'].show()
                })
            },
            showSaveModal() {
                this.$nextTick().then(response => {
                    this.$refs['saveModal'].show()
                })
            },
            closeDeleteModal() {
                this.$refs['deleteModal'].hide()
            },
            closeSaveUserModal() {
                this.$refs['saveModal'].hide()
            },
            deleteUser() {
                axios.post(`/admin/users/destroy/${this.$page.userForEdit.id}`, {
                        '_method': 'DELETE'
                    })
                    .then(response => {
                        this.$inertia.visit(this.route('user.index'), {
                            method: 'get'
                        })
                        this.$refs['deleteModal'].hide()
                    })
                    .catch(error => {
                        this.$refs['deleteModal'].hide()
                    })
            },
            getUserRoleLabelName({
                name
            }) {
                if (!this.superAdmin) {
                    return
                }
                let roleName = name;
                roleName = roleName.replace(/-/g, ' ');

                return roleName
                    .toLowerCase()
                    .split(' ')
                    .map(function (word) {
                        return word[0].toUpperCase() + word.substr(1);
                    })
                    .join(' ');
            },
            sendResetPassword() {
                event.preventDefault();
                axios.post(`/admin/users/send-reset-password/${this.$page.userForEdit.id}`);
            },
            copyPasswordResetLink() {
                event.preventDefault();
                axios.get(`/admin/users/${this.$page.userForEdit.id}/get-password-reset-link`)
                    .then(response => {
                        this.$copyText(response.data)
                        const h = this.$createElement
                        this.$bvModal.msgBoxOk([
                            h('p', {
                                class: 'mb-2'
                            }, ['A new password reset link has been created for this user and copied to your clipboard.']),
                            h('p', {
                                class: 'mb-2'
                            }, ['Any old password reset emails will no longer work.']),
                             h('p', {
                                class: 'mb-2'
                            }, ['Copy this link and email it directly to your investor to set their password.']),
                            h('b-form-input', {
                                class: ['form-input bg-gray-200'],
                                style: 'width: 100%',
                                props: {
                                    value: response.data
                                }
                            })
                        ])
                    });
            },
            getFolderLabelName(name) {
                let folderName = name;
                folderName = folderName.replace(/-/g, ' ');

                return folderName
                    .toLowerCase()
                    .split(' ')
                    .map(function(word) {
                        return word[0].toUpperCase() + word.substr(1);
                    })
                    .join(' ');
            },
            /// working with folder permissions
            changeFolderPermission(folders) {
                this.form.folders.length = 0;
                for (let i = 0; i < folders.length; i++) {
                    this.form.folders.push(folders[i])
                }
                this.$page.folders = folders;
                this.$set(this, 'oldFolders', this.folders)
            },
            /// working with subfolder permissions
            changeSubfolderPermission(subfolders) {
                this.form.subfolders.length = 0;
                for (let i = 0; i < subfolders.length; i++) {
                    this.form.subfolders.push(subfolders[i].id)
                }
                this.$page.subfolders = subfolders;
                // console.log()
                this.$set(this, 'oldSubfolders', this.subfolders)

            },
        }
    }
</script>
