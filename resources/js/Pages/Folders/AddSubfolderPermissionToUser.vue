<template>
    <add-subfolder-permission-to-user
        :folderInvestors="$page.folder.users"
        :allInvestors="$page.allInvestors"
        :folderType="$page.folderType"
        :folder="$page.folder"
    >
    </add-subfolder-permission-to-user>
</template>

<script>
    import AddSubfolderPermissionToUser from '../../Components/AddSubfolderPermissionToUser'

    export default {
        components: {
            AddSubfolderPermissionToUser
        },

        props: ['user'],

        data() {
            // console.log('this.investor', this.investor);
            return {
                form: this.$inertia.form({
                    '_method': 'POST',
                    investors: [],
                }, {
                    bag: 'saveFolder',
                    resetOnSuccess: false,
                }),
                isLoading: false,
                folderInvestors: this.$page.folder ? this.$page.folder.users : [],
                allInvestors: this.$page.allInvestors,
                photoPreview: null,
            }
        }
    }
</script>
