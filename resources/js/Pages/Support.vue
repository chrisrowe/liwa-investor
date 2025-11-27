<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Support
            </h2>
        </template>
        <div class="py-12">
            <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-5">
                    <ul>
                        <li v-for="faq in faqs" :key="faq.q" class="border-b p-4">
                            <h2>{{faq.q}}</h2>
                            <div v-html="faq.a" class="text-xl mb-4"></div>
                            <ul class="ml-4 list-disc">
                                <li v-for="(instruction, index) in faq.instructions" :key="faq.q+index">
                                    {{instruction}}
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout'
    export default {
        components: {
            AppLayout,
        },
        data() {
            return {
                faqs: [
                    {
                        q: 'How do I add investors / users to the portal?',
                        a: `<p>There are two steps to add a user to the portal.</p>
                            <p>First, you'll need to add the user from the Users page.</p>
                            <p>Then you'll need to give the user access to any folders they should be able to view.</p>
                        `,
                        instructions: [
                            'Click your name in near the top right corner',
                            'Click Users from the dropdown menu',
                            'Click "Add User"',
                            'Add the name and email and click save, making sure the box to send a welcome email is checked',
                            'Give the user access to any folders he or she should have access to by navigating to the appropriate page and folder and clicking "Manage Access"'
                        ]
                    },
                    {
                        q: 'How do I give investors / users access to a folder?',
                        instructions: [
                            'Click your name in near the top right corner',
                            'Click Users from the dropdown menu',
                            'Click "Add User"',
                            'Add the name and email and click save, making sure the box to send a welcome email is checked',
                            'Give the user access to any folders he or she should have access to'
                        ]
                    },
                    {
                        q: 'Why didn\'t a user receive the email?',
                        a: `<p>We try to ensure high email deliverability, but sometimes emails will go to junk or be completely blocked by the users ISP.</p>
                            <p>Please follow these steps if a user is not receiving an email.</p>`,
                        instructions: [
                            'Have the user check their junk mail',
                            'Resend a password reset email by clicking your name in the top right -> Users -> Find User -> Edit -> Send Password Reset',
                            'If no email is received, go to the users page and click the "Copy Password Reset Link" button.',
                            'Email the new link directly to the investor using your own email server.'
                        ]
                    },
                    {
                        q: 'Training Call Video',
                        a: `<iframe width="100%" height="515" src="https://www.youtube.com/embed/yZkXFSTeQRE" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>`
                    },
                    {
                        q: 'How do I contact support?',
                        a: `<p>Please email us at support@investordealroom.com with any other questions!</p>`,
                    },
                ]
            }
        }
    }
</script>
