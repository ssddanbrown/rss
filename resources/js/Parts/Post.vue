<template>
    <div class="rss-post p-4 block border border-transparent hover:border-gray-200 hover:bg-gray-100 rounded hover:relative hover:z-20 -my-px">

        <div class="flex text-sm text-gray-600 items-center">
            <Link :href="`/f/${encodeURIComponent(encodeURIComponent(post.feed.url))}`" :style="{color: post.feed.color}" class="font-bold text-xs block">{{ post.feed.name }}</Link>
            <div class="px-2">&bull;</div>
            <div class="italic" :title="isoTime">About {{ relatedPublishedTime }} ago</div>
            <div class="px-2">&bull;</div>
            <div class="flex gap-2">
                <Tag v-for="tag in post.feed.tags" :tag="tag"/>
            </div>
        </div>

        <div v-if="post.thumbnail">
            <a :href="post.url" target="_blank" class="block overflow-hidden rounded my-2 border">
                <img :src="`/storage/${post.thumbnail}`"
                     loading="lazy"
                     class="w-full h-64 object-cover saturate-50"
                     :alt="post.title">
            </a>
        </div>

        <h3 class="text-lg font-bold">
            <a :href="post.url" target="_blank" class="hover:underline">{{ post.title }}</a>
        </h3>

        <p class="text-gray-600">
            {{ post.description}}
        </p>
    </div>
</template>
<script>
import Tag from "./Tag.vue";
import {timestampToRelativeTime} from "../util";

export default {
    components: {Tag},
    props: {
        post: Object,
    },
    computed: {
        isoTime() {
            return (new Date(this.post.published_at * 1000)).toISOString();
        },
        relatedPublishedTime() {
            return timestampToRelativeTime(this.post.published_at);
        }
    }
}
</script>
