<script setup>
import { computed, ref, onBeforeMount, onBeforeUnmount, onMounted } from 'vue';
import TheToolboxList from './components/course/TheToolboxList.vue';
import StudipProgressIndicator from './components/studip/StudipProgressIndicator.vue';
import StudipDialog from './components/studip/StudipDialog.vue';
import { useToolsStore } from './stores/tools';
import { useCourseToolsStore } from './stores/course-tools';
import { useContextStore } from './stores/context';
import { useRulesStore } from './stores/rules';

const contextStore = useContextStore();
const toolsStore = useToolsStore();
const courseToolsStore = useCourseToolsStore();
const rulesStore = useRulesStore();

const studentView = ref(true);
const editView = ref(false);

const courseToolInterval = ref(null);

const openRulesDialog = ref(false);
const ruleContent = ref(null);

const switchView = (view) => {
    if (view === 'student') {
        studentView.value = true;
        editView.value = false;
    }
    if (view === 'edit') {
        studentView.value = false;
        editView.value = true;
    }
};

const isTeacher = computed(() => {
    return contextStore.isTeacher;
});
const appLoaded = computed(() => {
    return contextStore.appLoaded;
});

const hasRule = computed(() => {
    return rulesStore.hasRule;
});

const rule = computed(() => {
    return hasRule.value ? rulesStore.all[0] : null;
});

const updateShowRulesDialog = (state) => {
    openRulesDialog.value = state;
};

const storeRules = () => {
    updateShowRulesDialog(false);
    if (hasRule.value) {
        const updatedRule = {
            id: rule.value.id,
            'course-id': rule.value['course-id'],
            content: ruleContent.value,
            released: false
        }
        rulesStore.updateRule(updatedRule);
    } else {
        const rule = {
            content: ruleContent.value
        };
        rulesStore.createRule(rule);
    }
};


onBeforeMount(async () => {
    await courseToolsStore.fetchCourseToolsByCourse(contextStore.cid);
    if (isTeacher) {
        await toolsStore.fetchTools();
        await rulesStore.fetchRuleByCourse(contextStore.cid);
        if (!hasRule.value) {
            updateShowRulesDialog(true);
        } else {
            ruleContent.value = rule.value.content;
            if (ruleContent.value === '') {
                updateShowRulesDialog(true);
            }
        }
    }
    contextStore.setAppLoaded(true);

    courseToolInterval.value = setInterval(() => {
        courseToolsStore.fetchCourseToolsByCourse(contextStore.cid);
    }, 14000);
});

onMounted(() => {
    const listItem = window.document.querySelectorAll('[view-dummy-item]')[0];
    if (listItem) {
        listItem.parentElement.remove();
    }
});

onBeforeUnmount(() => {
    if (courseToolInterval?.value) {
        clearInterval(courseToolInterval.value);
    }
});
</script>
<template>
    <template v-if="appLoaded">
        <TheToolboxList
            :editMode="editView"
            @switch-mode-edit="switchView('edit')"
            @show-rules-dialog="updateShowRulesDialog(true)"
        />
        <StudipDialog
            v-if="isTeacher"
            :height="640"
            :width="900"
            :open="openRulesDialog"
            confirm-class="accept"
            :confirm-text="$gettext('Speichern')"
            :close-text="hasRule ? $gettext('Abbrechen') : $gettext('Erst einmal umschauen')"
            :title="$gettext('Rules for Tools')"
            @update:open="updateShowRulesDialog"
            @confirm="storeRules"
        >
            <template #dialogContent>
                <div class="kit-rule-edit-wrapper">
                    <div></div>
                    <div>
                        <form class="default">
                            <label>
                                {{ $gettext('Rules for Tools') }}
                                <textarea v-model="ruleContent"></textarea>
                            </label>
                        </form>
                    </div>
                </div>
            </template>
        </StudipDialog>
        <Teleport to="#ki-toolbox-view-widget .sidebar-views">
            <li :class="{ active: studentView }">
                <button type="button" @click="switchView('student')">
                    {{ $gettext('Studierendenansicht') }}
                </button>
            </li>
            <li :class="{ active: editView }">
                <button type="button" @click="switchView('edit')">
                    {{ $gettext('Bearbeitungsansicht') }}
                </button>
            </li>
        </Teleport>
    </template>
    <template v-else>
        <StudipProgressIndicator :description="$gettext('Lade Tools...')" />
    </template>
</template>
