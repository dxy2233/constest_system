export const debug = process.env.NODE_ENV !== 'production'
export const base = {
  // url: 'http://app.contest_system.local'
  url: debug ? 'http://app.contest_system.local' : '',
}
export const colorTheme = '#E74A2B'
export const colorSubTheme = '#FF6B3E'
