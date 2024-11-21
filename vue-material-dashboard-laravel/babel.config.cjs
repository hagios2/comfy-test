module.exports = {
  presets: [
    [
      "@vue/cli-plugin-babel/preset",
      {
        useBuiltIns: "entry", // This tells Babel to use polyfills based on usage
        corejs: 3,            // Specifies the version of core-js to use
      },
    ],
  ],
};
