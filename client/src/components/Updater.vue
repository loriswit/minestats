<template>
    <div class="updater">
        <div v-if="archivedServers">
            <select v-model="selectedServer" @change="changeServer">
                <option :value="activeServer">{{ activeServer }}</option>
                <option v-for="name in archivedServers" :key="name"
                        :value="name">
                    {{ name }}
                </option>
            </select>
        </div>
        <div>
            <button @click="update()" :class="{ updating: updating }">
                {{ updating ? "updating..." : "update now"}}
            </button>
        </div>
        <p v-if="error" class="error">{{ error }}</p>
        <p v-if="date">updated
            <timeago :datetime="date" :auto-update="true"/>
        </p>
    </div>
</template>

<script>
export default {
    name: "Updater",
    data()
    {
        return {
            dateStr: window.localStorage.getItem("date"),
            updating: false,
            error: "",

            selectedServer: null,
            activeServer: null,
            archivedServers: null
        };
    },
    computed: {
        date()
        {
            return this.dateStr ? parseInt(this.dateStr) : null;
        }
    },
    methods: {
        update()
        {
            this.updating = true;

            if(this.selectedServer === this.activeServer)
            {
                this.$http.get(this.$root.server + "/status").then(response =>
                {
                    if(this.activeServer === null)
                    {
                        this.activeServer = response.body.name;
                        this.selectedServer = this.activeServer;
                    }

                    let onlinePlayers = [];
                    if(response.body.status === "online")
                        if(response.body.info.players.online > 0)
                            for(let player of response.body.info.players.sample)
                                onlinePlayers.push(player.id);

                    this.getStats("", onlinePlayers);
                }, this.onUpdateFailed);
            }
            else
                this.getStats("/archived/" + this.selectedServer, []);
        },
        getStats(path, onlinePlayers)
        {
            const url = this.$root.server;
            this.$http.get(url + path + "/users").then(response =>
            {
                const users = response.body;
                const invalidUsers = [];

                let index = 0;
                for(let user of users)
                    this.$http.get(url + path + "/stats/" + user.uuid).then(response =>
                    {
                        user.online = onlinePlayers.includes(user.uuid);
                        user.stats = response.body.stats;

                        if(++index === users.length)
                            this.onUpdateFinished(users, invalidUsers)
                    }, () =>
                    {
                        invalidUsers.push(user);

                        if(++index === users.length)
                            this.onUpdateFinished(users, invalidUsers)
                    });
            }, this.onUpdateFailed);
        },
        onUpdateFinished(users, invalidUsers)
        {
            for(const user of invalidUsers)
            {
                const index = users.indexOf(user);
                users.splice(index, 1);
            }

            this.error = "";
            this.updating = false;

            this.dateStr = Date.now();
            this.$root.users = users;

            if(this.selectedServer === this.activeServer)
            {
                window.localStorage.setItem("users", JSON.stringify(this.$root.users));
                window.localStorage.setItem("date", this.date.toString());
            }
            else
            {
                window.localStorage.setItem(this.selectedServer + "/users", JSON.stringify(this.$root.users));
                window.localStorage.setItem(this.selectedServer + "/date", this.date.toString());
            }
        },
        onUpdateFailed()
        {
            this.error = "update failed";
            this.updating = false;
        },
        getArchivedServers()
        {
            this.$http.get(this.$root.server + "/archived").then(response =>
            {
                this.archivedServers = response.body;
            });
        },
        changeServer()
        {
            let userData;
            if(this.selectedServer === this.activeServer)
            {
                userData = window.localStorage.getItem("users");
                this.dateStr = window.localStorage.getItem("date");
            }
            else
            {
                userData = window.localStorage.getItem(this.selectedServer + "/users");
                this.dateStr = window.localStorage.getItem(this.selectedServer + "/date");
            }

            if(userData != null)
                this.$root.users = JSON.parse(userData);

            this.update();
        }
    },
    created()
    {
        this.update();
        this.getArchivedServers();
    }
}
</script>

<style scoped>
    .updater
    {
        display: flex;
        flex-direction: column;

        padding: 20px;
        width: 300px;
        text-align: center;
    }

    select
    {
        font-family: "Open Sans", sans-serif;
        font-size: 1em;
        color: white;
        background: sandybrown;
        border: none;
        cursor: pointer;

        min-height: 40px;
        min-width: 150px;
        padding: 5px;
        margin: 5px;
    }

    select:hover
    {
        background: darkorange;
    }

    select:focus
    {
        outline: 0;
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
