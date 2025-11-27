require('./bootstrap');

require('moment');

import Vue from 'vue';

import { InertiaApp } from '@inertiajs/inertia-vue';
import { InertiaForm } from 'laravel-jetstream';
import PortalVue from 'portal-vue';
import 'font-awesome/css/font-awesome.min.css'
import BootstrapVue from 'bootstrap-vue'
import VueClipboard from 'vue-clipboard2'

Vue.mixin({ methods: { route } });
Vue.use(InertiaApp);
Vue.use(InertiaForm);
Vue.use(PortalVue);
Vue.use(BootstrapVue);
Vue.use(VueClipboard)

import RouterLink from './Components/RouterLink'
Vue.component('RouterLink', RouterLink)
Vue.prototype.$router = 'fake'

import { BFormSelect } from 'bootstrap-vue'
Vue.component('b-form-select', BFormSelect)

Vue.mixin({
  methods: {
    paginationLinks: function (links) {
      return links
            .filter(
                (link) => {
                    console.log('link.url', link.url)
                    return typeof link.label == 'number'
                }
            )
            .map(
                (link) => {
                    return link.url
            })
    },
  },
})

const app = document.getElementById('app');

new Vue({
    render: (h) =>
        h(InertiaApp, {
            props: {
                initialPage: JSON.parse(app.dataset.page),
                resolveComponent: (name) => require(`./Pages/${name}`).default,
            },
        }),
}).$mount(app);
