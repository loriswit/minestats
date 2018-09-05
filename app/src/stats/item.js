export default class Item
{
    constructor(name, users)
    {
        this.name = name;
        this.total = 0;
        this.users = [];

        for(const user of users)
            this.users.push({
                uuid: user.uuid,
                name: user.name,
                online: user.online,
                value: 0,
            });
    }

    getUser(uuid)
    {
        if(uuid === "total")
            return {value: this.total};

        return this.users.find(function(user)
        {
            return user.uuid === uuid;
        });
    }
}
