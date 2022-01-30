
// css
import "@mdi/font/css/materialdesignicons.css";
import "material-design-icons-iconfont/dist/material-design-icons.css";
import "sweetalert2/dist/sweetalert2.min.css";
import Vue from 'vue';
import VueRouter from "vue-router";
import VueSweetalert2 from "vue-sweetalert2";
import Vuetify from "vuetify";
import 'vuetify/dist/vuetify.min.css';
//Main pages
import App from './app.vue';
import Axios from "./axios";
import routes from './routes';





Vue.use(VueRouter);
Vue.use(Vuetify);
Vue.use(VueSweetalert2);

const router = new VueRouter({
    routes,
    mode: "history"
});

Vue.prototype.$http = Axios;

const app = new Vue({
    el: "#app",
    components: { 'main-app': App },
    vuetify: new Vuetify(),
    router,
});
