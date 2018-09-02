<template>
    <nav>
        <button v-for="link in links"
                @click="$root.category = link"
                :class="{ active: $root.category === link }">{{ link }}
        </button>
    </nav>
</template>

<script>
    export default {
        name: "Categories",
        data: function()
        {
            return {
                links: [
                    "minecraft:custom",
                    "minecraft:killed",
                    "minecraft:killed_by",
                    "minecraft:mined",
                    "minecraft:crafted",
                    "minecraft:used",
                    "minecraft:broken",
                    "minecraft:dropped",
                    "minecraft:picked_up"
                ]
            };
        },
    }
</script>

<style scoped>
    nav
    {
        flex: 1;
        max-width: 700px;
        display: grid;
        width: 60%;
        grid-template-columns: 33.33% 33.33% 33.33%;
    }

    button
    {
        background: mediumseagreen;
    }

    @media (max-width: 1024px)
    {
        nav
        {
            grid-template-columns: 50% 50%;
        }
    }

    @media (max-width: 767px)
    {
        nav
        {
            grid-template-columns: 100%;
        }
    }
</style>
