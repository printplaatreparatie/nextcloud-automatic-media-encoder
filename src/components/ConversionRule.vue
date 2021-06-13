<template>
	<div class="automc-conversion-rule">
		<div class="automc-conversion-rule__delete-button">
			<Actions>
				<ActionButton icon="icon-delete" @click="$emit('remove')" />
			</Actions>
		</div>
		<div class="automc-conversion-rule__source-directory">
			<span>{{ ta(`When files are added to this folder`) }}</span>
			<div>
				<button @click="openFilePicker('sourceDirectory')">
					Choose Folder
				</button>
				<span>{{ sourceDirectory }}</span>
			</div>
		</div>
		<div class="automc-conversion-rule__from-format">
			<label>With this file extension/format</label>
			<select v-model="fromFormat" class="automc-conversion-rule__from-format-picker">
				<option value="" />
				<option
					v-for="(format, index) in fromFormats"
					:key="`format-from-${index}`"
					:value="format">
					<span>.{{ format.extension }} ({{ format.label }})</span>
				</option>
			</select>
		</div>
		<div class="automc-conversion-rule__to-format">
			<label>Convert them into this format</label>
			<select v-model="toFormat" class="automc-conversion-rule__to-format-picker">
				<option value="" />
				<option
					v-for="(format, index) in toFormats"
					:key="`format-to-${index}`"
					:value="format">
					<span>.{{ format.extension }} ({{ format.label }})</span>
				</option>
			</select>
		</div>
		<div class="automc-conversion-rule__encoder-settings">
			<label>With these encoder settings</label>
			<div class="automc-conversion-rule__encoder-settings-body">
				<div v-if="toFormat">
					<div v-if="toFormat.settings_schema">
						settings-n-stuff!
					</div>
					<div v-else>
						<p>The "{{ toFormat.label }}" format has no supported encoder settings.</p>
						<small>While you may still convert to this format, default parameters will be used which may result in media that is higher/lower in quality than desired.</small>
						<small>
							Found an issue or want to contribute settings for this encoder?
							<a target="_blank" rel="noopener noreferrer" :href="`https://github.com/cwilby/nextcloud-automatic-media-encoder/issues/new?title=[Encoder Settings Request] ${toFormat.label}`">
								<span>Open an issue on GitHub</span>
							</a>
						</small>
					</div>
				</div>
				<div v-else>
					(Select a "To" format to see encoder settings)
				</div>
			</div>
		</div>
		<div class="automc-conversion-rule__post-encode-rule">
			<label for="">Then</label>
			<select v-model="postEncodeRule" class="automc-conversion-rule__post-encode-rule-picker">
				<option value="keep">
					{{ ta("keep the old files") }}
				</option>
				<option value="delete">
					{{ ta("delete the old files") }}
				</option>
				<option value="move">
					{{ ta("move the old files") }}
				</option>
			</select>
		</div>
		<div v-if="postEncodeRule === 'move'" class="automc-conversion-rule__post-encode-rule--move">
			<label for="">To this directory</label>
			<div>
				<button @click="openFilePicker('moveDirectory')">
					Choose Folder
				</button>
				<span>{{ moveDirectory }}</span>
			</div>
		</div>
	</div>
</template>

<script>
import { FilePicker } from '@nextcloud/dialogs'
import Actions from '@nextcloud/vue/dist/Components/Actions'
import ActionButton from '@nextcloud/vue/dist/Components/ActionButton'

export default {
	components: { Actions, ActionButton },

	props: {
		rule: {
			required: true,
			type: Object,
		},
		fromFormats: {
			required: true,
			type: Array,
		},
		toFormats: {
			required: true,
			type: Array,
		},
	},

	computed: {
		sourceDirectory: {
			get() {
				return this.rule.source_directory
			},
			set(sourceDirectory) {
				this.emitChange({ source_directory: sourceDirectory })
			},
		},

		fromFormat: {
			get() {
				return this.rule.from_format
			},
			set(fromFormat) {
				this.emitChange({ from_format: fromFormat })
			},
		},

		toFormat: {
			get() {
				return this.rule.to_format
			},
			set(toFormat) {
				this.emitChange({ to_format: toFormat })
			},
		},

		postEncodeRule: {
			get() {
				return this.rule.post_encode_rule
			},
			set(postEncodeRule) {
				this.emitChange({ post_encode_rule: postEncodeRule })
			},
		},

		moveDirectory: {
			get() {
				return this.rule.move_directory
			},
			set(moveDirectory) {
				this.emitChange({ move_directory: moveDirectory })
			},
		},
	},

	methods: {
		emitChange(mutation) {
			this.$emit('change', { ...this.rule, ...mutation })
		},

		async openFilePicker(directoryKey) {
			const filepicker = new FilePicker(
				'', // title
				false, // multiSelect,
				[], // mime type filter,
				true, // modal
				1, // file picker type (1-choose,2-move,3-copy,4-copymove)
				true, // directories allowed
			)

			this[directoryKey] = await filepicker.pick()
		},
	},
}
</script>

<style lang="scss">
.automc-conversion-rule {
	position: relative;
	padding: 2em;
	border: 1px solid #ededed;
	border-radius: .5em;
	margin-bottom: 1em;

	label {
		display: block;
	}

	small {
		display: block;
		a {
			font-weight: 500;
		}
	}

	select, input {
		width: 100%;
		margin-bottom: 2em;
	}

	&__delete-button {
		position: absolute;
		top: .5em;
		right: 1em;
	}

	&__source-directory {
		width: 100%;
		margin-bottom: 2em;
		div {
			button {
				height: 2em;
				width: 12em;
			}
		}
	}

	&__formats {
		display: grid;
		grid-template-columns: 1fr 1fr;
		grid-template-rows: 1fr;
		gap: 0 1em;
	}

	&__encoder-settings {
		&-body {
			padding: 1em;
			background-color: #fdfdff;
			border: 1px dashed #ededed;
			margin-bottom: 2em;
		}
	}

	&__post-encode-rule {
		label {
			display: block;
		}

		&--move {
			width: 100%;
			div {
				button {
					height: 2em;
					width: 12em;
				}
			}
		}
	}
}
</style>
