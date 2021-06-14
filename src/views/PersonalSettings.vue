<template>
	<div>
		<SettingsSection title="Automatic Media Encoder" description="Configure rules to automatically convert your media as it is uploaded.">
			<h2>{{ ta('Status') }} <span v-if="refreshing" class="icon icon-history" /> </h2>
			<div>
				<PersonalStatus :statistics="state.statistics" />
			</div>

			<h2>{{ ta('Conversion Rules') }}</h2>
			<ConversionRuleList
				:rules="state.rules"
				:formats="state.formats"
				@changeRule="updateConversionRule"
				@addRule="addConversionRule"
				@removeRule="removeConversionRule" />
		</SettingsSection>
	</div>
</template>

<script>
import debounce from 'debounce'
import { showError } from '@nextcloud/dialogs'
import { generateUrl } from '@nextcloud/router'
import axios from '@nextcloud/axios'
import { loadState } from '@nextcloud/initial-state'
import SettingsSection from '@nextcloud/vue/dist/Components/SettingsSection'

import PersonalStatus from '../components/PersonalStatus.vue'
import ConversionRuleList from '../components/ConversionRuleList.vue'
import { generateUniqueId } from '../utils'

export default {
	components: { PersonalStatus, ConversionRuleList, SettingsSection },

	data: () => ({
		refreshing: false,
		polling: null,
		state: loadState('automaticmediaencoder', 'user-config'),
		loading: false,
		saving: false,
	}),

	beforeDestroy() {
		clearInterval(this.polling)
	},

	created() {
		this.pollData()
	},

	methods: {
		pollData() {
			this.polling = setInterval(async() => {
				if (document.hasFocus()) {
					await this.updateStatistics()
				}
			}, 5000)
		},

		addConversionRule: debounce(function() {
			this.state.rules.push({
				id: generateUniqueId(),
				source_directory: null,
				from_format: null,
				to_format: null,
				post_encode_rule: null,
				move_directory: null,
			})
			this.saveConfig()
		}, 600),

		updateConversionRule: debounce(function(rule) {
			this.state.rules = this.state.rules.map(r => r.id === rule.id ? rule : r)
			this.saveConfig()
		}, 600),

		removeConversionRule(rule) {
			OC.dialogs.confirmDestructive(
				'All queued and running conversions for this rule will be cancelled.',
				'Delete Rule',
				{
					type: OC.dialogs.YES_NO_BUTTONS,
					confirm: 'Delete',
					confirmClasses: 'error',
					cancel: 'Cancel',
				},
				(confirmed) => {
					if (!confirmed) return

					this.state.rules = this.state.rules.filter(r => r.id !== rule.id)

					return this.saveConfig()
				},
				true
			)
		},

		async saveConfig() {
			try {
				this.saving = true
				await axios.put(generateUrl('/apps/automaticmediaencoder/config'), { values: { rules: this.state.rules } })
			} catch (e) {
				showError(this.ta('Failed to save automatic media encoder config'))
				console.error(e)
			} finally {
				this.saving = false
			}
		},

		async updateStatistics() {
			try {
				this.refreshing = true
				const { data } = await axios.get(generateUrl('/apps/automaticmediaencoder/statistics'))
				this.state.statistics = data
			} catch (e) {
				showError(this.ta('Failed to save automatic media encoder config'))
				console.error(e)
			} finally {
				this.refreshing = false
			}
		},
	},
}
</script>
