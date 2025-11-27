<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Access Report
            </h2>
        </template>

        <div class="pt-6 max-w-6xl mx-auto sm:px-6 lg:px-8 d-flex justify-between align-middle">
            <a class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent
                        rounded-md font-semibold text-xs text-white hover:text-white uppercase tracking-widest
                        hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900
                        focus:shadow-outline-gray transition ease-in-out duration-150 card-link"
                    :href="route('access-report.export')"
            >
                Export Reports
            </a>
        </div>

        <div class="py-12">
            <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg" :class="$page.folders.length ? '': 'p-5'">
                    <ul>
                        <li v-for="folder in $page.folders" class="border-b p-4 col-12">
                            <div class="row" style="font-weight: 900;font-size: 20px;">
                                <div class="col-9">
                                    {{ getFolderLabelName(folder.name) }}
                                </div>
                                <div class="col-3 text-right">
                                    <js-button
                                        @click.native="showAccessFolderModal(folder, 'folder')"
                                        style="margin-left: 10px;"
                                    >
                                        Manage Access
                                    </js-button>
                                </div>
                            </div>
                            <ul>
                                <li v-for="investor in folder.investors" class="border-b p-4 row">
                                    <div class="col-9">
                                        {{ investor.name }}
                                    </div>
                                    <div class="col-3 text-right">
                                        <js-secondary-button
                                            @click.native="removeInvestor(investor, folder, 'folder')"
                                            style="margin-left: 10px;"
                                        >
                                            Remove Access
                                        </js-secondary-button>
                                    </div>
                                </li>
                            </ul>
                            <ul>
                                <li v-for="subfolder in folder.subfolders" class="border-b p-4 col-12">
                                    <div class="row" style="font-weight: 900;font-size: 16px;">
                                        <div class="col-9">
                                            {{ subfolder.name }}
                                        </div>
                                        <div class="col-3 text-right">
                                            <js-button
                                                @click.native="showAccessFolderModal(subfolder, 'subfolder')"
                                                style="margin-left: 10px;"
                                            >
                                                Manage Access
                                            </js-button>
                                        </div>
                                    </div>
                                    <ul>
                                        <li v-for="investor in subfolder.users" class="border-b p-4 row">
                                            <div class="col-9">
                                                {{ investor.name }}
                                            </div>
                                            <div class="col-3 text-right">
                                                <js-secondary-button
                                                    @click.native="removeInvestor(investor, subfolder, 'subfolder')"
                                                    style="margin-left: 10px;"
                                                >
                                                    Remove Access
                                                </js-secondary-button>
                                            </div>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                    </ul>
                    <div v-if="!$page.folders.length">
                        No folders yet
                    </div>
                </div>
            </div>
        </div>

        <b-modal v-if="selectedFolder"
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
                :folderType="selectedBaseFolder"
                :folder="selectedFolder"
                :saveApi="selectedFolerType === 'folder' ? route(selectedBaseFolder + '.save_permission_to_user') : null"
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

    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout'
    import JsButton from '../Jetstream/Button'
    import JsSecondaryButton from '../Jetstream/SecondaryButton'
    import AddSubfolderPermissionToUser from '../Components/AddSubfolderPermissionToUser'
    export default {
        components: {
            AppLayout,
            JsButton,
            JsSecondaryButton,
            AddSubfolderPermissionToUser
        },
        data() {
            return {
                investorsWithoutAccessToFolder: [],
                investorsWithAccessToFolder: [],
                selectedFolder: null,
                selectedFolerType: '',
                selectedBaseFolder: ''
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
            async showAccessFolderModal(folder, type = 'folder') {
                this.setSelectedInfoByFolder(folder, type);
                await this.getInvestorsWithoutAccessToFolder(folder);
                this.$nextTick().then(response => {
                    this.$refs['manageAccessInvestorsToFolderModal'].show()
                })
            },
            setSelectedInfoByFolder(folder, type = 'folder') {
                this.$set(this, 'selectedFolerType', type)
                this.$set(this, 'selectedFolder', folder)
                if (type === 'subfolder') {
                    if (folder.type === 'other') {
                        this.$set(this, 'selectedBaseFolder', 'liwa-fund-reports')
                    } else {
                        this.$set(this, 'selectedBaseFolder', folder.type)
                    }

                } else if (type === 'folder') {
                    this.$set(this, 'selectedBaseFolder', folder.name)
                }
            },
            async getInvestorsWithoutAccessToFolder(folder) {
                if (this.selectedFolerType === 'subfolder') {
                    let response = await axios.get(this.route(this.selectedBaseFolder + '.folder.investors-access-list', folder));
                    this.investorsWithoutAccessToFolder = response.data.usersWithoutAccess
                    this.investorsWithAccessToFolder = response.data.usersWithAccess
                } else {
                    let response = await axios.get(this.route(folder.name + '.folder.investors-basefolder-access-list'));
                    console.log('folderrr access list', response);
                    this.investorsWithoutAccessToFolder = response.data.usersWithoutBaseFolderAccess
                    this.investorsWithAccessToFolder = response.data.usersWithBaseFolderAccess
                }
            },
            updateInvestorsList(investors) {
                for (let i = 0; i < this.$page.folders.length; i++) {
                    if (this.$page.folders[i].name !== this.selectedBaseFolder) {
                        continue;
                    }
                    if (this.selectedFolerType === 'subfolder') {
                        for (let j = 0; j < this.$page.folders[i].subfolders.length; j++) {
                            if (this.$page.folders[i].subfolders[j].id !== this.selectedFolder.id) {
                                continue;
                            }
                            this.$page.folders[i].subfolders[j].users = investors;
                        }
                    } else if (this.selectedFolerType === 'folder') {
                        this.$page.folders[i].investors = investors;
                    }
                }
            },
            savePermissions() {
                this.$refs['manageAccessInvestorsToFolderModal'].hide()
                this.$refs.userAccessFolderComponentRef.savePermissions()
                    .then(investors => {
                        this.updateInvestorsList(investors)
                    });
            },
            removeInvestor(removedInvestor, folder, folderType = 'folder') {
                this.setSelectedInfoByFolder(folder, folderType);
                let investors = [];
                if (folderType === 'subfolder') {
                    investors = folder.users;
                } else if (folderType === 'folder') {
                    investors = folder.investors;
                }
                const investorsForSaving = investors.filter(investor => {
                  return (investor.id !== removedInvestor.id);
                });
                const investorsIdsForSaving = investorsForSaving.map(investor => {
                    return investor.id;
                })

                const apiForSave = (folderType === 'subfolder') ?
                    this.route(this.selectedBaseFolder + '.save_subfolder_permission_to_user', folder) :
                    this.route(this.selectedBaseFolder + '.save_permission_to_user');

                axios.post(apiForSave, {investors: investorsIdsForSaving})
                    .then(response => {
                        this.openToast('Permission was removed from user')
                        this.updateInvestorsList(investorsForSaving);
                    });
            },
        }
    }
</script>
