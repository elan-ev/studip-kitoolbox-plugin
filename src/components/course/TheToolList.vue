<script setup>
import { computed } from 'vue';
import ToolItem from './ToolItem.vue';
import { useToolsStore } from './../../stores/tools';
import { useCourseToolsStore } from './../../stores/course-tools';
import { useContextStore } from './../../stores/context';

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
        tool.redirect = courseTool ? courseTool.redirect : tool.url;
        return tool;
    });
});

const courseTools = computed(() => {
    let courseTools = courseToolsStore.all;

    return courseTools;
});
</script>

<template>
    <ul class="kit-tool-list">
        <li v-for="tool in tools" :key="tool.id">
            <ToolItem :tool="tool" />
        </li>
    </ul>
</template>
<style lang="scss">
.kit-tool-list {
    list-style: none;
    padding: 0;
    li {
        margin-bottom: 30px;
    }
}
</style>