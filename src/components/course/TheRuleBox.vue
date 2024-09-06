<script setup>
import { computed } from 'vue';
import { useContextStore } from '../../stores/context';
import { useRulesStore } from '../../stores/rules';

const contextStore = useContextStore();
const rulesStore = useRulesStore();

const hasRule = computed(() => {
    return rulesStore.hasRule;
});
const rule = computed(() => {
    return hasRule.value ? rulesStore.all[0] : null;
});
const isTeacher = computed(() => {
    return contextStore.isTeacher;
});

const showRule = computed(() => {
    return isTeacher.value || rule.value.released;
});
</script>

<template>
    <form v-if="showRule" class="default collapsable kit-rule-box"  @submit.prevent>
        <fieldset :class="{'collapsed': isTeacher }">
            <legend>{{ $gettext('Rules for Tools: Verbindliche Hinweise zur Nutzung von KI-Tools') }} <span v-if="isTeacher && !rule.released">({{ $gettext('nicht veröffentlicht') }})</span></legend>
            <article class="kit-rule-content">
                <p v-html="rule.content"></p>
                <div class="kit-rule-buttons-wrapper" v-if="isTeacher">
                    <button class="button" @click="$emit('edit-rule')">{{ $gettext('Bearbeiten') }}</button>
                    <button v-if="!rule.released" class="button" @click="$emit('release')">{{ $gettext('Veröffentlichen') }}</button>
                </div>
            </article>
        </fieldset>
    </form>
</template>
<style lang="scss">
.kit-rule-box {
    margin-bottom: 30px;
    .kit-rule-content {
        padding: 10px 20px;
    }
    .kit-rule-buttons-wrapper {
        display: flex;
        flex-direction: row;
        justify-content: flex-end;
        margin-top: 30px;
        border-top: solid thin var(--content-color-40);
    }
}

</style>