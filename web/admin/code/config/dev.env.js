'use strict'
const merge = require('webpack-merge')
const prodEnv = require('./prod.env')

module.exports = merge(prodEnv, {
  NODE_ENV: '"development"',
  // BASE_API: '"https://www.easy-mock.com/mock/5b8606cdeeb1c664b09253e3/api"',
  BASE_API: '"http://admin.meeting_system.local"',
  // BASE_API: '"https://easy-mock.com/mock/5950a2419adc231f356a6636/vue-admin"',
})
