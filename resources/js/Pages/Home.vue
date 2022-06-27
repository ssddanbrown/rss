<template>
    <h1 class="p-4 text-green-500">Hello {{name}}!</h1>
</template>
<script>
    export default {
        props: {
            name: String,
        }
    }
</script>
