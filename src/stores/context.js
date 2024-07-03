import { ref, computed } from 'vue';
import { defineStore } from 'pinia';
import { api } from '../api.js';

export const useContextStore = defineStore('kitoolbox-context', () => {
    const isTeacher = ref(false);

    const cid = computed(() => {
        return window.STUDIP.URLHelper.parameters.cid;
    });

    const userId = computed(() => {
        return window.STUDIP.USER_ID;
    });

    async function fetchCourseMembership() {
        return api
            .fetch(`users/${userId.value}/course-memberships`)
            .then(({ data }) => {
                return data.length > 0 ? data[0] : null;
            })
            .catch((err) => {
                console.error(err);
            });
    }

    async function fetchCurrentUser() {
        return api
            .fetch(`users/${userId.value}`)
            .then(({ data }) => {
                return data;
            })
            .catch((err) => {
                console.error(err);
            });
    }

    async function getTeacherStatus() {
        const member = await fetchCourseMembership();
        if (member !== null) {
            const perm = member.permission;
            if (perm === 'dozent' || perm === 'tutor') {
                isTeacher.value = true;
                return;
            }
        } else {
            const user = await fetchCurrentUser();
            const perm = user.permission;
            if (perm === 'root') {
                isTeacher.value = true;
                return;
            }
        }
    }

    return {
        isTeacher,
        cid,
        userId,
        getTeacherStatus,
    };
});
