<script setup>
import { computed } from 'vue';

const props = defineProps({
    courseTool: Object,
    showQuotaState: {
        type: Boolean,
        default: false,
    },
});

const preview = computed(() => {
    return (
        props.courseTool.preview ||
        STUDIP.URLHelper.getURL('plugins_packages/elan-ev/KIToolbox/assets/images/kitoolbox-preview-default.svg')
    );
});

const name = computed(() => {
    return props.courseTool.name;
});

const description = computed(() => {
    return props.courseTool.description;
});
</script>

<template>
    <article class="kit-tool-item" :class="editModeHighlight">
        <header class="kit-tool-item-head">
            <h2>{{ name }}</h2>
            <template v-if="showQuotaState">
                <div v-if="!toolTokenLimitReached" class="kit-token-info">
                    <span v-if="toolTokenLimit !== null" :class="{ 'kit-token-warning': toolTokenLimit <= 0 }">
                        {{ $gettext('Übrige Tokens') + ': ' + toolTokenLimit }}
                    </span>
                    <span v-if="userTokenLimit !== null" :class="{ 'kit-token-warning': userTokenLimit <= 0 }">
                        <span v-if="toolTokenLimit !== null" class="seperator"> | </span>
                        {{ $gettext('Ihre restlichen Tokens') + ': ' + userTokenLimit }}
                    </span>
                </div>
                <div v-else class="kit-token-info">
                    <span class="kit-token-warning">{{ $gettext('Die Tokens für dieses Tool sind verbraucht!') }}</span>
                </div>
            </template>
        </header>
        <div class="kit-tool-item-body">
            <img :src="preview" aria-hidden="true" />
            <p v-html="description" class="formatted-content ck-content"></p>
        </div>
        <footer class="kit-tool-item-footer">
            <a :href="courseTool.redirect" target="_blank" class="button">
                {{ $gettext('Tool starten') }}
            </a>
        </footer>
    </article>
</template>
