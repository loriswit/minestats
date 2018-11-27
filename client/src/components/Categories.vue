<template>
    <nav>
        <button v-for="(name, category) in categories"
                @click="$root.category = category"
                :class="{ active: $root.category === category }">{{ name }}
        </button>
    </nav>
</template>

<script>
export default {
    name: "Categories",
    data: function()
    {
        return {
            categories: {
                "minecraft:custom:general": "general",
                "minecraft:custom:distance": "distance",
                "minecraft:custom:interaction": "interaction",
                "minecraft:custom:combat": "combat",
                "minecraft:killed": "mobs killed",
                "minecraft:killed_by": "killed by mobs",
                "minecraft:mined": "mined",
                "minecraft:crafted": "crafted",
                "minecraft:used": "used",
                "minecraft:broken": "broken",
                "minecraft:dropped": "dropped",
                "minecraft:picked_up": "picked up"
            }
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

    @media (max-width: 767px)
    {
        nav
        {
            grid-template-columns: 50% 50%;
        }
    }
</style>
