import Vue from 'vue'
import { translate, translatePlural } from '@nextcloud/l10n'

Vue.filter('ucwords', (value) =>
	value
		? value.toLowerCase().replace(/\b[a-z]/g, (letter) => letter.toUpperCase())
		: null
)

Vue.prototype.ta = (...args) => translate('automaticmediaencoder', ...args)
Vue.prototype.t = translate
Vue.prototype.n = translatePlural
Vue.prototype.OC = window.OC
Vue.prototype.OCA = window.OCA
