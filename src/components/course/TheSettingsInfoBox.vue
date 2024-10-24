<script setup>
import { useContextStore } from '../../stores/context';
const contextStore = useContextStore();

</script>
<template>
    <section id="kit-settings-info-box">
        <article>
            <header>
                <h1>{{ $gettext('Einrichtung der Rules for Tools') }}</h1>
            </header>
            <p>
                Rules for Tools sind spezielle Regeln, die die Nutzung KI-basierter Anwendungen in einer Veranstaltung
                rahmen. Über die Rules for Tools können Sie Ihren Studierenden vermitteln, welche Tools sie zu welchen
                Zwecken und in welchem Umfang in Ihrer Veranstaltung nutzen dürfen. Genauere Informationen zu den Rules
                for Tools finden Sie auf unserem
                <a href="https://digitale-lehre.uni-osnabrueck.de/wie-kann-ich-ki-in-der-lehre-nutzen/" target="_blank">Digitale-Lehre-Portal</a>. 
            </p>
            <p>
                Fragen können bei der Erstellung von Rules for Tools hilfreich sein:
            </p>
            <p>  
                <ul>
                    <li>
                        Möchten Sie KI-Tools in Ihrer Veranstaltung zulassen? Wenn ja, welche und unter welchen Umständen? Wenn nein, warum nicht?
                    </li>
                    <li>
                        Wozu möchten Sie Studierende verpflichten, wenn sie KI-Tools nutzen?
                    </li>
                </ul>
            </p>
            <p>
                Falls Sie Ihre Rules for Tools nicht
                komplett selbst formulieren möchten, können Sie auch die frei lizensierte Vorlage von Dr. Christian
                Spannagel verwenden. Die Vorlage können Sie nach Ihren Vorstellungen modifizieren. 
            </p>
            <p>
                Bitte beachten Sie:
                Erst nachdem Sie die Einrichtung der Rules for Tools abgeschlossen und gespeichert haben, können Sie
                Tools für die Studierenden im Kurs aktivieren. Natürlich können Sie sich auch entscheiden, keine Rules
                for Tools zu definieren, lassen Sie hierfür einfach das Textfeld im Dialog leer und klicken auf
                Speichern.
            </p>
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
