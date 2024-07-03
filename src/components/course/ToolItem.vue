<script setup>
import { computed, onMounted } from 'vue';

const props = defineProps({
    tool: Object,
    editMode: Boolean,
});

const editModeHighlight = computed(() => {
    if (props.editMode) {
        return props.tool['active-in-course'] ? 'active-item' : 'inactive-item';
    }
});

onMounted(()=> {
    console.log(props.tool);
});

</script>

<template>
    <article class="kit-tool-item" :class="editModeHighlight">
        <header class="kit-tool-item-head">
            <h2>{{ tool.name }}</h2>
        </header>
        <div class="kit-tool-item-body">
            <!-- <img :src="previewImage" /> -->
            <p>{{ tool.description }}</p>
        </div>
    </article>
</template>

<style lang="scss">
.kit-tool-item {
    &.active-item {
        border-left: solid 4px var(--dark-green);
    }
    &.inactive-item {
        border-left: solid 4px var(--dark-gray-color-80);
    }
}

</style>