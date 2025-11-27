<style>
    #fileNotFound___BV_modal_backdrop_, #manageAccessInvestorsToFolderModal___BV_modal_backdrop_, #uploadFileModal___BV_modal_backdrop_ {
        opacity: 0.5;
    }

    #folderContent .loading.file-loading:after {
        top: 0px;
    }
</style>
<template>
    <app-layout id="folderContent">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight pb-0">
                {{ getPageTitle() }}
            </h2>

            <jet-nav-link
                :href="!$page.fileFolder.parent_folder ?
                        route($page.folderType) :
                        route($page.folderType + '.folder.index') + '/' + $page.fileFolder.parent_folder.id"
            >
                <js-button>
                    Back
                </js-button>
            </jet-nav-link>
        </template>

        <div class="pt-6 max-w-6xl mx-auto sm:px-6 lg:px-8 d-flex justify-content-end align-items-center">
            <upload-file-button
                v-if="$page.user_roles.includes('super-admin') || $page.user_roles.includes('admin')"
                :folderType="$page.folderType"
                :fileFolder="$page.fileFolder"
                style="display: contents;"
            >
            </upload-file-button>

            <jet-nav-link v-if="($page.user_roles.includes('super-admin') || $page.user_roles.includes('admin')) && !$page.fileFolder.parent_folder"
                          :href="route($page.folderType + '.folder.create') + '/' + $page.fileFolder.id">
                <js-secondary-button>
                    Add folder
                </js-secondary-button>
            </jet-nav-link>

            <js-button
                v-if="$page.fileFolder.id && ($page.user_roles.includes('super-admin') || $page.user_roles.includes('admin')) && !$page.fileFolder.parent_folder"
                @click.native="showAccessFolderModal($page.fileFolder)"
                style="margin-left: 10px;"
            >
                Manage Access
            </js-button>
        </div>

        <div class="py-6" v-if="$page.folders.data.length">
            <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg"
                    :class="$page.folders.data.length ? '': 'p-5'">
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
                                <js-danger-button
                                    v-if="folder.id && ($page.user_roles.includes('super-admin') || $page.user_roles.includes('admin'))"
                                    @click.native="showDeleteModal(folder)">
                                    Delete
                                </js-danger-button>
                            </div>
                        </li>
                    </ul>
                    <pagination-links v-if="$page.folders.data.length" :urlsArray="$page.paginatedFolderLinks"
                        :previousPageUrl="$page.folders.prev_page_url" :nextPageUrl="$page.folders.next_page_url">
                    </pagination-links>
                </div>
            </div>
        </div>

        <file-list-component
            :files="$page.files"
            :user_roles="$page.user_roles"
            :folderType="$page.folderType"
            :paginatedLinks="$page.paginatedLinks"
            :fileFolder="$page.fileFolder"
        >
        </file-list-component>

        <b-modal v-if="($page.user_roles.includes('super-admin') || $page.user_roles.includes('admin')) && selectedFolder"
                 ref="manageAccessInvestorsToFolderModal"
                 id="manageAccessInvestorsToFolderModal"
                 hide-footer
                 centered
                 :title="`Share with investors`"
        >
            <add-subfolder-permission-to-user
                ref="userAccessFolderComponentRef"
                :folderInvestors="selectedFolder.users"
                :allInvestors="investorsWithoutAccessToFolder"
                :folderType="$page.folderType"
                :folder="selectedFolder"
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

        <b-modal
            v-if="($page.user_roles.includes('super-admin') || $page.user_roles.includes('admin')) && folderForDelete"
            ref="deleteModal" hide-footer centered :title="`Delete ${folderForDelete.name}`">
            <div class="d-block text-center">
                <h3>
                    Are you sure you want to delete "{{ folderForDelete.name }}" and all of its contents?
                </h3>
            </div>
            <b-button class="mt-3" variant="outline-danger" block @click="deleteFolder">Yes</b-button>
            <b-button class="mt-2" variant="outline-success" block @click="closeDeleteModal">No</b-button>
        </b-modal>

    </app-layout>
</template>

<script>
    const url = 'https://liwa-capital.s3.amazonaws.com/Investor+Deal+Room.mp4';
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
                fileForDelete: null,
                folderForDelete: null,
                files: this.$page.files,
                folderType: null,
                uploadedFile: null,
                paginatedLinks: [],
                title: null,
                investorsWithoutAccessToFolder: []
            }
        },
        methods: {
            getPageTitle() {
                let pageTitle = this.$page.title;
                if (this.$page.fileFolder.parent_folder) {
                    pageTitle += ` / ${this.$page.fileFolder.parent_folder.name}`
                }
                pageTitle += ` / ${this.$page.fileFolder.name}`

                return pageTitle;
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
            saveFile() {
                if (this.$refs.fileInput.files && this.$refs.fileInput.files[0]) {
                    this.isFileLoaded = true;
                    var data = new FormData()
                    data.append('file', this.$refs.fileInput.files[0] || '')
                    let uploadUrl = '';
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
            readFile(file) {
                return Promise.resolve().then(() => `/files/view/${file.id}`);
            },
            viewFile(file) {
                // Get url and mark file as viewed
                this.readFile(file)
                    .then(response => {
                        let self = this
                        this.$set(this, 'selectedFile', file)
                        this.$set(this.selectedFile, 'url', response)
                        this.$nextTick().then(response => {
                            // Open file window
                            self.$refs['viewFileModal'].show()
                        })
                    })
            },
            downloadFile(file) {
                this.$set(file, 'isDownloaded', true)
                axios
                    .get(`/files/download/${file.id}`, {responseType: 'arraybuffer'})
                    .then(response => {
                    //   console.log('response.headers[content-type]', response.headers['content-type']);
                      let blob = new Blob([response.data], {type: response.headers['content-type']})
                      let link = document.createElement('a')
                      link.href = window.URL.createObjectURL(blob)
                      link.download = file.name
                      link.click()
                      this.$set(file, 'isDownloaded', false)
                    }, error => {
                        this.$set(file, 'isDownloaded', false)
                        this.$refs['fileIsNotFound'].show()
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
            deleteFile() {
                this.$inertia.post(
                    this.route(`${this.$page.folderType}.destroy`, this.fileForDelete.id),
                    {_method: 'DELETE'},
                    {
                        onSuccess: page => {
                            this.$refs['deleteModal'].hide()
                        },
                    }
                )
            },
            async showAccessFolderModal(folder) {
                this.$set(this, 'selectedFolder', folder)
                await this.getInvestorsWithoutAccessToFolder(folder);
                this.$nextTick().then(response => {
                    this.$refs['manageAccessInvestorsToFolderModal'].show()
                })
            },
            async getInvestorsWithoutAccessToFolder(folder) {
                this.isLoading = true
                let response = await axios.get(this.route(this.$page.folderType + '.folder.investors-access-list', folder));
                this.investorsWithoutAccessToFolder = response.data.usersWithoutAccess
                this.$page.fileFolder.users = response.data.usersWithAccess
            },
            getDownloadFileUrl(file) {
                return `/${this.$page.folderType}/download/${file.id}`;
            },
            fileNotFound() {
                if (this.$refs['viewFileModal']) {
                    this.$refs['viewFileModal'].hide()
                }
                this.$refs['fileIsNotFound'].show()
            },
            getFileClass(file) {
                if (this.$page.folderType === 'videos') {
                    return 'fa fa-play-circle text-red-500';
                }
                switch (file.extension) {
                    case 'pdf':
                        return 'fa fa-file-pdf-o text-red-700'
                        break;
                    case 'xlsx':
                        return 'fa fa-file-excel-o text-green-500'
                        break;
                    case 'ppt':
                        return 'fa fa-file-powerpoint-o text-secondary'
                        break;
                    case 'doc':
                        return 'fa fa-file-word-o text-blue-500'
                        break;
                    case 'png':
                    case 'jpg':
                    case 'jpeg':
                    case 'gif':
                        return 'fa fa-file-photo-o text-blue-500'
                        break;
                    default:
                        return 'fa fa fa-file-o'
                }
            },
            savePermissions() {
                this.$refs['manageAccessInvestorsToFolderModal'].hide()
                // this.$refs.userAccessFolderComponentRef.savePermissions();
            }
        }
    }
</script>
