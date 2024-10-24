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
import TheInfoBox from './components/course/TheInfoBox.vue';
import StudipWysiwyg from './components/studip/StudipWysiwyg.vue';

import { ruleTemplate } from './static-strings.js';
import TheLadingpage from './components/course/TheLadingpage.vue';

const contextStore = useContextStore();
const toolsStore = useToolsStore();
const courseToolsStore = useCourseToolsStore();
const rulesStore = useRulesStore();

const courseToolInterval = ref(null);

const ruleContent = ref(null);
const ruleReleased = ref(false);

const isTeacher = computed(() => {
    return contextStore.isTeacher;
});
const appLoaded = computed(() => {
    return contextStore.appLoaded;
});
const exploreMode = computed(() => {
    return contextStore.exploreMode;
});
const showRuleDialog = computed(() => {
    return contextStore.showRuleDialog;
});

const hasRule = computed(() => {
    return rulesStore.hasRule;
});

const rule = computed(() => {
    return hasRule.value ? rulesStore.all[0] : null;
});

const showLandingpage = computed(() => {
    if (isTeacher.value) {
        return !hasRule.value && !exploreMode.value;
    }
    return !rule.value.released;
});

const storeRules = () => {
    contextStore.setShowRuleDialog(false);
    if (hasRule.value) {
        const updatedRule = {
            id: rule.value.id,
            'course-id': rule.value['course-id'],
            content: ruleContent.value,
            released: ruleReleased.value,
        };
        rulesStore.updateRule(updatedRule);
    } else {
        const rule = {
            content: ruleContent.value,
            released: ruleReleased.value,
        };
        rulesStore.createRule(rule);
    }

    contextStore.setExploreMode(false);
};

const releaseRule = () => {
    const updatedRule = {
        id: rule.value.id,
        'course-id': rule.value['course-id'],
        content: ruleContent.value,
        released: true,
    };
    rulesStore.updateRule(updatedRule);
    ruleReleased.value = true;
    contextStore.setExploreMode(false);
};

const insertRuleTemplate = () => {
    ruleContent.value = ruleTemplate;
};

onBeforeMount(async () => {
    await courseToolsStore.fetchCourseToolsByCourse(contextStore.cid);
    await rulesStore.fetchRuleByCourse(contextStore.cid);

    if (isTeacher) {
        await toolsStore.fetchTools();
        if (!hasRule.value) {
            ruleContent.value = '';
        } else {
            ruleReleased.value = rule.value.released;
            ruleContent.value = rule.value.content;
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
        <div id="kit-app-wrapper">
            <TheLadingpage v-if="showLandingpage" />
            <template v-else>
                <div id="kit-info-col">
                    <TheInfoBox />
                    <TheRuleBox
                        v-if="hasRule"
                        @edit-rule="contextStore.setShowRuleDialog(true)"
                        @release="releaseRule()"
                    />
                    <button v-if="!hasRule && isTeacher" class="button" @click="contextStore.setShowRuleDialog(true)">
                        {{ $gettext('Rules for Tools anlegen') }}
                    </button>
                </div>
                <div id="kit-content-col">
                    <template v-if="isTeacher">
                        <TheToolList />
                    </template>
                    <template v-else>
                        <TheCourseToolList />
                    </template>
                </div>
            </template>
        </div>
        <StudipDialog
            v-if="isTeacher"
            :height="640"
            :width="880"
            :open="showRuleDialog"
            confirm-class="accept"
            :confirm-text="$gettext('Speichern')"
            :close-text="$gettext('Abbrechen')"
            :title="$gettext('Rules for Tools: Verbindliche Hinweise zur Nutzung von KI-Tools')"
            @update:open="contextStore.setShowRuleDialog"
            @confirm="storeRules"
        >
            <template #dialogContent>
                <div class="kit-rule-edit-wrapper">
                    <div class="kit-rule-edit-info">
                        <StudipIcon shape="literature2" :size="96" class="kit-rule-edit-info-icon" />
                        <div class="kit-rule-edit-info-text">
                            <p>
                                Stellen Sie Regeln für die Nutzung von KI-Tools in Ihrer Lehrveranstaltung auf (warum
                                das wichtig ist, erfahren Sie hier). Bei der Formulierung helfen Ihnen die folgenden
                                Fragen:
                            </p>
                            <ul>
                                <li>
                                    Möchten Sie KI-Tools in Ihrer Veranstaltung zulassen? Wenn ja, welche und unter
                                    welchen Umständen? Wenn nein, warum nicht?
                                </li>
                                <li>Wozu möchten Sie Studierende verpflichten, wenn sie KI-Tools nutzen?</li>
                            </ul>
                        </div>
                    </div>
                    <div class="kit-rule-edit-form">
                        <form class="default">
                            <label for="rule-content">
                                {{ $gettext('Rules for Tools') }}
                            </label>
                            <StudipWysiwyg id="rule-content" v-model="ruleContent"></StudipWysiwyg>
                            <button v-if="ruleContent === ''" class="button" @click.prevent="insertRuleTemplate">
                                {{ $gettext('Vorlage einfügen') }}
                            </button>
                            <br />

                            <label for="rule-released">
                                <input type="checkbox" id="rule-released" v-model="ruleReleased" />
                                {{ $gettext('Sichtbar für Studierende') }}
                            </label>
                        </form>
                    </div>
                </div>
            </template>
        </StudipDialog>
    </template>
    <template v-else>
        <StudipProgressIndicator :description="$gettext('Lade Tools...')" />
    </template>
</template>

<style lang="scss">
#kit-app-wrapper {
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    gap: 15px 50px;
    max-width: 1530px;
    margin: 20px auto 0 auto;

    #kit-info-col {
        max-width: 600px;
        flex-grow: 1;
    }

    #kit-content-col {
        width: 100%;
        max-width: 860px;
    }
}
.kit-rule-edit-wrapper {
    display: flex;
    flex-direction: row;
    flex-wrap: nowrap;
    gap: 20px;
    height: 100%;
    justify-content: space-between;

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
    }
}

html:not(.size-large) {
    #kit-app-wrapper #kit-info-col {
        max-width: unset;
    }
    .kit-tool-item .kit-tool-item-body p {
        max-width: unset;
    }
}
html.responsive-display {
    #kit-app-wrapper {
        margin: 0;
    }

    .kit-rule-edit-wrapper {
        flex-direction: column;

        .kit-rule-edit-info {
            width: unset;
            border-bottom: solid thin var(--light-gray-color-20);
            border-right: none;
        }
        .kit-rule-edit-form {
            max-width: unset;
            padding-bottom: 2em;
        }
    }
}
#kitoolbox-index {
    .ck-body-wrapper {
        z-index: 3001;
    }
}
</style>
