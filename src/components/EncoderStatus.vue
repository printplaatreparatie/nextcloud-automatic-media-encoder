<template>
	<section class="automc-status">
		<span v-if="statusMessage" class="automc-status__message">
			<strong>Status:</strong> {{ statusMessage }}
		</span>
		<span v-if="statusError" class="automc-status__error">
			<strong>Error:</strong> {{ statusError }}
		</span>
	</section>
</template>

<script>
export default {
	props: {
		statusMessage: {
			required: false,
			default: '',
			type: String,
		},
		statusError: {
			required: false,
			default: '',
			type: String,
		},
	},
}
</script>

<style lang="scss">
.automc-status {
	margin-bottom: 1em;
	&__message {
		color: green;
	}
	&__error {
		color: red;
	}
}
</style>
