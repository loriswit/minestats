import Item from "./item";

export default class Stats
{
    constructor(users)
    {
        this.users = users;
        this.items = users.length === 0 ? null : [];
        this.total = new Item("total", this.users);
    }

    update(category)
    {
        this.items = [];
        this.total = new Item("total", this.users);
        this.eachHour = new Item("every_hour", this.users);

        const type = category.match(/^[^:]+:[^:]+/)[0];
        for(const user of this.users)
        {
            for(const itemName in user.stats[type])
            {
                if(!Stats.itemInCategory(itemName, category))
                    continue;

                const item = this.putItem(itemName);
                const value = user.stats[type][itemName];

                item.total += value;
                item.getUser(user.uuid).value += value;

                this.total.total += value;
                this.total.getUser(user.uuid).value += value;
            }

            const eachHour =
                this.total.getUser(user.uuid).value /
                (user.stats["minecraft:custom"]["minecraft:play_one_minute"] / 72000);

            this.eachHour.total += eachHour;
            this.eachHour.getUser(user.uuid).value = eachHour;
        }

        // if mixed stats, remove total
        if(category === "minecraft:custom:general")
            this.substituteTotal("minecraft:play_one_minute");
        if(category === "minecraft:custom:combat")
            this.substituteTotal("minecraft:mob_kills");
    }

    sortByUser(uuid)
    {
        this.items.sort(function(a, b)
        {
            return b.getUser(uuid).value - a.getUser(uuid).value;
        });
    }

    sortByItem(name)
    {
        const sortingItem = this.getItem(name);
        for(const item of this.items)
            if(item.name !== name)
                item.users.sort(function(a, b)
                {
                    return sortingItem.getUser(b.uuid).value - sortingItem.getUser(a.uuid).value;
                });

        if(sortingItem !== this.total)
            this.total.users.sort(function(a, b)
            {
                return sortingItem.getUser(b.uuid).value - sortingItem.getUser(a.uuid).value;
            });

        if(sortingItem !== this.eachHour)
            this.eachHour.users.sort(function(a, b)
            {
                return sortingItem.getUser(b.uuid).value - sortingItem.getUser(a.uuid).value;
            });

        sortingItem.users.sort(function(a, b)
        {
            return b.value - a.value;
        });
    }

    substituteTotal(substitution)
    {
        this.total = this.getItem(substitution);
        this.items = this.items.filter(function(item)
        {
            return item.name !== substitution;
        });
    }

    putItem(name)
    {
        const item = this.getItem(name);
        if(item)
            return item;

        this.items.push(new Item(name, this.users));
        return this.items[this.items.length - 1];
    }

    getItem(name)
    {
        if(name === "total")
            return this.total;

        if(name === this.eachHour.name)
            return this.eachHour;

        return this.items.find(function(item)
        {
            return item.name === name;
        });
    }

    static itemInCategory(itemName, category)
    {
        if(!/^minecraft:custom/.test(category))
            return true;

        if(/:.+_one_cm$/.test(itemName))
            return category === "minecraft:custom:distance";

        if(/:(damage_.+|.+_kills)/.test(itemName))
            return category === "minecraft:custom:combat";

        const generalStats = [
            "leave_game", "play_one_minute", "time_since_death", "sneak_time",
            "jump", "deaths", "drop", "time_since_rest"
        ];

        for(const stat of generalStats)
            if(itemName === "minecraft:" + stat)
                return category === "minecraft:custom:general";

        return category === "minecraft:custom:interaction";
    }
}
