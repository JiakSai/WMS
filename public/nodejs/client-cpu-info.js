var clientCPUInfo = {
  getCPUInfo: function() {
    return navigator.hardwareConcurrency;
  }
};

// Example usage
console.log(clientCPUInfo.getCPUInfo());