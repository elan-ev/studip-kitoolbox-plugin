<script setup>
import { computed, ref } from 'vue';
import { useCourseToolsStore } from './../../stores/course-tools';
import { useContextStore } from './../../stores/context';
import StudipIcon from '../studip/StudipIcon.vue';
import StudipDialog from '../studip/StudipDialog.vue';

const courseToolsStore = useCourseToolsStore();
const contextStore = useContextStore();

const props = defineProps({
    tool: Object,
    settingsEnabled: {
        type: Boolean,
        default: false
    }
});

const openEditDialog = ref(false);
const toolClone = ref(null);

const indicatorClass = computed(() => {
    if (toolTokenLimitReached.value) {
        return 'warning-item';
    }
    return props.tool['active-in-course'] ? 'active-item' : 'inactive-item';
});
const toolTokenLimitReached = computed(() => {
    const tool = props.tool.tool?.data ?? props.tool;

    if (tool['max-quota'] === -1) {
        return false;
    }

    return tool['max-quota'] <= tool['used-tokens'];
});

const toggleActiveState = (tool) => {
    courseToolsStore.toggleActiveState(tool);
};
const updateShowEditDialog = (state) => {
    openEditDialog.value = state;
};
const showEditTool = () => {
    toolClone.value = JSON.parse(JSON.stringify(props.tool?.['course-tool']));
    updateShowEditDialog(true);
};

const storeTool = () => {
    updateShowEditDialog(false);
    courseToolsStore.updateCourseTool(toolClone.value);
};

const setUnlimited = (field) => {
    if (field === 'max-tokens') {
        toolClone.value['max-tokens'] = maxTokensUnlimited.value ? 0 : -1;
    }
    if (field === 'tokens-per-user') {
        toolClone.value['tokens-per-user'] = tokensPerUserUnlimited.value ? 0 : -1;
    }
};

const maxTokensUnlimited = computed(() => {
    return toolClone.value['max-tokens'] === -1;
});

const tokensPerUserUnlimited = computed(() => {
    return toolClone.value['tokens-per-user'] === -1;
});
const preview = computed(() => {
    return (
        props.tool.preview || props.tool.metadata?.image_url	||
        STUDIP.URLHelper.getURL('plugins_packages/elan-ev/KIToolbox/assets/images/kitoolbox-preview-default.svg')
    );
});

const name = computed(() => {
    return props.tool.name || props.tool.metadata?.title[contextStore.preferredLanguage] || '---';
});

const description = computed(() => {
    return props.tool.description || props.tool.metadata?.description[contextStore.preferredLanguage] || '---';
});

</script>

<template>
    <article class="kit-tool-item" :class="indicatorClass">
        <header class="kit-tool-item-head">
            <input
                type="checkbox"
                :checked="tool['active-in-course']"
                :title="tool['active-in-course'] ? $gettext('KI-Tool deaktivieren') : $gettext('KI-Tool aktivieren')"
                @change="toggleActiveState(tool)"
            />
            <h2>{{ name }}</h2>
            <button v-if="settingsEnabled" @click="showEditTool" :title="$gettext('Einstellungen')">
                <StudipIcon shape="admin" />
            </button>
        </header>
        <div class="kit-tool-item-body">
            <img :src="preview" aria-hidden="true" />
            <p v-html="description" class="formatted-content ck-content"></p>
        </div>
        <footer class="kit-tool-item-footer">
            <a :href="tool.redirect" target="_blank" class="button">
                {{ $gettext('Tool starten') }}
            </a>
        </footer>
    </article>
    <StudipDialog
        :height="350"
        :open="openEditDialog"
        confirm-class="accept"
        :confirm-text="$gettext('Speichern')"
        :close-text="$gettext('Abbrechen')"
        :title="$gettext('Einstellungen')"
        @update:open="updateShowEditDialog"
        @confirm="storeTool"
    >
        <template #dialogContent>
            <form class="default">
                <label>
                    {{ $gettext('Maximale Anzahl Tokens') }}
                    <input
                        type="number"
                        min="0"
                        :max="props.tool['max-quota']"
                        v-model="toolClone['max-tokens']"
                        :disabled="maxTokensUnlimited"
                    />
                    <span>{{ $gettext('unbegrenzt') }}</span>
                    <input type="checkbox" @click="setUnlimited('max-tokens')" :checked="maxTokensUnlimited" />
                </label>
                <label>
                    {{ $gettext('Tokens pro Nutzendem') }}
                    <input
                        type="number"
                        min="0"
                        :max="toolClone['max-tokens']"
                        v-model="toolClone['tokens-per-user']"
                        :disabled="tokensPerUserUnlimited"
                    />
                    <span>{{ $gettext('unbegrenzt') }}</span>
                    <input type="checkbox" @click="setUnlimited('tokens-per-user')" :checked="tokensPerUserUnlimited" />
                </label>
            </form>
        </template>
    </StudipDialog>
</template>

<style lang="scss">
.kit-tool-item {
    position: relative;
    margin-bottom: 15px;

    &.active-item,
    &.inactive-item,
    &.warning-item {
        padding: 0 10px 2px 10px;
        border-left-width: 6px;
        border-left-style: solid;
    }

    &.active-item {
        border-left-color: var(--green);
    }
    &.inactive-item {
        border-left-color: var(--dark-gray-color-60);
    }
    &.warning-item {
        border-left-color: var(--active-color);
    }

    .kit-tool-item-head {
        position: relative;
        margin-bottom: 16px;
        padding-bottom: 4px;
        border-bottom: solid thin var(--dark-gray-color-20);

        input {
            margin-right: 10px;
        }
        h2 {
            display: inline-block;
            margin-top: 0;
            margin-bottom: 0;
            color: unset;
        }
        button {
            position: absolute;
            right: 0;
            border: none;
            background-color: transparent;
            cursor: pointer;
        }
        .kit-token-info {
            position: absolute;
            right: 4px;
            top: 0;
            .kit-token-warning {
                color: var(--active-color);
            }

            .seperator {
                color: var(--black);
            }
        }
    }
    .kit-token-warning {
        font-weight: 700;
    }
    .kit-tool-item-body {
        display: flex;
        flex-wrap: wrap;
        flex-direction: row;
        gap: 10px 40px;

        img {
            width: 270px;
            height: 180px;
        }
        p {
            max-width: 540px;
        }
    }
    .kit-tool-item-footer {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
    }
}
</style>
