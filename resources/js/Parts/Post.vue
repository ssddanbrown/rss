<template>
    <div
        :class="{'py-2': formatCompact}"
        class="rss-post p-4 block border border-transparent hover:border-gray-200 hover:bg-gray-100
        dark:hover:border-gray-700 dark:hover:bg-gray-800 rounded hover:relative hover:z-20 -my-px">

        <div class="flex text-sm text-gray-600 dark:text-gray-400 items-center flex-wrap">
            <Link :href="`f/${encodeURIComponent(encodeURIComponent(post.feed.url))}`" :style="{color: post.feed.color}" class="font-bold text-xs block">{{ post.feed.name }}</Link>
            <div class="px-2">&bull;</div>
            <div class="italic" :title="isoTime">About {{ relatedPublishedTime }} ago</div>
            <div class="px-2 hidden md:block">&bull;</div>
            <div class="flex gap-2 basis-full md:basis-auto">
                <Tag v-for="tag in post.feed.tags" :tag="tag"/>
            </div>
        </div>

        <div :class="{'flex items-center gap-5': formatList && post.thumbnail}">
            <div v-if="post.thumbnail && !formatCompact" :class="{'w-32': formatList}">
                <a :href="post.url" target="_blank" class="block overflow-hidden rounded my-2 border dark:border-black">
                    <img :src="`storage/${post.thumbnail}`"
                         loading="lazy"
                         class="w-full h-32 md:h-48 lg:h-64 object-cover saturate-50"
                         :class="{'h-16 md:h-16 lg:h-16': formatList}"
                         :alt="post.title">
                </a>
            </div>

            <div :class="{'whitespace-nowrap overflow-ellipsis overflow-hidden': formatCompact, 'flex-1': formatList}">
                <h3 class="font-bold text-black dark:text-gray-300" :class="{'text-lg': formatCard, 'text-md': formatList, 'inline': formatCompact}">
                    <a :href="post.url" target="_blank" class="hover:underline">{{ post.title }}</a>
                </h3>

                <p class="text-gray-600 dark:text-gray-400" :class="{'inline ml-2 text-sm': formatCompact}">
                    {{ post.description}}
                </p>
            </div>
        </div>
    </div>
</template>
<script>
import Tag from "./Tag.vue";
import {timestampToRelativeTime} from "../util";

export default {
    components: {Tag},
    props: {
        post: Object,
        format: {
            type: String,
            default: 'list',
        }
    },
    computed: {
        isoTime() {
            return (new Date(this.post.published_at * 1000)).toISOString();
        },
        relatedPublishedTime() {
            return timestampToRelativeTime(this.post.published_at);
        },
        formatCard() {
            return this.format === 'card';
        },
        formatList() {
            return this.format === 'list';
        },
        formatCompact() {
            return this.format === 'compact';
        }
    }
}
</script>
