<template>
    <a :href="post.url" target="_blank" class="py-1 px-4 block">
        <h3 class="text-xl">{{ post.title }}</h3>
        <p>{{ post.description}}</p>
        <small class="italic">{{ relatedPublishedTime }}</small>
        <div class="flex gap-1">
            <span v-for="tag in post.tags" class="inline-block">{{ tag }}</span>
        </div>
    </a>
</template>
<script>
export default {
    props: {
        post: Object,
    },
    computed: {
        relatedPublishedTime() {
            const date = new Date(this.post.published_at * 1000);
            const dateFormat = {weekday: "short", year: "numeric", month: "short", day: "numeric", hour: "numeric", minute: "numeric"};
            return date.toLocaleString("en-GB", dateFormat);
        }
    }
}
</script>
