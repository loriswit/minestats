<template>
    <div class="updater">
        <button @click="update()">update now</button>
        <p v-if="error" class="error">{{ error }}</p>
        <p v-if="date">updated
            <timeago :datetime="date" :auto-update="true"></timeago>
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
                error: ""
            };
        },
        methods: {
            update: function()
            {
                const url = "https://" + this.$root.server;
                this.$http.get(url + "/usercache.json").then(response =>
                {
                    const users = response.body;
                    let index = 0;
                    for(let user of users)
                        this.$http.get(url + "/world/stats/" + user.uuid + ".json").then(response =>
                        {
                            user.stats = response.body.stats;
                            if(++index === users.length)
                            {
                                this.error = "";
                                this.date = Date.now();
                                this.$root.users = users;

                                window.localStorage.setItem("users", JSON.stringify(this.$root.users));
                                window.localStorage.setItem("date", this.date.toString());
                            }
                        }, () =>
                        {
                            this.error = "Update failed.";
                        });
                }, () =>
                {
                    this.error = "Update failed.";
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
</style>
