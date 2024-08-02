// dbvault-sbbe/index.js

// Other imports and setup code
const express = require('express');
const bcrypt = require('bcrypt');
const db = require('./db');
const app = express();
const port = process.env.PORT || 5000;

app.use(express.json());

// User registration route
app.post('/register', async (req, res) => {
  const { username, email, password } = req.body;
  console.log('Received data:', { username, email, password });

  // Check if email already exists
  db.query('SELECT * FROM users WHERE email = ?', [email], async (err, results) => {
    if (err) {
      console.error('Error checking email:', err);
      return res.status(500).send('Database error');
    }
    if (results.length > 0) {
      console.log('Email already exists:', email);
      return res.status(400).send('Email already registered');
    }

    // Hash the password
    const hashedPassword = await bcrypt.hash(password, 10);

    // Insert the new user into the database
    db.query('INSERT INTO users (username, email, password) VALUES (?, ?, ?)', 
      [username, email, hashedPassword], (err, result) => {
        if (err) {
          console.error('Error inserting user:', err);
          return res.status(500).send('Database error');
        }
        console.log('User registration successful:', result);
        res.status(201).send('User registered');
    });
  });
});

// User login route
app.post('/login', async (req, res) => {
  const { email, password } = req.body;

  // Check if the user exists
  db.query('SELECT * FROM users WHERE email = ?', [email], async (err, results) => {
    if (err) throw err;
    if (results.length === 0) {
      return res.status(400).send('Email not registered');
    }

    const user = results[0];

    // Compare the password with the hashed password in the database
    const isMatch = await bcrypt.compare(password, user.password);

    if (!isMatch) {
      return res.status(400).send('Invalid password');
    }

    res.send('Login successful');
  });
});

// Start the server
app.listen(port, () => {
  console.log(`Server is running on port ${port}`);
});
