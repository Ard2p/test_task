require('./bootstrap');

require('bootstrap');
window.Vue = require('vue');

import store from './store/index';

import { routes } from './routes';
import VueRouter from 'vue-router'
Vue.use(VueRouter)
const router = new VueRouter({
  routes,
  mode: 'history'
});

const app = new Vue({
  el: '#app',
  router,
  store
});