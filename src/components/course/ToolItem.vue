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
    editMode: Boolean,
});

const openEditDialog = ref(false);
const toolClone = ref(null);

const editModeHighlight = computed(() => {
    if (props.editMode) {
        if (toolTokenLimitReached.value) {
            return 'warning-item';
        }
        return props.tool['active-in-course'] ? 'active-item' : 'inactive-item';
    }
});

const toggleActiveState = (tool) => {
    courseToolsStore.toggleActiveState(tool);
};

const updateShowEditDialog = (state) => {
    openEditDialog.value = state;
};

const showEditTool = () => {
    toolClone.value = JSON.parse(JSON.stringify(props.tool['course-tool']));
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

const showRedirectLink = computed(() => {
    return !props.editMode && Boolean(props.tool?.redirect);
});

const quotas = computed(() => {
    return props.tool.quotas?.data;
});

const userQuotas = computed(() => {
    return quotas.value.filter((quota) => quota.user.data.id === contextStore.userId);
});

const toolTokenLimit = computed(() => {
    if (props.tool['max-tokens-unlimited'] || quotas === undefined) {
        return null;
    }
    return props.tool['max-tokens'] - quotas.value.length;
});

const userTokenLimit = computed(() => {
    if (props.tool['tokens-per-user-unlimited']) {
        return null;
    }

    return props.tool['tokens-per-user'] - userQuotas.value.length;
});

const toolTokenLimitReached = computed(() => {
    const tool = props.tool.tool?.data ?? props.tool;

    return tool['max-quota'] <= tool['used-tokens'];
});

const itemAvailable = computed(() => {
    if (contextStore.isTeacher) {
        return true;
    }

    return userTokenLimit.value !== 0 && toolTokenLimit.value !== 0 && !toolTokenLimitReached.value;
});

const preview = computed(() => {
    return (
        props.tool.preview ||
        STUDIP.URLHelper.getURL('plugins_packages/elan-ev/KIToolbox/assets/images/kitoolbox-preview-default.svg')
    );
});
</script>

<template>
    <article class="kit-tool-item" :class="editModeHighlight">
        <header class="kit-tool-item-head">
            <input
                v-if="editMode"
                type="checkbox"
                :checked="tool['active-in-course']"
                :title="tool['active-in-course'] ? $gettext('KI-Tool deaktivieren') : $gettext('KI-Tool aktivieren')"
                @change="toggleActiveState(tool)"
            />
            <h2>{{ tool.name }}</h2>
            <button v-if="editMode" @click="showEditTool" :title="$gettext('Einstellungen')">
                <StudipIcon shape="admin" />
            </button>
            <div v-if="!editMode && !toolTokenLimitReached" class="kit-token-info">
                <span v-if="toolTokenLimit !== null" :class="{ 'kit-token-warning': toolTokenLimit <= 0 }">
                    {{ $gettext('Übrige Tokens') + ': ' + toolTokenLimit }}
                </span>
                <span
                    v-if="!contextStore.isTeacher && userTokenLimit !== null"
                    :class="{ 'kit-token-warning': userTokenLimit <= 0 }"
                >
                    <span v-if="toolTokenLimit !== null" class="seperator"> | </span>
                    {{ $gettext('Ihre restlichen Tokens') + ': ' + userTokenLimit }}
                </span>
            </div>
            <div v-if="toolTokenLimitReached" :class="{ 'kit-token-info': !editMode }">
                <span class="kit-token-warning">{{ $gettext('Die Tokens für dieses Tool sind verbraucht!') }}</span>
            </div>
        </header>
        <div class="kit-tool-item-body">
            <img :src="preview" aria-hidden="true" />
            <p>{{ tool.description }}</p>
        </div>
        <footer class="kit-tool-item-footer">
            <a v-if="showRedirectLink && itemAvailable" :href="tool.redirect" target="_blank" class="button">
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
    max-width: 870px;
    position: relative;
    margin-bottom: 4em;

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
