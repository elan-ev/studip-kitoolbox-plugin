<script setup>
import { computed } from 'vue';
import { useContextStore } from '../../stores/context';
const contextStore = useContextStore();

const essentialText = computed(() => {
    return contextStore.staticTexts['essential'];
});
</script>
<template>
    <form class="default kit-info-box collapsable"  @submit.prevent>
        <fieldset>
            <legend>{{ $gettext('Grundsätzliches') }}</legend>
            <article class="kit-info-content">
                <div v-html="essentialText"></div>
            </article>
        </fieldset>
    </form>
</template>
<style lang="scss">
    .kit-info-box {

        .kit-info-content {
        padding: 10px 20px;
    }
}
</style>