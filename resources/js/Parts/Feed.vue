<template>
    <div class="py-1">
        <h4>{{ feed.name }}</h4>
        <p>{{ feed.url }}</p>
        <div class="flex gap-1">
            <span v-for="tag in feed.tags" class="inline-block">{{ tag }}</span>
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

export default {
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
