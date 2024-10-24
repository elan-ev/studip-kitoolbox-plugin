<script setup>
import { computed } from 'vue';
import { useContextStore } from '../../stores/context';
import TheTeaserBox from './TheTeaserBox.vue';
import TheSettingsInfoBox from './TheSettingsInfoBox.vue';
const contextStore = useContextStore();

const isTeacher = computed(() => {
    return contextStore.isTeacher;
});
</script>
<template>
    <div id="kit-landing">
        <TheTeaserBox />
        <TheSettingsInfoBox v-if="isTeacher" />
    </div>
</template>
<style lang="scss">
#kit-landing {
    max-width: 900px;
    margin: 0 auto;
}
</style>
