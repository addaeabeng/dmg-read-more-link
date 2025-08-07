module.exports = {
    root: true,
    env: {
      browser: true,
      es6: true,
      jest: true,
    },
    extends: [
      "plugin:@wordpress/eslint-plugin/recommended",
      "wordpress",
      "prettier",
    ],
    parserOptions: {
      ecmaVersion: 2020,
      sourceType: "module",
    },
    rules: {
      // Ensure React is handled correctly (not required in modern setups)
      "react/react-in-jsx-scope": "off",
  
      // Helpful accessibility warning
      "jsx-a11y/anchor-is-valid": "warn",
  
      // Prevent lingering console.log in production
      "no-console": "development" === process.env.NODE_ENV ? "warn" : "error",
  
      // Optional style rules for consistency
      "comma-dangle": ["error", "always-multiline"],
      indent: ["error", 2],
      quotes: ["error", "single"],
      semi: ["error", "always"],
    },
};
  