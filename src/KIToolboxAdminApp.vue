<script setup>
import { onBeforeMount } from 'vue'
import TheAdminToolTable from './components/admin/TheAdminToolTable.vue';
import { useToolsStore } from './stores/tools';

const toolsStore = useToolsStore();

onBeforeMount(() => {
  toolsStore.fetchTools();
})

</script>

<template>
    <the-admin-tool-table />
</template>
