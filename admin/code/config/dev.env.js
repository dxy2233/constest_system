'use strict'
const merge = require('webpack-merge')
const prodEnv = require('./prod.env')

module.exports = merge(prodEnv, {
  NODE_ENV: '"development"',
  BASE_API: '"http://admin.contest_system.local"',
  // BASE_API: '"http://contestadmin.vguiren.com"',
})
