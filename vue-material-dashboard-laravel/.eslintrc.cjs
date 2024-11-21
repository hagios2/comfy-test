module.exports = {
  root: true, // Ensures this config is the root config for eslint
  env: {
    browser: true, // Specifies browser global variables like `window` and `document`
    node: true, // Specifies Node.js global variables and scope
    es2021: true, // Allows modern ECMAScript features from 2021
  },
  extends: [
    "eslint:recommended", // Basic recommended rules from ESLint
    "plugin:vue/vue3-essential", // Essential rules for Vue 3
    "@vue/prettier" // Integrates Prettier formatting (if enabled)
  ],
  parserOptions: {
    parser: "@babel/eslint-parser", // Uses Babel parser to parse modern JavaScript
    requireConfigFile: false, // Disables the requirement for a Babel config file
    ecmaVersion: 2020, // Supports modern ECMAScript features (like optional chaining)
    sourceType: "module", // Parses code as an ES module
  },
  rules: {
    "no-console": process.env.NODE_ENV === "production" ? "warn" : "off", // Warn for `console` in production
    "no-debugger": process.env.NODE_ENV === "production" ? "warn" : "off", // Warn for `debugger` in production
    "prettier/prettier": "off", // Disables Prettier formatting warnings (controlled by Prettier settings)
    "vue/multi-word-component-names": "off", // Allows single-word component names
    "vue/no-v-html": "off", // Disables warnings for using `v-html` (use with caution)
    "no-unused-vars": [
      "warn",
      {
        argsIgnorePattern: "^_", // Ignores variables starting with `_`
        varsIgnorePattern: "^_" // Ignores unused variables starting with `_`
      }
    ],
    "vue/no-unused-components": "warn", // Warns for unused Vue components
    "no-undef": "error", // Disallows usage of undeclared variables
  },
  overrides: [
    {
      files: ["**/__tests__/*.{j,t}s?(x)", "**/tests/unit/**/*.spec.{j,t}s?(x)"],
      env: {
        jest: true, // Adds Jest testing globals for unit tests
      },
    },
  ],
  globals: {
    defineProps: "readonly", // Allows `defineProps` in Vue 3 without ESLint warnings
    defineEmits: "readonly", // Allows `defineEmits` in Vue 3 without ESLint warnings
    defineExpose: "readonly", // Allows `defineExpose` in Vue 3 without ESLint warnings
    withDefaults: "readonly", // Allows `withDefaults` in Vue 3 without ESLint warnings
  },
};
