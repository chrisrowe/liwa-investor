<style>
    #fileNotFound___BV_modal_backdrop_, #manageAccessInvestorsToFolderModal___BV_modal_backdrop_, #uploadFileModal___BV_modal_backdrop_ {
        opacity: 0.5;
    }
</style>
<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight pb-0">
                {{ $page.title }}
            </h2>
        </template>

        <div class="pt-6 max-w-6xl mx-auto sm:px-6 lg:px-8 d-flex justify-between align-middle">
            <js-button
                v-if="$page.user_roles.includes('super-admin') || $page.user_roles.includes('admin')"
                @click.native="showAccessFolderModal(null)"
            >
                Manage Access
            </js-button>
            <div>
                <upload-file-button
                    v-if="$page.user_roles.includes('super-admin') || $page.user_roles.includes('admin')"
                    :folderType="$page.folderType"
                    :fileFolder="$page.fileFolder"
                    style="display: contents;"
                >
                </upload-file-button>
                <jet-nav-link v-if="$page.user_roles.includes('super-admin') || $page.user_roles.includes('admin')"
                    :href="route($page.folderType + '.folder.create')">
                    <js-secondary-button>
                        Add folder
                    </js-secondary-button>
                </jet-nav-link>
            </div>
        </div>

        <div class="py-6" v-if="$page.folders.data.length">
            <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg"
                    :class="folders.data.length ? '': 'p-5'">
                    <ul>
                        <li v-for="folder in $page.folders.data" :key="folder.id"
                            class="border-b d-flex align-items-center p-4">

                            <div class="flex-grow" style="line-height:1.2">
                                <jet-nav-link class="text-gray-500 hover:text-gray-500"
                                    :href="route($page.folderType + '.folder.index', folder)">
                                    <span class="text-xl mr-3">
                                        <i class="fa fa-folder"></i>
                                    </span>
                                    {{folder.name}}
                                </jet-nav-link>

                            </div>
                            <span class="text-xl mr-3">

                            </span>
                            <div>
                                <jet-nav-link
                                    v-if="folder.id && ($page.user_roles.includes('super-admin') || $page.user_roles.includes('admin'))"
                                    :href="route($page.folderType + '.folder.edit', folder)">
                                    <js-button>
                                        Edit
                                    </js-button>
                                </jet-nav-link>
                                <js-button
                                    v-if="folder.id && ($page.user_roles.includes('super-admin') || $page.user_roles.includes('admin'))"
                                    @click.native="showAccessFolderModal(folder)"
                                    style="margin-left: 10px;"
                                >
                                    Manage Access
                                </js-button>
                                <js-danger-button
                                    v-if="folder.id && ($page.user_roles.includes('super-admin') || $page.user_roles.includes('admin'))"
                                    @click.native="showDeleteModal(folder)">
                                    Delete
                                </js-danger-button>
                            </div>
                        </li>
                    </ul>
                    <pagination-links
                        :withLinks="true"
                        :paginationItems="folders"
                     >
                    </pagination-links>
                </div>
            </div>
        </div>

        <file-list-component
            :files="$page.files"
            :user_roles="$page.user_roles"
            :folderType="$page.folderType"
            :paginatedLinks="$page.paginatedFileLinks"
            :fileFolder="null"
        >
        </file-list-component>

        <b-modal
            v-if="($page.user_roles.includes('super-admin') || $page.user_roles.includes('admin')) && folderForDelete"
            ref="deleteModal" hide-footer centered :title="`Delete ${folderForDelete.name}`">
            <div class="d-block text-center">
                <h3>
                    Are you sure you want to delete "{{ folderForDelete.name }}"" and all of its contents?
                </h3>
            </div>
            <b-button class="mt-3" variant="outline-danger" block @click="deleteFolder">Yes</b-button>
            <b-button class="mt-2" variant="outline-success" block @click="closeDeleteModal">No</b-button>
        </b-modal>
        <b-modal v-if="($page.user_roles.includes('super-admin') || $page.user_roles.includes('admin')) && selectedFolder"
                 ref="manageAccessInvestorsToFolderModal"
                 id="manageAccessInvestorsToFolderModal"
                 hide-footer
                 centered
                 :title="`Share with investors`"
        >
                <add-subfolder-permission-to-user
                    ref="userAccessFolderComponentRef"
                    :folderInvestors="investorsWithAccessToFolder"
                    :allInvestors="investorsWithoutAccessToFolder"
                    :folderType="$page.folderType"
                    :folder="selectedFolder"
                    :saveApi="!selectedFolder.id ? route($page.folderType + '.save_permission_to_user') : null"
                >
                </add-subfolder-permission-to-user>
                <div>
                    <button type="submit"
                            @click="savePermissions"
                            style="float:right;margin-top: 10px;"
                            class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray transition ease-in-out duration-150"
                    >
                        Done
                    </button>
                </div>
        </b-modal>

        <b-modal v-if="($page.user_roles.includes('super-admin') || $page.user_roles.includes('admin'))"
                 ref="uploadFileModal"
                 id="uploadFileModal"
                 hide-footer
                 centered
                 :title="`Upload file`"
        >
            <div class="d-block text-center">
                <h3>Are you sure you want to upload this file?</h3>
            </div>
            <b-button class="mt-3" variant="outline-danger" block @click="saveFile();cloaseUploadFileModal()">Yes</b-button>
            <b-button class="mt-2" variant="outline-success" block @click="cloaseUploadFileModal()">No</b-button>
        </b-modal>

    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout'
    import JsButton from '../../Jetstream/Button'
    import JsSecondaryButton from '../../Jetstream/SecondaryButton'
    import JsDangerButton from '../../Jetstream/DangerButton'
    import JetInputError from '@/Jetstream/InputError'
    import DialogModal from '../../Jetstream/DialogModal'
    import PaginationLinks from '../../Components/PaginationLinks'
    import JetNavLink from '@/Jetstream/NavLink'
    import AddSubfolderPermissionToUser from '../../Components/AddSubfolderPermissionToUser'
    import FileListComponent from '../../Components/FileListComponent'
    import UploadFileButton from '../../Components/UploadFileButton'
    export default {
        components: {
            AppLayout,
            JsButton,
            JsSecondaryButton,
            JsDangerButton,
            DialogModal,
            JetInputError,
            PaginationLinks,
            JetNavLink,
            AddSubfolderPermissionToUser,
            FileListComponent,
            UploadFileButton
        },
        data() {
            return {
                isFileLoaded: false,
                selectedFile: null,
                selectedFolder: null,
                folderForDelete: null,
                folders: this.$page.folders,
                uploadedFile: null,
                paginatedLinks: [],
                investorsWithoutAccessToFolder: [],
                investorsWithAccessToFolder: []
            }
        },
        methods: {
            openToast(title) {
                this.$bvToast.toast(title, {
                  // title: title,
                  variant: 'success',
                  toaster: 'b-toaster-top-right',
                  solid: true,
                  appendToast: false
                })
            },
            showDeleteModal(admin) {
                this.$set(this, 'folderForDelete', admin)
                this.$nextTick().then(response => {
                    this.$refs['deleteModal'].show()
                })
            },
            closeDeleteModal() {
                this.$refs['deleteModal'].hide()
            },
            deleteFolder() {
                let deleteFolderApi = this.folderForDelete.type === 'other' ?
                    `liwa-fund-reports.folder.destroy` :
                    `${this.folderForDelete.type}.folder.destroy`;
                this.$inertia.post(
                    this.route(deleteFolderApi, this.folderForDelete.id), {
                        _method: 'DELETE'
                    }, {
                        onSuccess: page => {
                            this.$refs['deleteModal'].hide()
                        },
                    }
                )
            },
            saveFile() {
                if (this.$refs.fileInput.files && this.$refs.fileInput.files[0]) {
                    this.isFileLoaded = true;
                    var data = new FormData()
                    data.append('file', this.$refs.fileInput.files[0] || '')
                    let uploadUrl = '';
                    // console.log('type', this.$page.folderType)
                    // console.log('fileFolder', this.$page.fileFolder)
                    if (this.$page.fileFolder && this.$page.fileFolder.id) {
                        uploadUrl = this.route(`${this.$page.folderType}.store`, this.$page.fileFolder.id);
                    } else {
                        uploadUrl = this.route(`${this.$page.folderType}.store`);
                    }
                    this.$inertia.post(uploadUrl, data, {
                        preserveScroll: true,
                        preserveState: true,
                        resetOnSuccess: true,
                    }).then(() => {
                        this.isFileLoaded = false;
                        this.openToast('This file has successfully been uploaded.')
                    })
                }
            },
            uploadFile() {
                // console.log('open upload file modal');
                if (this.$refs.fileInput.files && this.$refs.fileInput.files[0]) {
                    this.$nextTick().then(response => {
                        // Open file window
                        this.$refs['uploadFileModal'].show()
                    })
                }
            },
            cloaseUploadFileModal() {
                this.$refs['uploadFileModal'].hide()
            },
            async showAccessFolderModal(folder) {
                if (folder) {
                    this.$set(this, 'selectedFolder', folder)
                } else {
                    this.$set(this, 'selectedFolder', true)
                }
                await this.getInvestorsWithoutAccessToFolder(folder);
                this.$nextTick().then(response => {
                    this.$refs['manageAccessInvestorsToFolderModal'].show()
                })
            },
            async getInvestorsWithoutAccessToFolder(folder) {
                if (this.selectedFolder.id) {
                    let response = await axios.get(this.route(this.$page.folderType + '.folder.investors-access-list', folder));
                    this.investorsWithoutAccessToFolder = response.data.usersWithoutAccess
                    this.investorsWithAccessToFolder = response.data.usersWithAccess
                } else {
                    let response = await axios.get(this.route(this.$page.folderType + '.folder.investors-basefolder-access-list'));
                    this.investorsWithoutAccessToFolder = response.data.usersWithoutBaseFolderAccess
                    this.investorsWithAccessToFolder = response.data.usersWithBaseFolderAccess
                }
            },
            savePermissions() {
                this.$refs['manageAccessInvestorsToFolderModal'].hide()
                // this.$refs.userAccessFolderComponentRef.savePermissions();
            }
        }
    }
</script>
<style>
    #fileNotFound___BV_modal_backdrop_ {
        opacity: 0.5;
    }
</style>
