<template>
    <div class="py-1 my-2">
        <h4 class="font-bold" :style="{color: feed.color}">{{ feed.name }}</h4>
        <div class="font-mono text-xs overflow-ellipsis">{{ feed.url }}</div>
        <div class="flex gap-1 text-gray-600 text-sm">
            <Tag v-for="tag in feed.tags" :tag="tag" class="inline-block">{{ tag }}</Tag>
        </div>
        <p v-if="feed.reloading">
            Reloading...
        </p>
        <p v-if="pendingRefresh">
            <a target="#" @click="reload">Reloaded, Refresh page to show changes</a>
        </p>
    </div>
</template>
<script>

import axios from "axios";
import Tag from "./Tag";

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
            const resp = await axios.get(`/feed`, {params: {url: this.feed.url}});
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
