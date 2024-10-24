<script setup>
import StudipIcon from '../studip/StudipIcon.vue';
import { computed } from 'vue';
import { useContextStore } from '../../stores/context';

const contextStore = useContextStore();

const isTeacher = computed(() => {
    return contextStore.isTeacher;
});
</script>

<template>
    <section id="kit-teaser-box">
        <StudipIcon shape="network2" :size="96" />
        <article>
            <header>
                <h1>{{ $gettext('KI-Toolbox') }}</h1>
            </header>
            <p v-if="isTeacher">
                Die KI-Toolbox bietet einen direkten Zugriff auf alle KI-Tools, die an der Uni Osnabrück hochschulweit
                zur Verfügung stehen. Als Lehrende haben Sie die Möglichkeit, aus den verfügbaren KI-Werkzeugen jene
                auszuwählen und zu aktivieren, die Sie in Ihrer Lehrveranstaltung gemeinsam mit den Studierenden nutzen
                wollen. Außerdem bietet die KI-Toolbox einen zentralen Ort, an dem Sie Regeln für Nutzung von KI-Tools
                in Ihrer Lehrveranstaltung hinterlegen können.
            </p>
            <p v-else>
                Die KI-Toolbox bietet einen direkten Zugriff auf ausgewählte KI-Tools, die an der Uni Osnabrück
                hochschulweit zur Verfügung stehen. Sie finden über den KI-Toolbox-Reiter die in der Lehrveranstaltung
                geltenden „Rules for Tools“ und können auf die KI-Werkzeuge zugreifen, die in dieser Lehrveranstaltung
                genutzt werden dürfen.
            </p>
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
