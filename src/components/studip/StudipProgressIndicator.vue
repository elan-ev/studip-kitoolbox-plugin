<script setup>
import { computed } from 'vue';
import { useGettext } from 'vue3-gettext';

const props = defineProps({
    description: {
        type: String,
        required: false,
    },
    size: {
        type: Number,
        default: 32,
    },
});

const indicatorStyle = computed(() => {
    return {
        backgroundSize: `${props.size}px`,
        height: `${props.size}px`,
    };
});

const hasDescription = computed(() => {
    return props.description?.trim().length > 0;
});
</script>
<template>
    <div class="progress-indicator-wrapper">
        <div class="progress-indicator" :style="indicatorStyle"></div>
        <p v-if="hasDescription" class="progress-indicator-description">
            {{ description }}
        </p>
        <p v-else class="progress-indicator-description-default">
            {{ $gettext('Lade...') }}
        </p>
    </div>
</template>
