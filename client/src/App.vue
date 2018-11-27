<template>
    <div id="app">
        <header>
            <categories/>
            <updater/>
        </header>
        <stats :users="$root.users" :category="$root.category" class="stats"/>
    </div>
</template>

<script>
import Stats from "./components/Stats.vue"
import Updater from "./components/Updater.vue"
import Categories from "./components/Categories.vue"

export default {
    name: 'app',
    components: {
        categories: Categories,
        stats: Stats,
        updater: Updater
    },
}
</script>

<style>
    @import url("https://fonts.googleapis.com/css?family=Open+Sans");

    #app
    {
        font-family: "Open Sans", sans-serif;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        text-align: center;
        color: #224;
        margin-top: 10px;
    }

    header
    {
        max-width: 1000px;
        margin: auto;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-evenly;
    }

    button
    {
        font-family: "Open Sans", sans-serif;
        font-size: 1em;
        color: white;
        background: mediumseagreen;
        border: 2px solid transparent;
        cursor: pointer;

        min-height: 40px;
        min-width: 150px;
        padding: 5px;
        margin: 5px;
    }

    button:focus
    {
        outline: 0;
    }

    button:hover
    {
        background-color: seagreen;
    }

    button.active
    {
        background: palegreen;
        color: darkgreen;
        font-weight: bold;
        border: 2px solid mediumseagreen;

        cursor: default;
        pointer-events: none;
    }

    .stats
    {
        margin: 20px auto auto;
    }
</style>
