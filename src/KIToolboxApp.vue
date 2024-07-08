<script setup>
import { computed, ref, onBeforeMount, onBeforeUnmount, onMounted } from 'vue';
import TheToolboxList from './components/course/TheToolboxList.vue';
import { useToolsStore } from './stores/tools';
import { useCourseToolsStore } from './stores/course-tools';
import { useContextStore } from './stores/context';

const contextStore = useContextStore();
const toolsStore = useToolsStore();
const courseToolsStore = useCourseToolsStore();

const studentView = ref(true);
const editView = ref(false);

const courseToolInterval = ref(null);

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
    courseToolInterval.value = setInterval(() => {
        courseToolsStore.fetchCourseToolsByCourse(contextStore.cid);
    }, 14000);

    if (isTeacher) {
        toolsStore.fetchTools();
    }
});

onMounted(() => {
    const listItem = window.document.querySelectorAll("[view-dummy-item]")[0].parentElement.remove();
});

onBeforeUnmount(() => {
    if (courseToolInterval?.value) {
        clearInterval(courseToolInterval.value);
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
