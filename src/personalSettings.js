import Vue from 'vue'
import './bootstrap'
import PersonalSettings from './views/PersonalSettings.vue'

new Vue({ // eslint-disable-line no-new
	el: '#automaticmediaencoder_prefs',
	render: h => h(PersonalSettings),
})
