<script setup>
import { computed } from 'vue';
import { useContextStore } from '../../stores/context';
const contextStore = useContextStore();

const settingsText = computed(() => {
    return contextStore.staticTexts['landing-page-teacher-settings'];
});
</script>

<template>
    <section id="kit-settings-info-box">
        <article>
            <div v-html="settingsText"></div>
            <div class="kit-button-wrapper">
                <button class="button" @click="contextStore.setShowRuleDialog(true);">
                    {{ $gettext('Rules for Tools anlegen') }}
                </button>
                <button class="button" @click="contextStore.setExploreMode(true);">
                    {{ $gettext('Erstmal umschauen') }}
                </button>
            </div>
        </article>
    </section>
</template>

<style lang="scss">
#kit-settings-info-box {
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    background-color: var(--content-color-20);
    padding: 30px 30px 15px 30px;
    margin-bottom: 15px;
    max-width: 860px;

    article {
        flex-grow: 1;
        .kit-text-bold {
            font-weight: 700;
        }
    }
    .kit-button-wrapper {
        float: right;
        margin-top: 1em;
    }
}
</style>
