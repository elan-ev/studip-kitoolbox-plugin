<script setup>
import { computed } from 'vue';

const props = defineProps({
    tool: Object,
    editMode: Boolean,
});

const editModeHighlight = computed( () => {
    if (editMode) {
        return tool.active ? 'active-item' : 'inactive-item';
    }
});
</script>

<template>
    <article class="kit-tool-item" :class="editModeHighlight">
        <header class="kit-tool-item-head">
            <h2>{{ tool.name }}</h2>
        </header>
        <div class="kit-tool-item-body">
            <img :src="previewImage" />
            <p>{{ tool.description }}</p>
        </div>
    </article>
</template>
