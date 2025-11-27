<template>
    <inertia-link :href="href" :class="classes">
        <slot></slot>
    </inertia-link>
</template>

<script>
    export default {
        props: ['href', 'active'],

        computed: {
            classes() {
                return this.active
                            ? 'inline-flex items-center px-1 pt-1 border-b-2 border-white font-semibold leading-5 text-white hover:text-white hover:no-underline focus:outline-none focus:border-white transition duration-150 ease-in-out'
                            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent font-semibold leading-5 text-white hover:text-white transition duration-150 ease-in-out'
            }
        }
    }
</script>
