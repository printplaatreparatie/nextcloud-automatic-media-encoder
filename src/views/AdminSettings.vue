<template>
	<div class="automc-admin-settings">
		<h2>
			<img :src="require('@/img/icon.svg')">
			{{ ta('Automatic Media Encoder') }}
		</h2>
		<encoder-status :status-message="state.status_message" :status-error="state.status_error" />

		<h2>{{ ta('Settings') }}</h2>
		<div class="checkbox">
			<input v-model="videoConversionEnabled" type="checkbox"> <span>Enable Video Conversion</span>
		</div>
		<div class="checkbox">
			<input v-model="photoConversionEnabled" type="checkbox"> <span>Enable Photo Conversion</span>
		</div>
		<div class="checkbox">
			<input v-model="audioConversionEnabled" type="checkbox"> <span>Enable Audio Conversion</span>
		</div>
	</div>
</template>

<script>
import debounce from 'debounce'
import { showError } from '@nextcloud/dialogs'
import { generateUrl } from '@nextcloud/router'
import axios from '@nextcloud/axios'
import { loadState } from '@nextcloud/initial-state'

export default {
	name: 'AdminSettings',

	data: () => ({
		state: loadState('automaticmediaencoder', 'admin-config'),
		readonly: true,
	}),

	computed: {
		videoConversionEnabled: {
			get() {
				return this.state.video_conversion_enabled
			},
			set(value) {
				this.state.video_conversion_enabled = value
				this.saveConfig()
			},
		},

		photoConversionEnabled: {
			get() {
				return this.state.photo_conversion_enabled
			},
			set(value) {
				this.state.photo_conversion_enabled = value
				this.saveConfig()
			},
		},

		audioConversionEnabled: {
			get() {
				return this.state.audio_conversion_enabled
			},
			set(value) {
				this.state.audio_conversion_enabled = value
				this.saveConfig()
			},
		},
	},

	methods: {
		saveConfig: debounce(async function() {
			try {
				this.saving = true
				await axios.put(generateUrl('/apps/automaticmediaencoder/config'), this.state)
			} catch (e) {
				showError(this.ta('Failed to save automatic media encoder config'))
				console.error(e)
			} finally {
				this.saving = false
			}
		}, 600),
	},
}
</script>

<style lang="scss">
.automc-admin-settings {
	padding: 1em;
	.checkbox {
		display: flex;
		align-items: center;
	}
}
</style>
