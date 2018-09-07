<template>
    <div class="updater">
        <button @click="update()" :class="{ updating: updating }">
            {{ updating ? "updating..." : "update now"}}
        </button>
        <p v-if="error" class="error">{{ error }}</p>
        <p v-if="date">updated
            <timeago :datetime="date" :auto-update="true"/>
        </p>
    </div>
</template>

<script>
    export default {
        name: "Updater",
        data: function()
        {
            const dateStr = window.localStorage.getItem("date");

            return {
                date: dateStr ? parseInt(dateStr) : null,
                updating: false,
                error: ""
            };
        },
        methods: {
            update: function()
            {
                const url = "https://" + this.$root.server;
                this.updating = true;
                this.$http.get(url + "/status").then(response =>
                {
                    let onlinePlayers = [];
                    if(response.body.status === "online")
                        if(response.body.info.players.online > 0)
                            for(let player of response.body.info.players.sample)
                                onlinePlayers.push(player.id);

                    this.$http.get(url + "/users").then(response =>
                    {
                        const users = response.body;
                        let index = 0;
                        for(let user of users)
                            this.$http.get(url + "/stats/" + user.uuid).then(response =>
                            {
                                user.online = onlinePlayers.includes(user.uuid);
                                user.stats = response.body.stats;

                                if(++index === users.length)
                                {
                                    this.error = "";
                                    this.updating = false;

                                    this.date = Date.now();
                                    this.$root.users = users;

                                    window.localStorage.setItem("users", JSON.stringify(this.$root.users));
                                    window.localStorage.setItem("date", this.date.toString());
                                }
                            });
                    });
                }, () =>
                {
                    this.error = "update failed";
                    this.updating = false;
                });
            }
        },
        created: function()
        {
            this.update();
        }
    }
</script>

<style scoped>
    .updater
    {
        padding: 20px;
        width: 300px;
        text-align: center;
    }

    p
    {
        margin-top: 5px;
        margin-bottom: 0;
    }

    .error
    {
        font-weight: bold;
        color: red;
    }

    button
    {
        background-color: steelblue;
    }

    button:hover
    {
        background-color: royalblue;
    }

    button.updating
    {
        background-color: grey;
    }
</style>
