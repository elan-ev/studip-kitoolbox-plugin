<script setup>
import { computed, ref, onBeforeMount } from 'vue';
import TheToolboxList from './components/course/TheToolboxList.vue';
import { useToolsStore } from './stores/tools';
import { useCourseToolsStore } from './stores/course-tools';
import { useContextStore } from './stores/context';

const contextStore = useContextStore();
const toolsStore = useToolsStore();
const courseToolsStore = useCourseToolsStore();

const studentView = ref(true);
const editView = ref(false);

const switchView = (view) => {
    if (view === 'student') {
        studentView.value = true;
        editView.value = false;
    }
    if (view === 'edit') {
        studentView.value = false;
        editView.value = true;
    }
};

const isTeacher = computed(() => {
    return contextStore.isTeacher;
});

onBeforeMount(async () => {
    await contextStore.getTeacherStatus();
    courseToolsStore.fetchCourseToolsByCourse(contextStore.cid);
    if (isTeacher) {
        toolsStore.fetchTools();
    }
});
</script>
<template>
    <TheToolboxList :editMode="editView" @switch-mode-edit="switchView('edit')"/>
    <Teleport to="#ki-toolbox-view-widget .sidebar-views">
        <li :class="{ active: studentView }">
            <button type="button" @click="switchView('student')">
                {{ $gettext('Studierendenansicht') }}
            </button>
        </li>
        <li :class="{ active: editView }">
            <button type="button" @click="switchView('edit')">
                {{ $gettext('Bearbeitungsansicht') }}
            </button>
        </li>
    </Teleport>
</template>
