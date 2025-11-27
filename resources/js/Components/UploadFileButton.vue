<template>
    <div>
        <div v-if="isFileLoaded" class="loading file-loading"></div>
        <div v-if="folderType !== 'videos'"
             class="inline-flex"
        >
            <label
                for="fileInput"
                class="inline-flex items-center px-4 mb-0 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900  focus:shadow-outline-gray transition ease-in-out duration-150"
                style="cursor: pointer;"
            >
                Upload
            </label>
            <input type="file" id="fileInput" ref="fileInput" v-on:change="saveFile()"
                style="display:none;" />
            <jet-input-error v-if="$page.errors.file" :message="$page.errors.file[0]" class="mt-2" />
        </div>
        <div v-if="folderType === 'videos'"
             class="inline-flex"
        >
            <label
                class="inline-flex items-center px-4 mb-0 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900  focus:shadow-outline-gray transition ease-in-out duration-150"
                style="cursor: pointer;"
                @click="openVideoEmbedModal"
            >
                Embed
            </label>

            <jet-input-error v-if="$page.errors.file" :message="$page.errors.file[0]" class="mt-2" />
        </div>

        <b-modal v-if="folderType === 'videos'"
                 ref="manageVideoEmbeded"
                 id="manageVideoEmbeded"
                 centered
                 :title="`Manage video`"
        >
                <jet-label for="fileVideoEmbedLinkName" value="Video Link Title" />
                <jet-input id="fileVideoEmbedLinkName" type="text" class="mt-1 mb-4 block w-full" v-model="fileVideoEmbedLinkName" autocomplete="fileVideoEmbedLinkName" />
                <jet-input-error v-if="$page.errors.linkName" :message="$page.errors.linkName[0]" class="mt-2" />
                <jet-label for="fileVideoEmbedLink" value="Video Embed Link" />
                <jet-input id="fileVideoEmbedLink" type="text" class="mt-1 block w-full" v-model="fileVideoEmbedLink" autocomplete="fileVideoEmbedLink" />
                <jet-input-error v-if="$page.errors.embeded" :message="$page.errors.embeded[0]" class="mt-2" />


                <template #modal-footer="{ ok, cancel, hide }">
                    <jet-button :class="{ 'opacity-25': (!fileVideoEmbedLink || isSaveProcessVideoEmbedLink) }"
                                :disabled="!fileVideoEmbedLink || isSaveProcessVideoEmbedLink"
                                @click.native="saveFileVideoEmbedLink"
                    >
                        Save
                    </jet-button>
                </template>

        </b-modal>
    </div>
</template>

<script>
    import JetInputError from '@/Jetstream/InputError'
     import {
        InertiaProgress
    } from '@inertiajs/progress'
    import JetLabel from '@/Jetstream/Label'
    import JetButton from '@/Jetstream/Button'
    import JetInput from '@/Jetstream/Input'

    export default {
        components: {
            JetInputError,
            InertiaProgress,
            JetLabel,
            JetButton,
            JetInput
        },
        props: ['folderType', 'fileFolder'],
        data() {
            return {
                isFileLoaded: false,
                selectedFile: null,
                uploadedFile: null,
                fileVideoEmbedLink: null,
                isSaveProcessVideoEmbedLink: false,
                fileVideoEmbedLinkName: ''
            }
        },
        methods: {
            openToast(title) {
                this.$bvToast.toast(title, {
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
                    }).then(() => {
                        this.isFileLoaded = false;
                        this.openToast('File was uploaded')
                    })
                }
            },
            openVideoEmbedModal() {
                this.fileVideoEmbedLink = '';
                this.fileVideoEmbedLinkName = '';
                this.$nextTick().then(response => {
                    this.$refs['manageVideoEmbeded'].show()
                })
            },
            closeVideoEmbedModal() {
                this.$nextTick().then(response => {
                    this.$refs['manageVideoEmbeded'].hide()
                })
            },
            saveFileVideoEmbedLink() {
                console.log('saveFileVideoEmbededLink', this.route('videos.embed'));
                if (this.fileVideoEmbedLink) {
                    this.isFileLoaded = true;
                    var data = new FormData()
                    data.append('embeded', this.fileVideoEmbedLink)
                    data.append('linkName', this.fileVideoEmbedLinkName)
                    let uploadUrl = '';
                    if (this.fileFolder && this.fileFolder.id) {
                        uploadUrl = this.route(`videos.embed`, this.fileFolder.id);
                    } else {
                        uploadUrl = this.route(`videos.embed`);
                    }
                    this.$inertia.post(uploadUrl, data, {
                        preserveScroll: true,
                        preserveState: true,
                        resetOnSuccess: true,
                    }).then(() => {
                        if (!this.$page.errors.hasOwnProperty('linkName') && !this.$page.errors.hasOwnProperty('embeded')) {
                            this.openToast('File successfully embeded')
                            this.closeVideoEmbedModal();
                        }
                        this.isFileLoaded = false;
                    })
                }
            }
        }
    }
</script>
<style>
    #fileNotFound___BV_modal_backdrop_ {
        opacity: 0.5;
    }
</style>
