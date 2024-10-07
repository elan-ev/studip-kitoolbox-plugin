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
const cleanAuthentication = () => {
    // Clean up unrelated authentication configs based on selected auth method
    if (currentTool.value.auth_method === 'jwt' || currentTool.value.auth_method === 'none') {
        currentTool.value.oidc_client_id = '';
        currentTool.value.oidc_client_secret = '';
        currentTool.value.oidc_redirect_url = '';
    }

    if (currentTool.value.auth_method === 'oidc' || currentTool.value.auth_method === 'none') {
        currentTool.value.jwt_key = '';
    }
}
const storeTool = () => {
    updateShowEditDialog(false);
    currentTool.value.metadata = null;
    cleanAuthentication();
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
        auth_method: 'none',
        oidc_client_id: '',
        // oidc configs
        oidc_client_secret: '',
        oidc_redirect_url: '',
        // jwt config
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
            <thead>
                <tr>
                    <th>{{ $gettext('Aktiv') }}</th>
                    <th>{{ $gettext('URL') }}</th>
                    <th>{{ $gettext('Preview-URL') }}</th>
                    <th>{{ $gettext('Authentifizierung') }}</th>
                    <th>{{ $gettext('OIDC Client ID') }}</th>
                    <th>{{ $gettext('OIDC Client Secret ') }}</th>
                    <th>{{ $gettext('OIDC Redirect URL ') }}</th>
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
                        {{ tool.auth_method || 'none' }}
                    </td>
                    <td>
                        {{ tool.oidc_client_id }}
                    </td>
                    <td>
                        {{ tool.oidc_client_secret }}
                    </td>
                    <td>
                        {{ tool.oidc_redirect_url }}
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
                    <td colspan="13">
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
                        <span class="tool-metadata">{{ currentTool.metadata?.title?.['de-DE'] }}</span>
                    </label>
                    <label for="current-tool-description">
                        {{ $gettext('Beschreibung') }}
                    </label>
                    <StudipWysiwyg id="current-tool-description"  v-model="currentTool.description"></StudipWysiwyg>
                    <span class="tool-metadata">{{ currentTool.metadata?.description?.['de-DE'] }}</span>
                    <br>
                    <label>
                        {{ $gettext('Authentifizierung') }}
                        <select v-model="currentTool.auth_method">
                            <option value="none">{{ $gettext('Keine Authentifizierung') }}</option>
                            <option value="oidc">{{ $gettext('OpenID Connect (OIDC)') }}</option>
                            <option value="jwt">{{ $gettext('JSON Web Token (JWT)') }}</option>
                        </select>
                    </label>
                    <div v-show="currentTool.auth_method === 'oidc'">
                        <label>
                            {{ $gettext('OIDC Client ID') }}
                            <input type="text" v-model="currentTool.oidc_client_id" />
                        </label>
                        <label>
                            {{ $gettext('OIDC Client Secret') }}
                            <input type="text" v-model="currentTool.oidc_client_secret" />
                        </label>
                        <label>
                            {{ $gettext('OIDC Redirect URL') }}
                            <input type="url" v-model="currentTool.oidc_redirect_url" />
                        </label>
                    </div>
                    <div v-show="currentTool.auth_method === 'jwt'">
                        <label>
                            {{ $gettext('JWT Key') }}
                            <input type="text" v-model="currentTool.jwt_key" />
                        </label>
                    </div>
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
                        {{ $gettext('Authentifizierung') }}
                        <select v-model="newTool.auth_method">
                            <option value="none">{{ $gettext('Keine Authentifizierung') }}</option>
                            <option value="oidc">{{ $gettext('OpenID Connect (OIDC)') }}</option>
                            <option value="jwt">{{ $gettext('JSON Web Token (JWT)') }}</option>
                        </select>
                    </label>
                    <div v-show="newTool.auth_method === 'oidc'">
                        <label>
                            {{ $gettext('OIDC Client ID') }}
                            <input type="text" v-model="newTool.oidc_client_id" />
                        </label>
                        <label>
                            {{ $gettext('OIDC Client Secret') }}
                            <input type="text" v-model="newTool.oidc_client_secret" />
                        </label>
                        <label>
                            {{ $gettext('OIDC Redirect URL') }}
                            <input type="url" v-model="newTool.oidc_redirect_url" />
                        </label>
                    </div>
                    <div v-show="newTool.auth_method === 'jwt'">
                        <label>
                            {{ $gettext('JWT Key') }}
                            <input type="text" v-model="newTool.jwt_key" />
                        </label>
                    </div>
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