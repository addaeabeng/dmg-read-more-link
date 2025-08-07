module.exports = {
    __: (str) => str,
    _x: (str) => str,
    _n: (single, plural, number) => (1 === number ? single : plural),
    _nx: (single, plural, number) => (1 === number ? single : plural),
    sprintf: (str) => str,
  };
  