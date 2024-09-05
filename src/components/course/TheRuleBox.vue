<script setup>
import { computed, ref, onBeforeMount, onBeforeUnmount, onMounted } from 'vue';
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
    <form v-if="showRule" class="default collapsable kit-rule-box" @submit.prevent>
        <fieldset class="collapsed">
            <legend>{{ $gettext('Rules for Tools: Verbindliche Hinweise zur Nutzung von KI-Tools') }} <span v-if="isTeacher && !rule.released">({{ $gettext('nicht veröffentlicht') }})</span></legend>
            <article class="kit-rule-content">
                <p v-html="rule.content"></p>
                <button v-if="isTeacher" class="button" @click="$emit('edit-rule')">{{ $gettext('Bearbeiten') }}</button>
            </article>
        </fieldset>
    </form>
</template>
<style lang="scss">
.kit-rule-box {
    margin-bottom: 30px;

    .kit-rule-content {
    padding: 10px;
}
}

</style>