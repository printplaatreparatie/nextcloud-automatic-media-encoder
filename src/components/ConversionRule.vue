<template>
	<div class="automc-conversion-rule">
		<span>{{ ta(`Convert files added to`) }}</span>
		<input v-model="sourceDirectory" type="text">
		<span>&nbsp;{{ ta("from") }}&nbsp;</span>
		<select v-model="fromFormat">
			<option value="" />
			<option
				v-for="({ value, label }, index) in fromFormats"
				:key="`${mediaType}-from-${index}`"
				:value="value">
				{{ label }}
			</option>
		</select>
		<span>&nbsp;{{ ta("to") }}&nbsp;</span>
		<select v-model="toFormat">
			<option value="" />
			<option
				v-for="({ value, label }, index) in toFormats"
				:key="`${mediaType}-to-${index}`"
				:value="value">
				{{ label }}
			</option>
		</select>
		<span>&nbsp;{{ ta("and") }}&nbsp;</span>
		<select v-model="postEncodeRule">
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
		<input
			v-if="postEncodeRule === 'move'"
			v-model="moveDirectory"
			type="text">
	</div>
</template>

<script>
export default {
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
	},
}
</script>
