<template>
	<div class="automc-personal-settings">
		<h2>
			<a class="icon icon-media" />
			{{ ta('Automatic Media Encoder') }}
		</h2>

		<encoder-status :status-message="state.status_message" :status-error="state.status_error" />

		<section class="automc-conversion-rules">
			<h2>{{ ta('Video Conversion Rules') }}</h2>
			<ConversionRuleList
				media-type="video"
				:conversion-rules="state.video_conversion_rules"
				:from-formats="state.video_from_formats"
				:to-formats="state.video_to_formats"
				@change="updateConversionRule"
				@addRule="() => addConversionRule('video')" />
		</section>

		<section class="automc-conversion-rules">
			<h2>{{ ta('Photo Conversion Rules') }}</h2>
			<ConversionRuleList
				media-type="photo"
				:conversion-rules="state.photo_conversion_rules"
				:from-formats="state.photo_from_formats"
				:to-formats="state.photo_to_formats"
				@change="updateConversionRule"
				@addRule="() => addConversionRule('photo')" />
		</section>

		<section class="automc-conversion-rules">
			<h2>{{ ta('Audio Conversion Rules') }}</h2>
			<ConversionRuleList
				media-type="audio"
				:conversion-rules="state.audio_conversion_rules"
				:from-formats="state.audio_from_formats"
				:to-formats="state.audio_to_formats"
				@change="updateConversionRule"
				@addRule="() => addConversionRule('photo')" />
		</section>
	</div>
</template>

<script>
import debounce from 'debounce'
import { showError } from '@nextcloud/dialogs'
import { generateUrl } from '@nextcloud/router'
import axios from '@nextcloud/axios'
import { loadState } from '@nextcloud/initial-state'

import ConversionRuleList from '../components/ConversionRuleList.vue'
import { generateUniqueId } from '../utils'

export default {
	components: { ConversionRuleList },

	data: () => ({
		state: loadState('automaticmediaencoder', 'user-config'),
		loading: false,
		saving: false,
	}),

	methods: {
		addConversionRule: debounce(function(mediaType) {
			const key = `${mediaType}_conversion_rules`
			this.state[key].push({
				id: generateUniqueId(),
				source_directory: null,
				from_format: null,
				to_format: null,
				post_encode_rule: null,
				move_directory: null,
			})
			this.saveConfig()
		}, 600),

		updateConversionRule: debounce(function({ mediaType, rule }) {
			const key = `${mediaType}_conversion_rules`
			this.state[key] = this.state[key].map(r => r.id === rule.id ? rule : r)
			this.saveConfig()
		}, 600),

		async saveConfig() {
			try {
				this.saving = true
				await axios.put(generateUrl('/apps/automaticmediaencoder/config'), this.state)
			} catch (e) {
				showError(this.ta('Failed to save automatic media encoder config'))
				console.error(e)
			} finally {
				this.saving = false
			}
		},
	},
}
</script>
