import { ref, computed } from 'vue';
import { defineStore } from 'pinia';
import { api } from '../api.js';

export const useContextStore = defineStore('kitoolbox-context', () => {
    const isTeacher = ref(false);
    const appLoaded = ref(false);

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

    return {
        isTeacher,
        cid,
        userId,
        appLoaded,
        setAppLoaded,
        setTeacherStatus,
    };
});
