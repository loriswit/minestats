<template>
    <p v-if="!stats">No statistics</p>
    <table v-else>
        <thead>
        <tr>
            <td class="empty"></td>
            <th class="total">total</th>
            <th v-for="user in users">{{ user.name }}</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="(item, key) in stats">
            <th :class="{ total: key === 'total' }">{{ key }}</th>
            <td class="total">{{ format(key, item.total) }}</td>
            <td :class="{ null: item[user.uuid] === 0, total: key === 'total' }"
                v-for="user in users">
                {{ format(key, item[user.uuid]) }}
            </td>
        </tr>
        </tbody>
    </table>
</template>

<script>
    export default {
        name: "Stats",
        props: {
            users: Array,
            category: String,
        },
        computed: {
            stats: function()
            {
                if(this.users.length === 0)
                    return null;

                let stats = {
                    total: {
                        total: 0
                    }
                };

                for(const user of this.users)
                    stats.total[user.uuid] = 0;

                for(const user of this.users)
                    for(const item in user.stats[this.category])
                        if(!stats.hasOwnProperty(item))
                        {
                            stats[item] = {};
                            stats[item].total = 0;

                            for(const user of this.users)
                            {
                                if(!user.stats.hasOwnProperty(this.category))
                                    user.stats[this.category] = {};

                                let value = user.stats[this.category][item];
                                if(value === undefined)
                                    value = 0;

                                stats[item][user.uuid] = value;
                                stats[item].total += value;
                                stats.total[user.uuid] += value;
                                stats.total.total += value;
                            }
                        }

                if(this.category === "minecraft:custom")
                    delete stats.total;

                return stats;
            }
        },

        methods: {
            format(key, value)
            {
                switch(key)
                {
                    case "minecraft:play_one_minute":
                    case "minecraft:time_since_death":
                    case "minecraft:sneak_time":
                    case "minecraft:time_since_rest":
                        if(value < 1200)
                            return Math.floor(value / 20 % 60) + " seconds";
                        else if(value < 72000)
                            return Math.floor(value / 20 / 60 % 60) + " minutes";
                        else
                            return Math.floor(value / 20 / 60 / 60) + " hours";

                    case "minecraft:sprint_one_cm":
                    case "minecraft:walk_one_cm":
                    case "minecraft:walk_under_water_one_cm":
                    case "minecraft:boat_one_cm":
                    case "minecraft:walk_on_water_one_cm":
                    case "minecraft:swim_one_cm":
                    case "minecraft:fly_one_cm":
                    case "minecraft:crouch_one_cm":
                    case "minecraft:fall_one_cm":
                    case "minecraft:climb_one_cm":
                        if(value < 100)
                            return value + " cm";
                        else if(value < 100000)
                            return (value / 100).toFixed(1) + " m";
                        else
                            return (value / 100000).toFixed(1) + " km";

                    default:
                        return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                }
            }
        }
    }
</script>

<style scoped>
    th
    {
        background-color: lightcoral;
        font-weight: bold;
        color: white;
    }

    th.total
    {
        background-color: indianred;
    }

    td
    {
        background-color: whitesmoke;
    }

    td.total
    {
        background-color: navajowhite;
        font-weight: bold;
    }

    td.empty
    {
        background: none;
    }

    td.null:not(.total)
    {
        color: #ddd;
    }

    th, td
    {
        padding: 10px;
    }
</style>
