<script setup>
import StudipIcon from '../studip/StudipIcon.vue';
import { computed } from 'vue';
import { useContextStore } from '../../stores/context';

const contextStore = useContextStore();

const isTeacher = computed(() => {
    return contextStore.isTeacher;
});

const teaserText = computed(() => {
    return isTeacher.value ? contextStore.staticTexts['landing-page-teacher'] : contextStore.staticTexts['landing-page-student'];
});
</script>

<template>
    <section id="kit-teaser-box">
        <StudipIcon shape="network2" :size="96" />
        <article>
            <header>
                <h1>{{ $gettext('KI-Toolbox') }}</h1>
            </header>
            <p v-html="teaserText"></p>
        </article>
    </section>
</template>

<style lang="scss">
#kit-teaser-box {
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    background-color: var(--content-color-20);
    padding: 30px;

    img {
        width: 96px;
        height: 96px;
        padding: 30px 30px 5px 30px;
        margin: auto 30px auto 0;
    }
    article {
        max-width: calc(100% - 186px);
        flex-grow: 1;
        .kit-text-bold {
            font-weight: 700;
        }
    }
}
</style>
