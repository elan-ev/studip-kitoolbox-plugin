<script setup>
import ToolItem from './ToolItem.vue';
import { useCourseToolsStore } from './../../stores/course-tools';

const props = defineProps({
    editMode: Boolean,
});
</script>

<template>
    <h1>toolbox list</h1>
    <h2 v-if="editMode">EDIT MODE</h2>
    <ul>
        <li v-for="tool in tools" :key="tool"><ToolItem :tool="tool" :editMode="editMode" /></li>
    </ul>
</template>
