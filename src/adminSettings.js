import Vue from 'vue'
import './bootstrap'
import AdminSettings from './views/AdminSettings'

new Vue({ // eslint-disable-line no-new
	el: '#automaticmediaencoder_prefs',
	render: h => h(AdminSettings),
})
