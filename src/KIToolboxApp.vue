<template>
    <TheToolboxList :editMode="editView" />
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
<script setup>
import TheToolboxList from './components/course/TheToolboxList.vue';
import { ref } from 'vue';

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
</script>
