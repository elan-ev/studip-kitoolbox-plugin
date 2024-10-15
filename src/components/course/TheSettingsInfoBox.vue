<script setup></script>
<template>
    <section id="kit-settings-info-box">
        <article>
            <header>
                <h1>{{ $gettext('Einrichtung der Rules for Tools') }}</h1>
            </header>
            <p>
                Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut
                labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et
                ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum
                dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore
                magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet
                clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.
            </p>
            <div class="kit-button-wrapper">
                <button class="button" @click="$emit('show-rule-dialog')">{{ $gettext('Rules for Tools anlegen') }}</button>
                <button class="button" @click="$emit('enable-explore-mode')">{{ $gettext('Erstmal umschauen') }}</button>
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
