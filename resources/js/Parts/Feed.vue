<template>
    <div class="py-1 my-2">
        <h4 class="font-bold text-black dark:text-gray-400" :style="{color: feed.color}">
            <Link :href="`f/${encodeURIComponent(encodeURIComponent(feed.url))}`">{{ feed.name }}</Link>
            <span v-if="feed.hidden" title="Posts hidden by default" class="text-xs ml-2 text-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="inline bi bi-eye-slash" viewBox="0 0 16 16">
                  <path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7.028 7.028 0 0 0-2.79.588l.77.771A5.944 5.944 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.134 13.134 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755-.165.165-.337.328-.517.486l.708.709z"/>
                  <path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829l.822.822zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829z"/>
                  <path d="M3.35 5.47c-.18.16-.353.322-.518.487A13.134 13.134 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7.029 7.029 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884-12-12 .708-.708 12 12-.708.708z"/>
                </svg>
            </span>
        </h4>
        <div class="font-mono text-gray-600 dark:text-gray-500 text-xs my-1 overflow-ellipsis whitespace-nowrap w-full overflow-hidden">{{ feed.url }}</div>
        <div class="flex gap-1 text-gray-600 dark:text-gray-500 text-sm flex-wrap">
            <Tag v-for="tag in feed.tags" :tag="tag" class="inline-block">{{ tag }}</Tag>
        </div>
        <div v-if="feed.reloading || pendingRefresh" class="flex gap-2 items-center text-gray-600 text-sm mt-1">
            <div :class="{'animate-spin': feed.reloading, 'text-green-600': pendingRefresh}">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M11.534 7h3.932a.25.25 0 0 1 .192.41l-1.966 2.36a.25.25 0 0 1-.384 0l-1.966-2.36a.25.25 0 0 1 .192-.41zm-11 2h3.932a.25.25 0 0 0 .192-.41L2.692 6.23a.25.25 0 0 0-.384 0L.342 8.59A.25.25 0 0 0 .534 9z"/>
                    <path fill-rule="evenodd" d="M8 3c-1.552 0-2.94.707-3.857 1.818a.5.5 0 1 1-.771-.636A6.002 6.002 0 0 1 13.917 7H12.9A5.002 5.002 0 0 0 8 3zM3.1 9a5.002 5.002 0 0 0 8.757 2.182.5.5 0 1 1 .771.636A6.002 6.002 0 0 1 2.083 9H3.1z"/>
                </svg>
            </div>
            <p v-if="feed.reloading">
                Reloading feed...
            </p>
            <p v-if="pendingRefresh" class="text-green-800">
                <a target="#" @click="reload" class="cursor-pointer">Reloaded, refresh to show changes</a>
            </p>
        </div>
    </div>
</template>
<script>

import axios from "axios";
import Tag from "./Tag.vue";

export default {
    components: {Tag},
    props: {
        feed: Object
    },
    data() {
        return {
            pendingRefresh: false,
            reloadingPoll: null,
        }
    },
    methods: {
        reload() {
            window.location.reload();
        },
        async pollFeedStatus() {
            const resp = await axios.get(`feed`, {params: {url: this.feed.url}});
            if (resp?.data?.outdated) {
                this.reloadingPoll = setTimeout(this.pollFeedStatus.bind(this), 3000);
            } else {
                this.feed.reloading = false;
                this.pendingRefresh = true;
            }
        }
    },
    mounted() {
        if (this.feed.reloading) {
            this.reloadingPoll = setTimeout(this.pollFeedStatus.bind(this), 3000);
        }
    },
    unmounted() {
        if (this.reloadingPoll) {
            window.clearTimeout(this.reloadingPoll);
        }
    }
}
</script>
