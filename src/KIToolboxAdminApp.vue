<script setup>
import { onBeforeMount, ref } from 'vue'
import TheAdminToolTable from './components/admin/TheAdminToolTable.vue';
import StudipProgressIndicator from './components/studip/StudipProgressIndicator.vue';
import { useToolsStore } from './stores/tools';

const toolsStore = useToolsStore();

const loadingTools = ref(true);

onBeforeMount(async () => {
  await toolsStore.fetchTools();
  loadingTools.value = false;
})

</script>

<template>
    <StudipProgressIndicator v-if="loadingTools" :description="$gettext('Lade Tools...')"/>
    <the-admin-tool-table v-else />
</template>
<style lang="scss">
#kitoolbox-admin {
    .ck-body-wrapper {
        z-index: 3001;
    }
}

</style>