<script setup>
import StudipIcon from '../studip/StudipIcon.vue';

</script>

<template>
    <section id="kit-teaser-box">
        <StudipIcon shape="exclaim-circle" :size="96" />
        <article>
            <header>
                <h1>{{ $gettext('Grundsätzliches') }}</h1>
            </header>
            <p>
                Über die KI-Toolbox erhalten Sie einen datenschutzkonformen Zugang zu generativen KI-Tools. 
                Das bedeutet: Sie benötigen keinen eigenen Account bei dem jeweiligen Tool-Anbieter. 
                Die Eingaben, die Sie über den Uni-Zugang an ein KI-System übermitteln, kann der Anbieter 
                keiner Person zuordnen.
                <span class="kit-text-bold">
                    Beachten Sie jedoch: Sämtliche Eingaben werden an den Tool-Anbieter gesendet und durch 
                    dessen Dienste verarbeitet. Geben Sie daher auf keinen Fall personenbezogene Daten ein 
                    und beachten Sie das
                    <a href="https://digitale-lehre.uni-osnabrueck.de/rechtliches/" target="_blank">Urheberrecht</a>.
                </span>

            </p>
            <p>
                Bitte bedenken Sie außerdem: Die Ausgaben von KI-Systemen können fehlerhaft sein und/oder 
                stereotype Darstellungsweisen reproduzieren. Wir empfehlen daher dringend, jeden Output 
                dahingehend zu überprüfen. Weitere Informationen zu Risiken und ethischen Bedenken im Umgang 
                mit KI-Tools finden Sie 
                <span class="kit-text-bold"><a href="https://digitale-lehre.uni-osnabrueck.de/was-ist-ki/" target="_blank">hier</a></span>.
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
    margin-bottom: 15px;

    img {
        width: 96px;
        height: 96px;
        padding: 30px;
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