import { ref, computed } from 'vue';
import { defineStore } from 'pinia';
import { api } from '../api.js';

export const useToolsStore = defineStore('kitoolbox-tools', () => {
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
            const { data } = await api.fetch(`kitoolbox-tools/${id}`, {
                params: {},
            });
            storeRecord(data);
        } catch (err) {
            console.error('fetching kitoolbox-tools', err);
            errors.value = err;
        }
        isLoading.value = false;
    }

    async function fetchTools() {
        isLoading.value = true;
        return api
            .fetch('kitoolbox-tools', {
                params: {
                    'page[limit]': 100,
                },
            })
            .then(({ data }) => {
                data.forEach(storeRecord);
                isLoading.value = false;
            })
            .catch((err) => {
                console.error('fetching kitoolbox-tools', err);
                errors.value = err;
            })
            .finally(() => {
                isLoading.value = false;
            });
    }

    async function createTool(tool) {
        const { data } = await api.create('kitoolbox-tools', tool);
        storeRecord(data);

        return data;
    }

    async function updateTool(tool) {
        return api.patch('kitoolbox-tools', { id: tool.id, ...tool }).then(() => {
            fetchById(tool.id);
        });
    }

    async function toggleActiveState(tool) {
        tool.active = !tool.active;
        return updateTool(tool);
    }

    async function deleteTool(tool) {
        return api.delete('kitoolbox-tools', tool.id).then(() => records.value.delete(tool.id));
    }

    return {
        all,
        byId,
        fetchById,
        fetchTools,
        clearRecords,
        createTool,
        updateTool,
        deleteTool,
        toggleActiveState,
    };
});
