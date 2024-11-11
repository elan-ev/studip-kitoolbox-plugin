import { ref, computed } from 'vue';
import { defineStore } from 'pinia';

export const useContextStore = defineStore('kitoolbox-context', () => {
    const isTeacher = ref(false);
    const preferredLanguage = ref('de_DE');
    const appLoaded = ref(false);
    const exploreMode = ref(false);
    const showRuleDialog = ref(false);
    const staticTexts = ref('');

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

    function setStaticTexts(val) {
        staticTexts.value = val;
    }

    function setExploreMode(val) {
        exploreMode.value = val;
    }

    function setShowRuleDialog(val) {
        showRuleDialog.value = val;
    }

    return {
        showRuleDialog,
        exploreMode,
        isTeacher,
        preferredLanguage,
        staticTexts,
        cid,
        userId,
        appLoaded,
        setAppLoaded,
        setExploreMode,
        setShowRuleDialog,
        setTeacherStatus,
        setPreferredLanguage,
        setStaticTexts
    };
});
