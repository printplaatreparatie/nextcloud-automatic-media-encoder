<template>
	<div class="automc-conversion-rule-list">
		<div v-if="!rules.length" class="alert">
			{{ ta(`There are no conversion rules, click "+ Add" to get started.`) }}
		</div>
		<ul class="automc-conversion-rules__list">
			<li v-for="(rule, i) in rules" :key="i">
				<ConversionRule
					:rule="rule"
					:from-formats="fromFormats"
					:to-formats="toFormats"
					@change="$emit('changeRule', $event)"
					@remove="$emit('removeRule', rule)" />
			</li>
			<li class="add">
				<button @click="$emit('addRule')">
					<span class="icon icon-add" /> Add Conversion Rule
				</button>
			</li>
		</ul>
	</div>
</template>

<script>
import ConversionRule from './ConversionRule.vue'

export default {
	components: { ConversionRule },

	props: {
		rules: {
			required: true,
			type: Array,
		},
		formats: {
			required: true,
			type: Array,
		},
	},

	computed: {
		fromFormats() {
			return this.formats.filter(f => f.decode)
		},

		toFormats() {
			return this.formats.filter(f => f.encode)
		},
	},
}
</script>
