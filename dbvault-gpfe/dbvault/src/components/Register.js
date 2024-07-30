// src/components/Register.js
import React from 'react';
import { Link } from 'react-router-dom';
import './Auth.css';

const Register = () => {
  return (
    <div className="auth-wrapper">
      <div className="auth-container">
        <h1 className="title">GuardiansDB</h1>
        <h2>Register</h2>
        <form>
          <div className="form-group">
            <label>Username:</label>
            <input type="text" placeholder="Enter your username" />
          </div>
          <div className="form-group">
            <label>Email ID:</label>
            <input type="email" placeholder="Enter your email" />
          </div>
          <div className="form-group">
            <label>Password:</label>
            <input type="password" placeholder="Enter your password" />
          </div>
          <div className="form-group">
            <label>Confirm Password:</label>
            <input type="password" placeholder="Confirm your password" />
          </div>
          <button type="submit" className="auth-button">Register</button>
        </form>
        <p>
          Already have an account? <Link to="/login">Login</Link>
        </p>
      </div>
    </div>
  );
};

export default Register;
