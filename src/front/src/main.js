import Vue from 'vue'
import VueRouter from 'vue-router'
import axios from 'axios'
import App from './App'
import Orders from './pages/Orders'
import Revenue from './pages/Revenue'
import Saved from './pages/Saved'

Vue.config.productionTip = false

Vue.use(VueRouter)

const axiosInstance = axios.create({
  baseURL: '/admin/commerceinsights/',
})

Object.defineProperty(Vue.prototype, '$axios', {
  get: () => axiosInstance,
})

const queryProps = route => ({query: route.query})

const router = new VueRouter({
  mode: 'history',
  base: '/admin/commerceinsights/view',
  routes: [
    { path: '/revenue', component: Revenue, props: queryProps },
    { path: '/orders', component: Orders, props: queryProps },
    { path: '/saved', component: Saved },
  ],
})

/* eslint-disable no-new */
new Vue({
  el: '#main',
  router,
  components: { App },
  template: '<App/>',
})
