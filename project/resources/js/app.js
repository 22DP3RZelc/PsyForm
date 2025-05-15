import './bootstrap';
import { createApp } from 'vue';
import ProfilePage from './components/ProfilePage.vue';
import RegistrationPage from './components/RegistrationPage.vue';
import LoginPage from './components/LoginPage.vue';
import AdminPage from './components/AdminPage.vue';
import HomePage from './components/HomePage.vue';
import AboutPage from './components/AboutPage.vue';
import ExamplePage from './components/ExamplePage.vue';
import AppLayout from './layouts/App.vue';

createApp({})
    /* LAYOUTS */
    .component('AppLayout', AppLayout)

    /* PAGES */
    .component('home-page', HomePage)
    .component('login-page', LoginPage)
    .component('profile-page', ProfilePage)
    .component('registration-page', RegistrationPage)
    .component('admin-page', AdminPage)
    .component('about-page', AboutPage)
    .component('example-page', ExamplePage)

    .mount('#app');

