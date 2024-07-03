import { ref, computed } from 'vue';
import { defineStore } from 'pinia';
import { api } from '../api.js';

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
            .fetch(`courses/${cid}/kitoolbox-course-tools`, {
                params: {
                    'page[limit]': 100,
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

    async function createCourseTool(tool) {
        const { data } = await api.create('kitoolbox-course-tools', tool);
        storeRecord(data);

        return data;
    }

    async function updateCourseTool(tool) {
        return api.patch('kitoolbox-course-tools', { id: tool.id, ...tool }).then(() => {
            fetchById(tool.id);
        });
    }

    async function toggleActiveState(tool) {
        tool.active = !tool.active;
        return updateCourseTool(tool);
    }

    async function deleteCourseTool(tool) {
        return api.delete('kitoolbox-course-tools', tool.id).then(() => records.value.delete(tool.id));
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
