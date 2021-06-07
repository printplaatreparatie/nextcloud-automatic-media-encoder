import Vue from 'vue'
import { translate, translatePlural } from '@nextcloud/l10n'

Vue.prototype.ta = (...args) => translate('automaticmediaencoder', ...args)
Vue.prototype.t = translate
Vue.prototype.n = translatePlural
Vue.prototype.OC = window.OC
Vue.prototype.OCA = window.OCA
