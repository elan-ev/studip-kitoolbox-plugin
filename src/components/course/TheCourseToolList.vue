<script setup>
import { computed } from 'vue';
import EmptyTeaser from './EmptyTeaser.vue';
import { useToolsStore } from './../../stores/tools';
import { useCourseToolsStore } from './../../stores/course-tools';
import { useContextStore } from './../../stores/context';
import CourseToolItem from './CourseToolItem.vue';

const contextStore = useContextStore();
const toolsStore = useToolsStore();
const courseToolsStore = useCourseToolsStore();

const courseTools = computed(() => {
    let courseTools = courseToolsStore.all;

    return courseTools.filter((courseTool) => courseTool.active);;
});
const showTeaser = computed(() => {
    return courseTools.value.length === 0;
});
</script>
<template>
    <ul class="kit-tool-list">
        <li v-for="courseTool in courseTools" :key="courseTool.id">
            <CourseToolItem :course-tool="courseTool" />
        </li>
    </ul>
    <EmptyTeaser v-show="showTeaser" />
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