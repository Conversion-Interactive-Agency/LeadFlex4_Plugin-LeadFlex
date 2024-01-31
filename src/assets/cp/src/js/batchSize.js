
let increaseBatchSize = function(limit = 1000) {
  Craft.BaseElementIndex.defaults.batchSize = limit; // eslint-disable-line no-undef
};

increaseBatchSize(1000);
