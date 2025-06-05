import './bootstrap';
import { createApp } from 'vue';
import AppLayout from './layouts/App.vue';
import ProfilePage from './components/ProfilePage.vue';
import RegistrationPage from './components/RegistrationPage.vue';
import LoginPage from './components/LoginPage.vue';
import AdminPage from './components/AdminPage.vue';
import HomePage from './components/HomePage.vue';
import AboutPage from './components/AboutPage.vue';
import TestCreatePage from './components/Tests/TestCreatePage.vue';
import TestCompletePage from './components/Tests/TestCompletePage.vue';
import EditProfilePage from './components/EditProfilePage.vue';

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
    .component('test-create-page', TestCreatePage)

    .component('test-complete-page', TestCompletePage)
    .component('edit-profile-page', EditProfilePage)

    .mount('#app');

