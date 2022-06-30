<template>
    <div class="flex h-full min-h-0">
        <div class="w-1/3 flex justify-end border-r px-12 overflow-auto py-2">
            <div class="">

                <h2 class="font-bold">Feeds</h2>
                <template v-for="feed in feeds">
                    <Feed :feed="feed"/>
                    <hr class="last:hidden">
                </template>

            </div>
        </div>
        <div class="w-2/3 px-12 overflow-auto h-full py-2">
            <div class="max-w-2xl">

                <h2 class="font-bold">Posts</h2>
                <div class="-mx-4">
                    <template v-for="post in posts">
                        <Post :post="post"/>
                        <div class="px-4">
                            <hr>
                        </div>
                    </template>
                </div>

                <div class="py-2">
                    <Link href="/" :data="{page: page + 1}">Next Page</Link>
                </div>

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
