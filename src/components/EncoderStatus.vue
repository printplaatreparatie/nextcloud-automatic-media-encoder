<template>
	<section class="automc-status">
		<h2>{{ t('Status') }}</h2>

		<span v-if="statusMessage" class="automc-settings-personal-status__message">
			{{ statusMessage }}
		</span>
		<span v-if="statusError" class="automc-settings-personal-status__error">
			{{ statusError }}
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
