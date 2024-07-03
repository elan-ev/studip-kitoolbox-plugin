<script setup>
import { computed } from 'vue';
import ToolItem from './ToolItem.vue';
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
            return course_tool['tool-id'] == tool.id // yes '==' the one is an int the other a string
        });
        tool['active-in-course'] = Boolean(courseTool && courseTool?.active);

        return tool;
   });
});

const courseTools = computed(() => {
    let tools = courseToolsStore.all;
    if (!isTeacher) {
        return tools.filter((tool) => tool.active);
    }
    return tools;
});

const isTeacher = computed(() => {
    return contextStore.isTeacher;
});
</script>

<template>
    <h1>toolbox list</h1>
    <h2 v-if="editMode">EDIT MODE</h2>
    <ul v-if="editMode">
        <li v-for="tool in tools" :key="tool"><ToolItem :tool="tool" :editMode="editMode" /></li>
    </ul>
    <ul v-else>
        <li v-for="tool in courseTools" :key="tool"><ToolItem :tool="tool" :editMode="editMode" /></li>
    </ul>
</template>
