import { createApp } from 'vue'
import App from './KIToolboxAdminApp.vue'
import { createGettext } from 'vue3-gettext';
import translations from './locales/translations.json';
import { createPinia } from 'pinia';

const app = createApp(App);

const gettext = createGettext({
    availableLanguages: {
      en: "English",
      de: "Deutsch",
    },
    defaultLanguage: "de",
    translations: translations,
});
app.use(gettext);

const pinia = createPinia();
app.use(pinia);

app.mount('#kitoolbox-admin-app');