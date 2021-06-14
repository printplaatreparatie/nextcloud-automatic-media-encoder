<template>
	<div class="automc-status">
		<div v-if="!anyPresentStatistics">
			There are currently no jobs running.  Create a conversion rule to start.
		</div>
		<div v-else>
			<div class="automc-status-bar" :style="barContainerStyles">
				<div v-for="(stat, key) in presentStatistics"
					:key="key"
					class="automc-st"
					:style="{'background-color': colorMap[key], width: `${stat / total * 100}%`}" />
			</div>
			<div class="automc-status-bar-legend">
				<strong>Legend</strong>
				<div v-for="(stat, key) in presentStatistics" :key="key" class="automc-status-bar-legend-key">
					<span class="badge" :style="{ backgroundColor: colorMap[key] }">{{ stat }}</span>
					<span class="label"> {{ key | ucwords }}</span>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
export default {
	props: {
		statistics: {
			required: true,
			type: Object,
		},
	},

	data: () => ({
		colorMap: {
			pending: '#0082c9',
			converting: 'yellow',
			finished: '#46ba61',
			retries: 'orange',
			failed: 'red',
			ignored: 'gray',
		},
	}),

	computed: {
		anyPresentStatistics() {
			return Object.keys(this.presentStatistics).length
		},

		presentStatistics() {
			return Object.entries(this.statistics).reduce((presentStatistics, [key, value]) => {
				if (value > 0) {
					presentStatistics[key] = value
				}

				return presentStatistics
			}, {})
		},

		total() {
			return Object.values(this.statistics).reduce((sum, stat) => sum + stat)
		},

		barContainerStyles() {
			const weights = Object.values(this.statistics).map(statistic => `${statistic / this.total * 100}%`)

			return {
				'grid-template-columns': weights.join(' '),
			}
		},
	},
}
</script>

<style lang="scss">
.automc-status {
	max-width: 720px;
	margin-bottom: 1em;

	&-bar {
		display: grid;
		grid-template-rows: 1fr;
		height: 1em;
		max-height: 1em;
		min-height: 1em;
		max-width: 720px;
		min-width: 720px;
		width: 720px;
		border: 1px solid #ededed;
	}

	&-bar-legend {
		&-key {
			display: flex;
			align-items: center;
			flex-direction: row;
			margin-bottom: .66em;
			.badge {
				display: flex;
				align-items: center;
				justify-content: center;
				min-width: 2em;
				min-height: 2em;
				max-width: 2em;
				max-height: 2em;
				border-radius: 50%;
			}

			.label {
				margin-left: .66em;
			}
		}
	}
}
</style>
