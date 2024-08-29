<script setup>
import { computed } from 'vue';
import { useContextStore } from './../../stores/context';

const contextStore = useContextStore();
const isTeacher = computed(() => {
    return contextStore.isTeacher;
});

const companion = computed(() => {
    return STUDIP.URLHelper.getURL('assets/images/companion/Tin_unsure.svg');
});
</script>
<template>
    <div class="kit-teaser">
        <img :src="companion" />
        <h2>{{ $gettext('Es sind noch keine KI-Tools aktiv.') }}</h2>
        <template v-if="isTeacher">
            <button class="button" @click="$emit('switch-mode-edit')">{{ $gettext('Tools aktivieren') }}</button>
            <button class="button" @click="$emit('show-rules-dialog')">{{ $gettext('Rules for Tools bearbeiten') }}</button>
        </template>
    </div>
</template>
<style lang="scss">
.kit-teaser {
    text-align: center;
    width: 100%;

    img {
        max-width: 256px;
        height: auto;
    }

    h2 {
        margin-top: -20px;
        margin-bottom: 20px;
    }
}
</style>
