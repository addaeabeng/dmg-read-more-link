// Extend Jest's expect with jest-dom matchers
require("@testing-library/jest-dom");

// Optional: reset fetch mocks before each test
beforeEach(() => {
  global.fetch = undefined;
});
