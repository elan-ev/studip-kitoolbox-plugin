<script setup>
import { computed, onMounted } from 'vue';
import { useCourseToolsStore } from './../../stores/course-tools';

const courseToolsStore = useCourseToolsStore();

const props = defineProps({
    tool: Object,
    editMode: Boolean,
});

const editModeHighlight = computed(() => {
    if (props.editMode) {
        return props.tool['active-in-course'] ? 'active-item' : 'inactive-item';
    }
});

const toggleActiveState = (tool) => {
    courseToolsStore.toggleActiveState(tool);
}

onMounted(() => {
    // console.log(props.tool);
});
</script>

<template>
    <article class="kit-tool-item" :class="editModeHighlight">
        <header class="kit-tool-item-head">
            <input v-if="editMode" type="checkbox" :checked="tool['active-in-course']" @change="toggleActiveState(tool)" />
            <h2>{{ tool.name }}</h2>
        </header>
        <div class="kit-tool-item-body">
            <img :src="tool.preview" />
            <p>{{ tool.description }}</p>
        </div>
    </article>
</template>

<style lang="scss">
.kit-tool-item {
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
        input {
            margin-right: 10px;
        }
        h2 {
            display: inline-block;
            margin-top: 0;
            margin-bottom: 16px;
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
