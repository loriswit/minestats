import Vue from 'vue'
import App from './App.vue'
import VueTimeago from 'vue-timeago'
import VueResource from 'vue-resource'

Vue.config.productionTip = false;

let data = {
    server: "olybri.serveminecraft.net",
    category: "minecraft:custom",
    users: [],
};

const userData = window.localStorage.getItem("users");
if(userData != null)
    data.users = JSON.parse(userData);

Vue.use(VueTimeago);
Vue.use(VueResource);

new Vue({
    render: h => h(App),
    data: data
}).$mount('#app');
