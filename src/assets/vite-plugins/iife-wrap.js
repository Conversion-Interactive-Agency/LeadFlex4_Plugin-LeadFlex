export default {
  name: "wrap-in-iife",
  generateBundle(outputOptions, bundle) {
    Object.keys(bundle).forEach((fileName) => {
      const file = bundle[fileName];
      if (fileName.slice(-3) === ".js" && "code" in file) {
        file.code = `(function(){${file.code}})()`;
      }
    });
  }
};