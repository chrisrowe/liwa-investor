<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ !$page.investor ? 'Create Investor' : 'Update Investor' }}
            </h2>
        </template>
        <div>
            <div class="max-w-6xl mx-auto py-10 sm:px-6 lg:px-8">
                <jet-form-section @submitted="storeAdmin">
                    <template #form>
                        <!-- Profile Photo -->
                        <div class="col-span-6 sm:col-span-4" v-if="$page.jetstream.managesProfilePhotos">
                            <!-- Profile Photo File Input -->
                            <input type="file" class="hidden"
                                        ref="photo"
                                        @change="updatePhotoPreview">

                            <jet-label for="photo" value="Photo" />

                            <!-- Current Profile Photo -->
                            <div class="mt-2" v-show="! photoPreview">
                                <img :src="user.profile_photo_url" alt="Current Profile Photo" class="rounded-full h-20 w-20 object-cover">
                            </div>

                            <!-- New Profile Photo Preview -->
                            <div class="mt-2" v-show="photoPreview">
                                <span class="block rounded-full w-20 h-20"
                                      :style="'background-size: cover; background-repeat: no-repeat; background-position: center center; background-image: url(\'' + photoPreview + '\');'">
                                </span>
                            </div>

                            <jet-secondary-button class="mt-2 mr-2" type="button" @click.native.prevent="selectNewPhoto">
                                Select A New Photo
                            </jet-secondary-button>

                            <jet-secondary-button type="button" class="mt-2" @click.native.prevent="deletePhoto" v-if="user.profile_photo_path">
                                Remove Photo
                            </jet-secondary-button>

                            <jet-input-error v-if="$page.errors.photo" :message="$page.errors.photo[0]" class="mt-2" />
                        </div>

                        <!-- Name -->
                        <div class="col-span-6 sm:col-span-4">
                            <jet-label for="name" value="Name" />
                            <jet-input id="name" type="text" class="mt-1 block w-full" v-model="form.name" autocomplete="name" />
                            <jet-input-error v-if="$page.errors.name" :message="$page.errors.name[0]" class="mt-2" />
                        </div>

                        <!-- Email -->
                        <div class="col-span-6 sm:col-span-4">
                            <jet-label for="email" value="Email" />
                            <jet-input id="email" type="email" class="mt-1 block w-full" v-model="form.email" />
                            <jet-input-error v-if="$page.errors.email" :message="$page.errors.email[0]" class="mt-2" />
                        </div>

                        <div v-if="!$page.investor"
                            class="col-span-6 sm:col-span-4"
                        >
                            <jet-label for="send_welcome_email" value="Send Welcome Email" />
                            <b-form-checkbox id="send_welcome_email"
                                             name="send_welcome_email"
                                             value="'1'"
                                             unchecked-value="'0'"
                                             v-model="form.send_welcome_email"
                            />
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
        },

        props: ['user'],

        data() {
            // console.log('this.investor', this.investor);
            return {
                form: this.$inertia.form({
                    '_method': 'POST',
                    name: this.$page.investor ? this.$page.investor.name : '',
                    email: this.$page.investor ? this.$page.investor.email : '',
                    photo: null,
                    send_welcome_email: '0'
                }, {
                    bag: 'storeAdmin',
                    resetOnSuccess: false,
                }),

                photoPreview: null,
            }
        },

        methods: {
            storeAdmin() {
                if (this.$refs.photo) {
                    this.form.photo = this.$refs.photo.files[0]
                }
                if (!this.$page.investor) {
                    this.form.post(route('investors.store'), {
                        preserveScroll: true
                    })
                    .then((response) => {
                        // console.log('response', response);
                        // console.log('form', this.form);
                    });
                } else {
                    this.form.post(route('investors.update', this.$page.investor), {
                        preserveScroll: true
                    });
                }
            },

            selectNewPhoto() {
                this.$refs.photo.click();
            },

            updatePhotoPreview() {
                const reader = new FileReader();

                reader.onload = (e) => {
                    this.photoPreview = e.target.result;
                };

                reader.readAsDataURL(this.$refs.photo.files[0]);
            },

            deletePhoto() {
                this.$inertia.delete(route('current-user-photo.destroy'), {
                    preserveScroll: true,
                }).then(() => {
                    this.photoPreview = null
                });
            },
        },
    }
</script>
