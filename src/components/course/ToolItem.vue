<script setup>
import { computed, ref } from 'vue';
import { useCourseToolsStore } from './../../stores/course-tools';
import StudipIcon from '../studip/StudipIcon.vue';
import StudipDialog from '../studip/StudipDialog.vue';

const courseToolsStore = useCourseToolsStore();

const props = defineProps({
    tool: Object,
    editMode: Boolean,
});

const openEditDialog = ref(false);
const toolClone = ref(null);

const editModeHighlight = computed(() => {
    if (props.editMode) {
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

const showJWTLink = computed(() => {
    return !props.editMode && Boolean(props.tool?.jwt);
});
</script>

<template>
    <component :is="showJWTLink ? 'a' : 'div'" :href="showJWTLink ? tool.jwt : null" :target="showJWTLink ? '_blank' : null ">
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
                <button v-if="editMode" @click="showEditTool" :title="$gettext('Einstellungen')"><StudipIcon shape="admin" /></button>
            </header>
            <div class="kit-tool-item-body">
                <img :src="tool.preview" aria-hidden="true"/>
                <p>{{ tool.description }}</p>
            </div>
        </article>
    </component>
    <StudipDialog
        :height="300"
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
                    <input type="number" min="0" v-model="toolClone['max-tokens']" />
                </label>
                <label>
                    {{ $gettext('Tokens pro Nutzendem') }}
                    <input type="number" min="0" v-model="toolClone['tokens-per-user']" />
                </label>
            </form>
        </template>
    </StudipDialog>
</template>

<style lang="scss">
.kit-tool-item {
    max-width: 870px;
    position: relative;

    &.active-item,
    &.inactive-item {
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
    }

    .kit-tool-item-body {
        display: flex;
        flex-direction: row;
        gap: 10px 40px;

        img {
            width: 270px;
        }
        p {
            max-width: 540px;
        }
    }
}
</style>
