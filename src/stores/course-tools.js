import { ref, computed } from 'vue';
import { defineStore } from 'pinia';
import { api } from '../api.js';
import { useContextStore } from './context';

export const useCourseToolsStore = defineStore('kitoolbox-course-tools', () => {
    const records = ref(new Map());
    const isLoading = ref(false);
    const errors = ref(false);

    function storeRecord(newRecord) {
        records.value.set(newRecord.id, newRecord);
    }

    function clearRecords() {
        records.value = new Map();
    }

    const all = computed(() => {
        return [...records.value.values()];
    });

    function byId(id) {
        return records.value.get(id);
    }

    async function fetchById(id) {
        isLoading.value = true;
        try {
            const { data } = await api.fetch(`kitoolbox-course-tools/${id}`, {
                params: {},
            });
            storeRecord(data);
        } catch (err) {
            console.error('fetching kitoolbox-course-tools', err);
            errors.value = err;
        }
        isLoading.value = false;
    }

    async function fetchCourseToolsByCourse(cid) {
        isLoading.value = true;
        return api
            .get(`courses/${cid}/kitoolbox-course-tools`, {
                params: {
                    'page[limit]': 1000,
                    'include': 'quotas,tool'
                },
            })
            .then(({ data }) => {
                data.forEach(storeRecord);
                isLoading.value = false;
            })
            .catch((err) => {
                console.error('fetching kitoolbox-course-tools', err);
                errors.value = err;
            })
            .finally(() => {
                isLoading.value = false;
            });
    }

    async function createCourseTool(courseTool) {
        const { data } = await api.create('kitoolbox-course-tools', courseTool);
        storeRecord(data);

        return data;
    }

    async function updateCourseTool(courseTool) {
        return api.patch('kitoolbox-course-tools', { id: courseTool.id, ...courseTool }).then(() => {
            fetchById(courseTool.id);
        });
    }

    async function toggleActiveState(tool) {
        let courseTool = tool['course-tool'];
        if (Boolean(courseTool)) {
            courseTool.active = !courseTool.active;
            return updateCourseTool(courseTool);
        } else {
            const contextStore = useContextStore();
            courseTool = {
                'tool-id': tool['id'],
                'course-id': contextStore.cid,
            };

            return createCourseTool(courseTool);
        }
    }

    async function deleteCourseTool(courseTool) {
        return api.delete('kitoolbox-course-tools', courseTool.id).then(() => records.value.delete(courseTool.id));
    }

    return {
        all,
        byId,
        fetchById,
        fetchCourseToolsByCourse,
        clearRecords,
        createCourseTool,
        updateCourseTool,
        deleteCourseTool,
        toggleActiveState,
    };
});
