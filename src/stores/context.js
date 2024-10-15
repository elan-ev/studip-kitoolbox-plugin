import { ref, computed } from 'vue';
import { defineStore } from 'pinia';
import { api } from '../api.js';

export const useContextStore = defineStore('kitoolbox-context', () => {
    const isTeacher = ref(false);
    const preferredLanguage = ref('de_DE');
    const appLoaded = ref(false);
    const exploreMode = ref(false);

    const cid = computed(() => {
        return window.STUDIP.URLHelper.parameters.cid;
    });

    const userId = computed(() => {
        return window.STUDIP.USER_ID;
    });

    function setAppLoaded(val) {
        appLoaded.value = val;
    }

    function setTeacherStatus(val) {
        isTeacher.value = val;
    }

    function setPreferredLanguage(val) {
        preferredLanguage.value = val;
    }

    function setExploreMode(val) {
        exploreMode.value = val;
    }

    return {
        exploreMode,
        isTeacher,
        preferredLanguage,
        cid,
        userId,
        appLoaded,
        setAppLoaded,
        setExploreMode,
        setTeacherStatus,
        setPreferredLanguage,
    };
});
