import { createApp } from 'vue'
import App from './KIToolboxApp.vue'
import { createGettext } from 'vue3-gettext';
import translations from './locales/translations.json';
import { createPinia } from 'pinia';
import { useContextStore } from './stores/context';
import CKEditor from '@ckeditor/ckeditor5-vue';
import { loadWysiwyg } from './wysiwyg.js';

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

const elem = document.getElementById('kitoolbox-app');
const contextStore = useContextStore();
if (elem?.attributes?.['is-teacher'] !== undefined) {
  const isTeacher = JSON.parse(elem.attributes['is-teacher'].value);
  contextStore.setTeacherStatus(isTeacher);
}
if (elem?.attributes?.['preferred-language'] !== undefined) {
  const preferredLanguage = elem.attributes['preferred-language'].value;
  contextStore.setPreferredLanguage(preferredLanguage);
}
if (elem?.attributes?.['static-texts'] !== undefined) {
  const staticTexts = elem.attributes['static-texts'].value;
  // console.log(staticTexts);
  // console.log(JSON.parse(staticTexts));
  contextStore.setStaticTexts(JSON.parse(staticTexts));
}

app.use(CKEditor);

loadWysiwyg()
    .then((wysiwyg) => app.use(wysiwyg))
    .then(() => app.mount('#kitoolbox-app'));





