// dbvault-sbbe/index.js
const express = require('express');
const app = express();
const port = process.env.PORT || 5000;

app.use(express.json()); // To parse JSON bodies

// Sample route
app.get('/', (req, res) => {
  res.send('Backend is working!');
});

app.listen(port, () => {
  console.log(`Server is running on port ${port}`);
});
