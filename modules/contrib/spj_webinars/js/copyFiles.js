const fs = require('fs-extra');
const path = require('path');

const sourceFile = path.resolve(__dirname, 'build', 'static', 'js', 'main.*.js');
const destinationFile = path.resolve(__dirname, 'custom-build', 'myapp.js');

fs.copy(sourceFile, destinationFile)
  .then(() => {
    console.log('Build file moved successfully!');
  })
  .catch((error) => {
    console.error('Error while moving build file:', error);
  });




