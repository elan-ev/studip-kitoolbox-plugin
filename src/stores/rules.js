import { ref, computed } from 'vue';
import { defineStore } from 'pinia';
import { api } from '../api.js';
import { useContextStore } from './context';

export const useRulesStore = defineStore('kitoolbox-rules', () => {
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

    const hasRule = computed(() => {
        return all.value.length > 0;
    });

    function byId(id) {
        return records.value.get(id);
    }

    async function fetchById(id) {
        isLoading.value = true;
        try {
            const { data } = await api.fetch(`kitoolbox-rules/${id}`, {
                params: {},
            });
            storeRecord(data);
        } catch (err) {
            console.error('fetching kitoolbox-rules', err);
            errors.value = err;
        }
        isLoading.value = false;
    }

    async function fetchRuleByCourse(cid) {
        isLoading.value = true;
        return api
            .get(`courses/${cid}/kitoolbox-rules`, {}
            )
            .then(({ data }) => {
                storeRecord(data);
                isLoading.value = false;
            })
            .catch((err) => {
                if (err.response.status !== 404) {
                    console.error('fetching kitoolbox-rules', err);
                    errors.value = err;
                }
            })
            .finally(() => {
                isLoading.value = false;
            });
    }

    async function createRule(rule) {
        const contextStore = useContextStore(); 
        const newRule = {
            'content': rule.content,
            'course-id': contextStore.cid,
        };
        const { data } = await api.create('kitoolbox-rules', newRule);
        storeRecord(data);

        return data;
    }

    async function updateRule(rule) {
        return api.patch('kitoolbox-rules', { id: rule.id, ...rule }).then(() => {
            fetchById(rule.id);
        });
    }

    async function toggleReleasedState(rule) {
        rule.released = !rule.released;

        return updateRule(rule);
    }

    async function deleteRule(rule) {
        return api.delete('kitoolbox-rules', rule.id).then(() => records.value.delete(rule.id));
    }

    return {
        all,
        hasRule,
        byId,
        fetchById,
        clearRecords,
        createRule,
        updateRule,
        deleteRule,
        fetchRuleByCourse,
        toggleReleasedState,
    };
});
