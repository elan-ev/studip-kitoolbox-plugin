<script setup>
import { computed, ref, onBeforeMount, onBeforeUnmount, onMounted } from 'vue';
import StudipProgressIndicator from './components/studip/StudipProgressIndicator.vue';
import StudipDialog from './components/studip/StudipDialog.vue';
import StudipIcon from './components/studip/StudipIcon.vue';
import { useToolsStore } from './stores/tools';
import { useCourseToolsStore } from './stores/course-tools';
import { useContextStore } from './stores/context';
import { useRulesStore } from './stores/rules';
import TheToolList from './components/course/TheToolList.vue';
import TheCourseToolList from './components/course/TheCourseToolList.vue';
import TheRuleBox from './components/course/TheRuleBox.vue';
import TheTeaserBox from './components/course/TheTeaserBox.vue';
import StudipWysiwyg from './components/studip/StudipWysiwyg.vue';

const contextStore = useContextStore();
const toolsStore = useToolsStore();
const courseToolsStore = useCourseToolsStore();
const rulesStore = useRulesStore();

const courseToolInterval = ref(null);

const openRulesDialog = ref(false);
const ruleContent = ref(null);
const ruleReleased = ref(false);

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
            released: ruleReleased.value
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
    await rulesStore.fetchRuleByCourse(contextStore.cid);

    if (isTeacher) {
        await toolsStore.fetchTools();
        if (!hasRule.value) {
            updateShowRulesDialog(true);
        } else {
            ruleReleased.value = rule.value.released;
            ruleContent.value = rule.value.content;
            if (ruleContent.value === '' || !ruleReleased.value) {
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
        <TheTeaserBox />
        <TheRuleBox v-if="hasRule" @edit-rule="updateShowRulesDialog(true)"/>
        <template v-if="isTeacher">
            <TheToolList />
            <StudipDialog
                v-if="isTeacher"
                :height="640"
                :width="900"
                :open="openRulesDialog"
                confirm-class="accept"
                :confirm-text="$gettext('Speichern')"
                :close-text="hasRule ? $gettext('Abbrechen') : $gettext('Erst einmal umschauen')"
                :title="$gettext('Rules for Tools: Verbindliche Hinweise zur Nutzung von KI-Tools')"
                @update:open="updateShowRulesDialog"
                @confirm="storeRules"
            >
                <template #dialogContent>
                    <div class="kit-rule-edit-wrapper">
                        <div class="kit-rule-edit-info">
                            <StudipIcon shape="literature2" :size="96" class="kit-rule-edit-info-icon" />
                            <div class="kit-rule-edit-info-text">
                                <p>
                                    Stellen Sie Regeln für die Nutzung von KI-Tools in Ihrer Lehrveranstaltung auf
                                    (warum das wichtig ist, erfahren Sie hier). Bei der Formulierung helfen Ihnen die
                                    folgenden Fragen:
                                </p>
                                <ul>
                                    <li>Möchten Sie KI-Tools in Ihrer Veranstaltung zulassen? Wenn ja, welche und unter welchen Umständen? Wenn nein, warum nicht?</li>
                                    <li>Wozu möchten Sie Studierende verpflichten, wenn sie KI-Tools nutzen?</li>
                                </ul>
                            </div>
                        </div>
                        <div class="kit-rule-edit-form">
                            <form class="default">
                                <label>
                                    {{ $gettext('Rules for Tools') }}
                                    <StudipWysiwyg v-model="ruleContent"></StudipWysiwyg>
                                </label>
                                <label class="col-2">
                                    {{ $gettext('Veröffentlicht') }}
                                    <select v-model="ruleReleased">
                                        <option :value="false">{{ $gettext('Nein') }}</option>
                                        <option :value="true">{{ $gettext('Ja') }}</option>
                                    </select>
                                </label>
                            </form>
                        </div>
                    </div>
                </template>
            </StudipDialog>
        </template>
        <template v-else>
            <TheCourseToolList />
        </template>
    </template>
    <template v-else>
        <StudipProgressIndicator :description="$gettext('Lade Tools...')" />
    </template>
</template>


<style lang="scss">
#kitoolbox-app {
    max-width: 880px;
    // margin: 0 auto;
}
.kit-rule-edit-wrapper {
    display: flex;
    flex-direction: row;
    gap: 20px;
    height: 100%;

    .kit-rule-edit-info {
        width: 270px;
        display: flex;
        flex-direction: column;
        border-right: solid thin var(--light-gray-color-20);

        .kit-rule-edit-info-icon {
            height: 96px;
            margin: 1em auto;
        }
        .kit-rule-edit-info-text {
            flex-grow: 1;
            overflow-y: auto;
            margin: 1em 0 1em 1em;
            padding-right: 1em;

            ul {
                padding-left: 15px;
            }
        }
    }
    .kit-rule-edit-form {
        flex-grow: 1;
        max-width: 540px;

        textarea {
            height: 12em;
            resize: none;
        }
    }
}
</style>