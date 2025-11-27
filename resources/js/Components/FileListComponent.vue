<style>
    #fileNotFound___BV_modal_backdrop_,
    #manageAccessInvestorsToFolderModal___BV_modal_backdrop_ {
        opacity: 0.5;
    }

    #videoEmbededLink iframe {
        margin: auto;
    }

    #adobe-dc-view {
        height: calc(100vh - 236px);
    }
</style>
<template>
    <div>
        <div class="py-6">
            <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg" :class="files.data.length ? '': 'p-5'">
                    <ul>
                        <li v-for="file in files.data" :key="file.id" class="border-b d-flex align-items-center p-4">
                            <span class="text-xl mr-3">
                                <i :class="getFileClass(file)"></i>
                            </span>
                            <div class="flex-grow" style="line-height:1.2">
                                <div>
                                    {{file.name}}
                                </div>
                                <small v-if="file.last_opened_at" class="text-cool-gray-500">
                                    Opened {{file.last_opened_at}}
                                </small>
                                <small v-if="user_roles.includes('super-admin') || user_roles.includes('admin')">
                                    <template v-if="$page.folderType === 'videos'">
                                        Clicked
                                    </template>
                                    <template v-else>
                                        Viewed
                                    </template>
                                    {{file.views}} times
                                </small>
                            </div>
                            <div>
                                <js-button v-if="$page.folderType === 'videos'" @click.native="viewFile(file)">
                                    View
                                </js-button>
                                <js-button v-if="file.extension === 'pdf'" @click.native="openPdfFile(file)">
                                    View
                                </js-button>
                                <js-secondary-button v-if="$page.folderType !== 'videos' && file.extension !== 'pdf'"
                                    @click.native="downloadFile(file)">
                                    Download
                                    <div v-if="file.isDownloaded" class="loading"></div>
                                </js-secondary-button>
                                <js-danger-button
                                    v-if="user_roles.includes('super-admin') || user_roles.includes('admin')"
                                    @click.native="showDeleteModal(file)">
                                    Delete
                                </js-danger-button>
                            </div>
                        </li>
                    </ul>
                    <pagination-links
                        :withLinks="true"
                        :paginationItems="files"
                     >
                    </pagination-links>
                    <div v-if="!files.data.length">
                        No files have been uploaded yet
                    </div>
                </div>
            </div>
        </div>

        <b-modal ref="viewFileModal" v-if="selectedFile" :title="`View ${selectedFile.name}`" @show="showPopupEvent()"
            size="xl">
            <div v-if="folderType === 'videos' && selectedFile.video && selectedFile.video.embeded"
                id="videoEmbededLink" v-html="selectedFile.video.embeded">
            </div>
            <object v-if="folderType !== 'videos' && selectedFile.extension !== 'pdf'">
                <embed :src="`${selectedFile.url}#toolbar=0`" width="100%" height="500">
            </object>
            <div v-if="folderType !== 'videos' && selectedFile.extension === 'pdf'" ref="adobeDocument"
                id="adobe-dc-view"></div>
        </b-modal>

        <b-modal ref="deleteModal" v-if="fileForDelete" :title="`Delete file ${fileForDelete.name}`" size="xl"
            hide-footer centered>
            <div class="d-block text-center">
                <h3>Are you sure you want to delete this file?</h3>
            </div>
            <b-button class="mt-3" variant="outline-danger" block @click="deleteFile();closeDeleteModal()">Yes
            </b-button>
            <b-button class="mt-2" variant="outline-success" block @click="closeDeleteModal()">No</b-button>
        </b-modal>

        <b-modal id="fileNotFound" content-class="your-class" ref="fileIsNotFound" hide-footer centered>
            <div class="d-block text-center">
                <h3>File is not found</h3>
            </div>
        </b-modal>

    </div>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout'
    import JsButton from '../Jetstream/Button'
    import JsSecondaryButton from '../Jetstream/SecondaryButton'
    import JsDangerButton from '../Jetstream/DangerButton'
    import JetInputError from '@/Jetstream/InputError'
    import DialogModal from '../Jetstream/DialogModal'
    import PaginationLinks from './PaginationLinks'
    import {
        InertiaProgress
    } from '@inertiajs/progress'
    import JetNavLink from '@/Jetstream/NavLink'

    export default {
        components: {
            AppLayout,
            JsButton,
            JsSecondaryButton,
            JsDangerButton,
            DialogModal,
            JetInputError,
            PaginationLinks,
            InertiaProgress,
            JetNavLink
        },
        props: ['files', 'user_roles', 'folderType', 'paginatedLinks', 'fileFolder'],
        mounted() {
            let recaptchaScript = document.createElement('script')
            recaptchaScript.setAttribute('src', 'https://documentcloud.adobe.com/view-sdk/main.js')
            document.head.appendChild(recaptchaScript)
        },
        data() {
            return {
                selectedFile: null,
                selectedFolder: null,
                fileForDelete: null,
                uploadedFile: null,
                title: null,
                investorsWithoutAccessToFolder: []
            }
        },
        methods: {
            openPdf(file) {
                var adobeDCView = new AdobeDC.View({
                    clientId: this.$page.app.adobeReaderKey,
                    divId: "adobe-dc-view"
                });
                console.log('file.urlfile.url', file.url)
                console.log('file.urlfile.name', file.name)
                let previewFilePromise = adobeDCView.previewFile({
                    content: {
                        location: {
                            url: file.url
                        }
                    },
                    metaData: {
                        fileName: file.name
                    }
                }, {
                    showAnnotationTools: false,
                    showLeftHandPanel: false,
                    showDownloadPDF: false,
                    showPrintPDF: false

                });

                previewFilePromise.then(adobeViewer => {
                    adobeViewer.getAPIs().then(apis => {
                        apis.enableTextSelection(true)
                    });
                });
            },
            showPopupEvent() {
                if (this.selectedFile.extension === 'pdf' && this.$page.app.adobeReaderKey) {
                    this.$nextTick().then(response => {
                        this.openPdf(this.selectedFile)
                    })
                }
            },
            saveFile() {
                if (this.$refs.fileInput.files && this.$refs.fileInput.files[0]) {
                    var data = new FormData()
                    data.append('file', this.$refs.fileInput.files[0] || '')
                    InertiaProgress.init()
                    let uploadUrl = '';
                    if (this.fileFolder && this.fileFolder.id) {
                        uploadUrl = this.route(`${this.folderType}.store`, this.fileFolder.id);
                    } else {
                        uploadUrl = this.route(`${this.folderType}.store`);
                    }
                    this.$inertia.post(uploadUrl, data, {
                        preserveScroll: true,
                        preserveState: true,
                        resetOnSuccess: true,
                    })
                }
            },
            uploadFile() {
                if (this.$refs.fileInput.files && this.$refs.fileInput.files[0]) {
                    this.$nextTick().then(response => {
                        // Open file window
                        self.$refs['uploadFileModal'].show()
                    })
                }
            },
            cloaseUploadFileModal() {
                this.$refs['uploadFileModal'].hide()
            },
            readFile(file) {
                return axios.get(`/files/view/${file.id}`)
            },
            openPdfFile(file) {
                let self = this
                this.$set(this, 'selectedFile', file)
                this.$set(this.selectedFile, 'url', `/files/view/${file.id}`)
                this.$nextTick().then(response => {
                    // Open file window
                    self.$refs['viewFileModal'].show()
                })
            },
            viewFile(file) {
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
                    .catch((error) => {
                        this.$refs['fileIsNotFound'].show()
                    })
            },
            downloadFile(file) {
                this.$set(file, 'isDownloaded', true)
                axios
                    .get(`/files/download/${file.id}`, {
                        responseType: 'arraybuffer'
                    })
                    .then(response => {
                        //   console.log('response.headers[content-type]', response.headers['content-type']);
                        let blob = new Blob([response.data], {
                            type: response.headers['content-type']
                        })
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
            showDeleteModal(file) {
                this.$set(this, 'fileForDelete', file)
                this.$nextTick().then(response => {
                    this.$refs['deleteModal'].show()
                })
            },
            closeDeleteModal() {
                this.$refs['deleteModal'].hide()
            },
            deleteFile() {
                this.$inertia.post(
                    this.route(`${this.folderType}.destroy`, this.fileForDelete.id), {
                        _method: 'DELETE'
                    }, {
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
            getInvestorsWithoutAccessToFolder(folder) {
                this.isLoading = true
                axios.get(
                    this.route(this.folderType + '.folder.investors-without-access', folder)
                ).then(response => {
                    // console.log('resposne', response.data);
                    this.investorsWithoutAccessToFolder = response.data.data
                });
            },
            getDownloadFileUrl(file) {
                return `/${this.folderType}/download/${file.id}`;
            },
            fileNotFound() {
                if (this.$refs['viewFileModal']) {
                    this.$refs['viewFileModal'].hide()
                }
                this.$refs['fileIsNotFound'].show()
            },
            getFileClass(file) {
                if (this.folderType === 'videos') {
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
            }
        }
    }
</script>
