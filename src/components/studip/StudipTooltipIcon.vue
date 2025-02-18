<script setup>
import { computed } from "vue";

const props = defineProps({
    text: String,
    isHtml: {
        type: Boolean,
        required: false,
        default: false
    },
    isImportant: {
        type: Boolean,
        required: false,
        default: false
    },
});

const cssClass = computed(() => props.isImportant ? 'tooltip-important' : '');
</script>

<template>
    <span data-tooltip class="tooltip tooltip-icon" :class="cssClass" tabindex="0">
        <span class="tooltip-content" v-if="isHtml" v-html="text"></span>
        <span class="tooltip-content" v-else>{{ text }}</span>
    </span>
</template>
