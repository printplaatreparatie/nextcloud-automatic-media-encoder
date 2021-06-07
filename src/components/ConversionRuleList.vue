<template>
	<div>
		<div v-if="!conversionRules.length" class="notification">
			{{ ta(`There are no ${mediaType} conversion rules, click "+ Add" to get started.`) }}
		</div>
		<ul class="automc-conversion-rules__list">
			<li v-for="(rule, i) in conversionRules" :key="i">
				<ConversionRule
					:rule="rule"
					:from-formats="fromFormats"
					:to-formats="toFormats"
					@change="onRuleChange" />
			</li>
			<li class="add" @click="$emit('addRule')">
				+ {{ ta('Add Conversion Rule') }}
			</li>
		</ul>
	</div>
</template>

<script>
import ConversionRule from './ConversionRule.vue'

export default {
	components: { ConversionRule },

	props: {
		mediaType: {
			required: true,
			type: String,
		},
		conversionRules: {
			required: true,
			type: Array,
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

	methods: {
		onRuleChange(rule) {
			this.$emit('change', { mediaType: this.mediaType, rule })
		},
	},
}
</script>
