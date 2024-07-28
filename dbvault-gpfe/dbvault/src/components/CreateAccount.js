import React, { useState } from 'react';
import './styles.css';

function CreateAccount() {
  const [name, setName] = useState('');
  const [mobile, setMobile] = useState('');
  const [email, setEmail] = useState('');

  const handleCreateAccount = (e) => {
    e.preventDefault();
    // Add account creation logic here
  };

  return (
    <div className="account-container">
      <h2>Create Bank Account</h2>
      <form onSubmit={handleCreateAccount}>
        <input
          type="text"
          placeholder="Name"
          value={name}
          onChange={(e) => setName(e.target.value)}
          required
        />
        <input
          type="tel"
          placeholder="Mobile"
          value={mobile}
          onChange={(e) => setMobile(e.target.value)}
          required
        />
        <input
          type="email"
          placeholder="Email"
          value={email}
          onChange={(e) => setEmail(e.target.value)}
          required
        />
        <button type="submit">Create Account</button>
      </form>
    </div>
  );
}

export default CreateAccount;
