
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

// window.$ = window.jQuery = require('jquery');
require('./bootstrap');

window.Vue = require('vue');

require('../../node_modules/bootstrap-select/dist/js/bootstrap-select');
// require('../../node_modules/bootstrap-select/js/bootstrap-select');

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component('vue-multiselect', window.VueMultiselect.default)
Vue.component('chat-app', require('./components/ChatApp.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */


import moment from 'moment';



const app = new Vue({
    el: '#app',
});
