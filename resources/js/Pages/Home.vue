<template>
    <div class="flex">
        <div class="w-1/3">
            <h2 class="font-bold">Feeds</h2>
            <div v-for="feed in feeds">
                <Feed :feed="feed"/>
            </div>
        </div>
        <div class="w-2/3">
            <h2 class="font-bold">Posts</h2>
            <div v-for="post in posts">
                <Post :post="post"/>
            </div>

            <div class="py-2">
                <Link href="/" :data="{page: page + 1}">Next Page</Link>
            </div>
        </div>
    </div>

</template>
<script>
    import Post from "../Parts/Post";
    import Feed from "../Parts/Feed";

    export default {
        components: {Feed, Post},
        props: {
            page: Number,
            feeds: Array,
            posts: Array,
        }
    }
</script>
