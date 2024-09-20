<script setup>
import { computed } from 'vue';
import { useContextStore } from '../../stores/context';
const isTeacher = computed(() => {
    return contextStore.isTeacher;
});
</script>
<template>
    <form class="default kit-info-box collapsable"  @submit.prevent>
        <fieldset>
            <legend>{{ $gettext('Grundsätzliches') }}</legend>
            <article class="kit-info-content">
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
        </fieldset>
    </form>
</template>
<style lang="scss">
    .kit-info-box {

        .kit-info-content {
        padding: 10px 20px;
    }
}
</style>