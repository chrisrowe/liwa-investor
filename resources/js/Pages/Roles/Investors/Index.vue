<style>
    .modal-backdrop {
        background-color: rgba(123, 123, 123, 0.7);
    }
</style>
<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Investors
            </h2>
        </template>

        <div class="pt-12 max-w-6xl mx-auto sm:px-6 lg:px-8">
            <jet-nav-link :href="route('investors.create')">
                <js-button>
                    Add +
                </js-button>
            </jet-nav-link>
        </div>

        <div class="py-12">
            <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg" :class="investors.length ? '': 'p-5'">
                    <ul>
                        <li v-for="investor in investors" :key="investor.id" class="border-b p-4 col-12">
                            <div class="col-lg-3 col-xs-12 d-inline-block" style="line-height:1.2">
                                <div>
                                    {{investor.name}}
                                </div>
                            </div>
                            <div class="col-lg-5 col-xs-12 d-inline-block" style="line-height:1.2">
                                <div>
                                    {{investor.email}}
                                </div>
                            </div>
                            <div class="col-lg-3 col-xs-12 d-inline-block">
                                <jet-nav-link :href="route('investors.edit', investor)">
                                    <js-button>
                                        Edit
                                    </js-button>
                                </jet-nav-link>
                                <js-secondary-button @click.native="showDeleteModal(investor)">
                                    Delete
                                </js-secondary-button>
                            </div>
                        </li>
                    </ul>
                    <div v-if="!investors.length">
                        Investors not created yet
                    </div>
                </div>
            </div>
        </div>

        <b-modal v-if="selectedInvestor" ref="deleteModal" hide-footer centered :title="`Delete ${selectedInvestor.name}`">
            <div class="d-block text-center">
                <h3>Are you sure you want to remove this investor?</h3>
            </div>
            <b-button class="mt-3" variant="outline-danger" block @click="deleteInvestor">Yes</b-button>
            <b-button class="mt-2" variant="outline-success" block @click="closeDeleteModal">No</b-button>
        </b-modal>

    </app-layout>
</template>

<script>
    const url = 'https://liwa-capital.s3.amazonaws.com/Investor+Deal+Room.mp4';
    import AppLayout from '@/Layouts/AppLayout'
    import JsButton from '../../../Jetstream/Button'
    import JsSecondaryButton from '../../../Jetstream/SecondaryButton'
    import DialogModal from '../../../Jetstream/DialogModal'
    import JetNavLink from '@/Jetstream/NavLink'
    export default {
        components: {
            AppLayout,
            JsButton,
            JsSecondaryButton,
            DialogModal,
            JetNavLink
        },
        data() {
            return {
                selectedInvestor: null,
                investors: this.$page.investors
            }
        },
        methods: {
            getAdmins() {

            },
            showDeleteModal(investor) {
                this.$set(this, 'selectedInvestor', investor)
                this.$nextTick().then(response => {
                    this.$refs['deleteModal'].show()
                })
            },
            closeDeleteModal() {
                this.$refs['deleteModal'].hide()
            },
            deleteInvestor() {
                // console.log('investor', this.selectedInvestor);
                axios.delete(`/admin/investors/destroy/${this.selectedInvestor.id}`)
                    .then(response => {
                        for (var i = 0; i < this.$page.investors.length; i++) {
                            var obj = this.$page.investors[i];

                            if (this.selectedInvestor.id === obj.id) {
                                this.$page.investors.splice(i, 1);
                                break;
                            }
                        }
                        this.$refs['deleteModal'].hide()

                    })
                    .catch(error => {
                        this.$refs['deleteModal'].hide()
                    })

            }
        }
    }
</script>
