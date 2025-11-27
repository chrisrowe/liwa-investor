<style>
    .modal-backdrop {
        background-color: rgba(123, 123, 123, 0.7);
    }
</style>
<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Users
            </h2>
        </template>

        <div class="d-flex justify-content-between pt-12 max-w-6xl mx-auto sm:px-6 lg:px-8 row">
            <div class="col-lg-2 p-0">
                <jet-nav-link :href="route('user.create')">
                    <js-button>
                        Add +
                    </js-button>
                </jet-nav-link>
            </div>
            <div class="col-lg-3 pl-0 pr-0">

                    <b-form-input id="search-input"
                                  @input="getUsers"
                                  style="width: 100%;padding: 10px;border: 1px solid black;"
                                  v-model="search"
                                  placeholder="Search"
                                  trim
                    >
                    </b-form-input>
            </div>
        </div>

        <div class="py-12">
            <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white shadow-xl sm:rounded-lg" :class="users.data.length ? '': 'p-5'">
                    <ul class="overflow-hidden">
                        <li v-for="user in users.data" :key="user.id" class="border-b p-4 row align-items-center">
                            <div class="col" style="line-height:1.2">
                                <div>
                                    {{user.name}}
                                </div>
                            </div>
                            <div class="col" style="line-height:1.2">
                                <div>
                                    {{user.email}}
                                </div>
                            </div>
                            <div class="col-auto" style="line-height:1.2">
                                <div style="text-transform: capitalize;">
                                    {{ getUserRoleName(user) }}
                                </div>
                            </div>
                            <div class="col-auto d-inline-block">
                                <jet-nav-link :href="route('user.edit', user)">
                                    <js-button>
                                        Edit
                                    </js-button>
                                </jet-nav-link>
                                <js-secondary-button @click.native="showDeleteModal(user)">
                                    Delete
                                </js-secondary-button>
                            </div>
                        </li>
                    </ul>

                    <div class="pt-6 pb-6 d-flex justify-content-between sm:px-6 lg:px-8 row">
                        <div class="col-lg-9 col-md-9 col-sm-9">
                            <pagination-links
                                class="lg:float-left md:float-left sm:float-left pb-6 lg:pb-0 md:pb-0 sm:pb-0"
                                :paginationItems="users"
                                :changePaginationPage="changePaginationPage"
                                :showPaginationAllways="true"
                            >
                            </pagination-links>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-3 lg:pl-0 lg:pr-0 md:pl-0 md:pr-0 sm:pl-0 sm:pr-0">
                            <b-form-select 
                                class="col-12 form-control"
                                v-model="perPage"
                                :options="perPageOptions"
                                style="position: absolute;top: 50%;transform: translateY(-50%);"
                                @change="changePrePageParam"
                            >
                            </b-form-select>
                        </div>
                    </div>
                    
                    
                    <div v-if="!users.data.length">
                        Users not created yet
                    </div>
                </div>
            </div>
        </div>

        <b-modal v-if="selectedUser" ref="deleteModal" hide-footer centered :title="`Delete ${selectedUser.name}`">
            <div class="d-block text-center">
                <h3>Are you sure to remove this user?</h3>
            </div>
            <b-button class="mt-3" variant="outline-danger" block @click="deleteUser">Yes</b-button>
            <b-button class="mt-2" variant="outline-success" block @click="closeDeleteModal">No</b-button>
        </b-modal>

    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout'
    import JsButton from '../../Jetstream/Button'
    import JsSecondaryButton from '../../Jetstream/SecondaryButton'
    import DialogModal from '../../Jetstream/DialogModal'
    import JetNavLink from '@/Jetstream/NavLink'
    import PaginationLinks from '../../Components/PaginationLinks'
    export default {
        components: {
            AppLayout,
            JsButton,
            JsSecondaryButton,
            DialogModal,
            JetNavLink,
            PaginationLinks
        },
        data() {
            return {
                perPageOptions: [
                    { value: 10, text: '10' },
                    { value: 25, text: '25' },
                    { value: 50, text: '50' },
                ],
                selectedUser: null,
                users: this.$page.users,
                search: new URL(location.href).searchParams.get('search'),
                pagintatedUsersLink: this.$page.pagintatedUsersLink ? this.$page.pagintatedUsersLink : [],
                perPage: new URL(location.href).searchParams.get('perPage') ? new URL(location.href).searchParams.get('perPage') : 50,
                currentPage: this.$page.users.current_page,
                rows: 1,
            }
        },
        methods: {
            showDeleteModal(user) {
                this.$set(this, 'selectedUser', user)
                this.$nextTick().then(response => {
                    this.$refs['deleteModal'].show()
                })
            },
            closeDeleteModal() {
                this.$refs['deleteModal'].hide()
            },
            deleteUser() {
                axios.post(`/admin/users/destroy/${this.selectedUser.id}`, {'_method': 'DELETE'})
                    .then(response => {
                        for (var i = 0; i < this.$page.users.length; i++) {
                            var obj = this.$page.users[i];

                            if (this.selectedUser.id === obj.id) {
                                this.$page.users.splice(i, 1);
                                break;
                            }
                        }
                        this.$refs['deleteModal'].hide()

                    })
                    .catch(error => {
                        this.$refs['deleteModal'].hide()
                    })
            },
            changePrePageParam() {
                this.currentPage = 1;
                this.getUsers()
            },
            getUsers() {
                setTimeout(() => {
                    axios.get(this.route('user.list', {search: this.search, perPage: this.perPage, page: this.currentPage}))
                    .then(response => {
                        this.users = response.data.users;
                        this.currentPage = response.data.users.current_page
                        this.rows = response.data.users.total
                        this.perPage = Number(response.data.users.per_page)
                        console.log("this.perPage", this.perPage);
                        this.pagintatedUsersLink = response.data.pagintatedUsersLink;
                    })
                    .catch(error => {
                        //
                    })
                }, 500)
            },
            changePaginationPage(number) {
                this.currentPage = number;
                this.getUsers();
            },
            getUserRoleName(user) {
                if (!user.roles.length) {
                    return '';
                }
                let roleName = user.roles[0].name;
                roleName = roleName.replace(/-/g, ' ');

                return roleName
                    .toLowerCase()
                    .split(' ')
                    .map(function(word) {
                        return word[0].toUpperCase() + word.substr(1);
                    })
                    .join(' ');
            }
        }
    }
</script>
