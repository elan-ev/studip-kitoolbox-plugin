<script setup>
import { computed, ref } from 'vue';
import StudipActionMenu from '../studip/StudipActionMenu.vue';
import StudipDialog from '../studip/StudipDialog.vue';
import StudipIcon from '../studip/StudipIcon.vue';
import { useGettext } from 'vue3-gettext';
import { useToolsStore } from './../../stores/tools';
import StudipWysiwyg from '../studip/StudipWysiwyg.vue';
const { $gettext } = useGettext();

const openRemoveDialog = ref(false);
const openEditDialog = ref(false);
const openCreateDialog = ref(false);
const currentTool = ref(null);
const newTool = ref(null);

const toolsStore = useToolsStore();

const tools = computed(() => {
    return toolsStore.all;
});

const setCurrentTool = (tool) => {
    currentTool.value = JSON.parse(JSON.stringify(tool));
};
const resetCurrentTool = () => {
    currentTool.value = null;
};

const showRemovetool = (tool) => {
    setCurrentTool(tool);
    updateShowRemoveDialog(true);
};
const updateShowRemoveDialog = (state) => {
    openRemoveDialog.value = state;
};
const removeTool = () => {
    updateShowRemoveDialog(false);
    toolsStore.deleteTool(currentTool.value);
    resetCurrentTool();
};


const showEditTool = (tool) => {
    setCurrentTool(tool);
    updateShowEditDialog(true);
};
const updateShowEditDialog = (state) => {
    openEditDialog.value = state;
};
const storeTool = () => {
    updateShowEditDialog(false);
    currentTool.value.metadata = null;
    toolsStore.updateTool(currentTool.value);
    resetCurrentTool();
};
const toggleActiveState = (tool) => {
    toolsStore.toggleActiveState(tool);
}


const initNewTool = () => {
    newTool.value = {
        name: '',
        description: '',
        url: '',
        'preview-url': '',
        jwt_key: '',
        api_key: '',
        'max-quota': -1
    };
};
const showCreateTool = () => {
    updateShowCreateDialog(true);
    initNewTool();
};
const updateShowCreateDialog = (state) => {
    openCreateDialog.value = state;
};
const createTool = () => {
    updateShowCreateDialog(false);
    toolsStore.createTool(newTool.value);
    newTool.value = null;
};
</script>

<template>
    <div class="kit-admin-tool-table">
        <table class="default">
            <caption>
                KI-Tools
            </caption>
            <colgroup>
                <col width="5%" />  <!-- aktiv -->
                <col width="30%" /> <!-- url -->
                <col width="10%" /> <!-- preview-url -->
                <col width="10%" /> <!-- jwt-schlüssel -->
                <col width="25%" /> <!-- titel -->
                <col width="5%" /> <!-- beschreibung -->
                <col width="5%" /> <!-- unterstütze quota -->
                <col width="5%" /> <!-- maximale quota -->
                <col width="5%" />  <!-- aktionen -->
            </colgroup>
            <thead>
                <tr>
                    <th>{{ $gettext('Aktiv') }}</th>
                    <th>{{ $gettext('URL') }}</th>
                    <th>{{ $gettext('Preview-URL') }}</th>
                    <th>{{ $gettext('JWT Schlüssel') }}</th>
                    <th>{{ $gettext('Title') }}</th>
                    <th>{{ $gettext('Beschreibung') }}</th>
                    <th>{{ $gettext('Unterstützte Quota') }}</th>
                    <th>{{ $gettext('Maximale Quota') }}</th>
                    <th>{{ $gettext('Aktionen') }}</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="tool in tools" :key="tool.id">
                    <td>
                        <input type="checkbox" :checked="tool.active" @change="toggleActiveState(tool)" />
                    </td>
                    <td>
                        {{ tool.url || '-' }}
                    </td>
                    <td>
                        {{ tool.preview || 'default' }}
                    </td>
                    <td>
                        {{ tool.jwt_key }}
                    </td>
                    <td>
                        {{ tool.name || '-' }}
                    </td>
                    <td>
                        <StudipIcon :shape="tool.description === '' ? 'decline' : 'accept'" role="info" />
                    </td>
                    <td>
                        {{ tool['quota-type'] }}
                    </td>
                    <td>
                        {{ tool['max-quota'] }}
                    </td>
                    <td class="actions">
                        <studip-action-menu
                            :context="$gettext('KI-Tool')"
                            :items="[
                                {
                                    id: 1,
                                    label: $gettext('Bearbeiten'),
                                    icon: 'edit',
                                    emit: 'edit',
                                },
                                {
                                    id: 2,
                                    label: $gettext('Entfernen'),
                                    icon: 'trash',
                                    emit: 'remove',
                                },
                            ]"
                            @edit="showEditTool(tool)"
                            @remove="showRemovetool(tool)"
                        />
                    </td>
                </tr>
                <tr v-if="tools.length === 0">
                    <td colspan="9">
                        {{ $gettext('Es wurden keine KI Tools eingetragen.') }}
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="9">
                        <div class="footer-items">
                            <button class="button add" @click="showCreateTool">
                                {{ $gettext('Tool hinzufügen') }}
                            </button>
                        </div>
                    </td>
                </tr>
            </tfoot>
        </table>
        <studip-dialog
            :height="200"
            :open="openRemoveDialog"
            confirm-class="trash"
            :confirm-text="$gettext('Entfernen')"
            :close-text="$gettext('Abbrechen')"
            :title="$gettext('Tool entfernen')"
            :question="$gettext('Möchten Sie das Tool entfernen?')"
            @update:open="updateShowRemoveDialog"
            @confirm="removeTool"
        >
        </studip-dialog>
        <studip-dialog
            :height="610"
            :width="800"
            :open="openEditDialog"
            confirm-class="accept"
            :confirm-text="$gettext('Speichern')"
            :close-text="$gettext('Abbrechen')"
            :title="$gettext('Tool Einstellungen bearbeiten')"
            @update:open="updateShowEditDialog"
            @confirm="storeTool"
        >
            <template #dialogContent>
                <form class="default">
                    <label>
                        {{ $gettext('URL') }}
                        <input type="url" v-model="currentTool.url" />
                    </label>
                    <label>
                        {{ $gettext('API Key') }}
                        <input type="text" v-model="currentTool.api_key" />
                    </label>
                    <label>
                        {{ $gettext('Preview-URL') }}
                        <input type="url" v-model="currentTool.preview" />
                        <span class="tool-metadata">{{ currentTool.metadata?.image_url }}</span>
                    </label>
                    <label>
                        {{ $gettext('Titel') }}
                        <input type="text" v-model="currentTool.name" />
                        <span class="tool-metadata">{{ currentTool.metadata?.title['de-DE'] }}</span>
                    </label>
                    <label for="current-tool-description">
                        {{ $gettext('Beschreibung') }}
                    </label>
                    <StudipWysiwyg id="current-tool-description"  v-model="currentTool.description"></StudipWysiwyg>
                    <span class="tool-metadata">{{ currentTool.metadata?.description['de-DE'] }}</span>
                    <br>
                    <label>
                        {{ $gettext('JWT Key') }}
                        <input type="text" v-model="currentTool.jwt_key" />
                    </label>
                    <label>
                        {{ $gettext('Max Quota') }}
                        <input type="number" min="-1" v-model="currentTool['max-quota']" />
                    </label>
                </form>
            </template>
        </studip-dialog>
        <studip-dialog
            :height="610"
            :open="openCreateDialog"
            confirm-class="accept"
            :confirm-text="$gettext('Hinzufügen')"
            :close-text="$gettext('Abbrechen')"
            :title="$gettext('Tool hinzufügen')"
            @update:open="updateShowCreateDialog"
            @confirm="createTool"
        >
            <template #dialogContent>
                <form class="default">
                    <label>
                        {{ $gettext('URL') }}
                        <input type="url" v-model="newTool.url" />
                    </label>
                    <label>
                        {{ $gettext('API Key') }}
                        <input type="text" v-model="newTool.api_key" />
                    </label>
                    <label>
                        {{ $gettext('JWT Key') }}
                        <input type="text" v-model="newTool.jwt_key" />
                    </label>
                </form>
            </template>
        </studip-dialog>
    </div>
</template>
<style lang="scss">
.tool-metadata {
    font-style: oblique;
}
</style>