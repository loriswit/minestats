import Vue from "vue"
import App from "./App.vue"
import VueTimeago from "vue-timeago"
import VueResource from "vue-resource"

Vue.config.productionTip = false;

let data = {
    server: "http://mc.loriswit.com",
    category: "minecraft:custom:general",
    users: [],
};

const userData = window.localStorage.getItem("users");
if(userData != null)
    data.users = JSON.parse(userData);

Vue.use(VueTimeago, {locale: "en"});
Vue.use(VueResource);

new Vue({
    render: h => h(App),
    data: data
}).$mount("#app");
