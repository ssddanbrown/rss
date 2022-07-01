<template>
    <div class="flex h-full min-h-0">
        <div class="w-1/3 flex justify-end border-r px-12 overflow-auto py-2">
            <div class="w-80">

                <div>
                    <h2 class="font-bold">Feeds</h2>
                    <template v-for="feed in feeds">
                        <Feed :feed="feed"/>
                        <hr class="last:hidden">
                    </template>
                </div>

                <div v-if="tags.length > 0">
                    <h2 class="font-bold mt-12">Tags</h2>
                    <template v-for="tag in tags">
                        <Tag :tag="tag" class="mr-3 font-bold text-gray-600"/>
                    </template>
                </div>

            </div>
        </div>
        <div class="w-2/3 px-12 overflow-auto h-full py-2">
            <div class="max-w-2xl">

                <h2 class="font-bold">
                    <Link href="/">Posts</Link>
                    <span v-if="tag"> / #{{ tag }}</span>
                    <span v-if="feed && feeds.length === 1"> / <span :style="{ color: feeds[0].color}">{{ feeds[0].name }}</span></span>
                </h2>
                <div class="-mx-4">
                    <template v-for="post in posts">
                        <Post :post="post"/>
                        <div class="px-4">
                            <hr>
                        </div>
                    </template>
                </div>

                <div class="py-2 flex">
                    <Link v-if="page > 1" href="" :data="{page: page-1}" class="cursor-pointer text-gray-600 font-bold py-2 hover:text-black">&laquo; Previous Page</Link>
                    <Link v-if="posts.length === 100" href="" :data="{page: page + 1}" class="ml-auto cursor-pointer text-gray-600 font-bold py-2 hover:text-black">Next Page &raquo;</Link>
                </div>

            </div>
        </div>
    </div>

</template>
<script>
    import Post from "../Parts/Post.vue";
    import Feed from "../Parts/Feed.vue";
    import Tag from "../Parts/Tag.vue";

    export default {
        components: {Tag, Feed, Post},
        props: {
            page: Number,
            feeds: Array,
            posts: Array,
            tag: {
                type: String,
                default: null,
            },
            feed: {
                type: String,
                default: null,
            }
        },
        computed: {
            tags() {
                const tags = {};

                for (let i = 0; i < this.feeds.length; i++) {
                    const feed = this.feeds[i];
                    for (const tag of feed.tags) {
                        tags[tag] = true;
                    }
                }

                return Object.keys(tags).sort();
            }
        }
    }
</script>
