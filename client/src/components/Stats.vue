<template>
    <p v-if="!stats">No statistics</p>
    <table v-else>
        <thead>
        <tr>
            <td class="empty"></td>
            <th :class="{ sorted: sortingUser === 'total' && userSortable, sortable: userSortable }"
                @click="sortingUser = 'total'">
                total
            </th>
            <td class="h-space"></td>
            <th v-for="user in stats.total.users" :key="user.uuid"
                :title="user.online ? 'online' : null"
                :class="{ sorted: sortingUser === user.uuid && userSortable, sortable: userSortable }"
                @click="sortingUser = user.uuid">
                {{ user.name }}
                <span v-if="user.online" class="online"></span>
            </th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <th :class="{ sorted: sortingItem === 'total' }"
                @click="sortingItem = 'total'"
                class="sortable">
                {{ formatItem(stats.total.name) }}
            </th>
            <td :class="{ sorted: (sortingUser === 'total' && userSortable) || sortingItem === 'total' }">
                {{ formatValue(stats.total.name, stats.total.total) }}
            </td>
            <td class="h-space"></td>
            <td v-for="user in stats.total.users" :key="user.uuid"
                :class="{ sorted: (sortingUser === user.uuid && userSortable) || sortingItem === 'total' }">
                {{ formatValue(stats.total.name, user.value) }}
            </td>
        </tr>
        <tr v-if="userSortable">
            <th :class="{ sorted: sortingItem === stats.eachHour.name }"
                @click="sortingItem = stats.eachHour.name"
                class="sortable">
                {{ formatItem(stats.eachHour.name) }}
            </th>
            <td :class="{ sorted: sortingUser === 'total' || sortingItem === stats.eachHour.name }">
                {{ formatValue(stats.eachHour.name, stats.eachHour.total) }}
            </td>
            <td class="h-space"></td>
            <td v-for="user in stats.eachHour.users" :key="user.uuid"
                :class="{ sorted: sortingUser === user.uuid || sortingItem === stats.eachHour.name }">
                {{ formatValue(stats.eachHour.name, user.value) }}
            </td>
        </tr>
        <tr class="v-space"></tr>
        <tr v-for="item in stats.items" :key="item.name">
            <th :class="{ sorted: sortingItem === item.name }"
                @click="sortingItem = item.name"
                class="sortable">
                {{ formatItem(item.name) }}
            </th>
            <td :class="{ sorted: (sortingUser === 'total' && userSortable) || sortingItem === item.name }">
                {{ formatValue(item.name, item.total) }}
            </td>
            <td class="h-space"></td>
            <td v-for="user in item.users" :key="user.uuid"
                :class="{ null: user.value === 0,
                sorted: (sortingUser === user.uuid && userSortable) || sortingItem === item.name }">
                {{ formatValue(item.name, user.value) }}
            </td>
        </tr>
        </tbody>
    </table>
</template>

<script>
import Stats from "@/stats/stats";

export default {
    name: "Stats",
    data()
    {
        return {
            stats: null,
            sortingUser: "total",
            sortingItem: "total"
        }
    },
    props: {
        users: Array,
        category: String,
    },
    computed: {
        userSortable()
        {
            return this.stats.total.name === "total";
        }
    },
    watch: {
        users()
        {
            this.init();
        },
        category(value)
        {
            this.stats.update(value);

            if(!this.userSortable || this.sortingItem !== this.stats.eachHour.name)
                this.sortingItem = "total";

            this.stats.sortByItem(this.sortingItem);

            if(this.userSortable)
                this.stats.sortByUser(this.sortingUser);
        },
        sortingItem(value)
        {
            this.stats.sortByItem(value);
        },
        sortingUser(value)
        {
            if(this.userSortable)
                this.stats.sortByUser(value);
        }
    },
    methods: {
        init()
        {
            if(!this.users.length)
                return;

            this.stats = new Stats(this.users);
            this.stats.update(this.category);
            this.stats.sortByItem(this.sortingItem);
            if(this.userSortable)
                this.stats.sortByUser(this.sortingUser);
        },
        formatItem(name)
        {
            return name.replace("minecraft:", "")
                .replace("one_cm", "distance")
                .replace("one_minute", "time")
                .replace(/_/g, " ");
        },
        formatValue(name, value)
        {
            if(this.category === "minecraft:custom:distance")
                if(value < 100)
                    return value + " cm";
                else if(value < 100000)
                    return (value / 100).toFixed(1) + " m";
                else if(value < 100000000)
                    return (value / 100000).toFixed(1).toLocaleString() + " km";
                else
                    return Math.floor(value / 100000).toLocaleString() + " km";

            if(/:damage_/.test(name))
                return Math.floor(value / 20).toLocaleString() + " ♡";

            switch(name)
            {
                case "minecraft:play_one_minute":
                case "minecraft:time_since_death":
                case "minecraft:sneak_time":
                case "minecraft:time_since_rest":
                    if(value < 1200)
                        return Math.floor(value / 20 % 60) + " sec";
                    else if(value < 72000)
                        return Math.floor(value / 1200 % 60) + " min";
                    else
                        return Math.floor(value / 72000) + " hrs";

                default:
                    return Math.round(value).toLocaleString();
            }
        }

    },
    created()
    {
        this.init();
    }
}
</script>

<style scoped>
    th, td
    {
        white-space: nowrap;
        padding: 8px;
    }

    th
    {
        font-weight: normal;
        background-color: lightcoral;
        color: white;
        border: 2px solid transparent;
    }

    th.sortable
    {
        cursor: pointer;
    }

    th.sortable:hover:not(.sorted)
    {
        background-color: indianred;
    }

    th.sorted
    {
        color: darkred;
        background-color: pink;
        border: 2px solid indianred;
        font-weight: bold;
        cursor: default;
    }

    td
    {
        background-color: whitesmoke;
    }

    td.sorted
    {
        background-color: papayawhip;
        font-weight: bold;
    }

    td.empty
    {
        background: none;
    }

    td.null:not(.sorted)
    {
        color: #ddd;
    }

    .v-space
    {
        height: 10px;
    }

    .h-space
    {
        min-width: 10px;
        padding: 0;
        background: none;
    }

    .online
    {
        margin-left: 3px;
        height: 10px;
        width: 10px;
        background-color: lightgreen;
        border-radius: 50%;
        display: inline-block;
    }
</style>
