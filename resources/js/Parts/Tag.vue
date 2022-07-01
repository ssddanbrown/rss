<template>
    <Link :href="`/t/${urlTag}`"
          class="inline-block">{{ tag }}</Link>
</template>

<script>
export default {
    props: {
        tag: String,
    },
    computed: {
        urlTag() {
            return encodeURIComponent(this.tag.slice(1));
        },
    }
}
</script>
