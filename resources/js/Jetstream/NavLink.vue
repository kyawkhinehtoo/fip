<template>
    <inertia-link :href="href" :class="classes" >
        <slot></slot>
    </inertia-link>
</template>

<script>
    export default {
        props: ['href', 'active'],
        computed: {
            classes() {
                return this.active
                            ? 'text-blue-600 hover:text-blue-500 text-xs uppercase py-3 font-bold block px-4 rounded-md bg-blueGray-100 shadow-inner'
                            : 'text-blueGray-700 text-xs uppercase py-3 font-bold block px-4 rounded-md hover:bg-gray-100'
            }
        }
    }
</script>
