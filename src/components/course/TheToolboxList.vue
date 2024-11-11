<script setup>
import { computed } from 'vue';
import ToolItem from './ToolItem.vue';
import EmptyTeaser from './EmptyTeaser.vue';
import { useToolsStore } from './../../stores/tools';
import { useCourseToolsStore } from './../../stores/course-tools';
import { useContextStore } from './../../stores/context';

const props = defineProps({
    editMode: Boolean,
});
const contextStore = useContextStore();
const toolsStore = useToolsStore();
const courseToolsStore = useCourseToolsStore();
const tools = computed(() => {
    let tools = toolsStore.all;
    tools = tools.filter((tool) => tool.active); // filter for root users

    return tools.map((tool) => {
        const courseTool = courseTools.value.find((course_tool) => {
            return course_tool['tool-id'] == tool.id; // yes '==' the one is an int the other a string
        });
        tool['active-in-course'] = Boolean(courseTool && courseTool?.active);
        tool['course-tool'] = courseTool;
        return tool;
    });
});

const courseTools = computed(() => {
    let tools = courseToolsStore.all;
    if (!props.editMode) {
        return tools.filter((tool) => tool.active);
    }
    return tools;
});

const isTeacher = computed(() => {
    return contextStore.isTeacher;
});

const toolList = computed(() => {
    if (props.editMode) {
        return tools.value;
    }

    return courseTools.value;
});

const showTeaser = computed(() => {
    return !props.editMode && courseTools.value.length === 0;
});
</script>

<template>
    <ul class="kit-tool-list">
        <li v-for="tool in toolList" :key="tool.id">
            <ToolItem :tool="tool" :editMode="editMode" />
        </li>
    </ul>
    <EmptyTeaser v-show="showTeaser" @switch-mode-edit="$emit('switch-mode-edit')" @show-rules-dialog="$emit('show-rules-dialog')"/>
</template>

<style lang="scss">
.kit-tool-list {
    list-style: none;
    padding: 0;
    > li {
        margin-bottom: 30px;
    }
}
</style>